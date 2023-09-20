<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Service\Order;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Tax\Model\Calculation as TaxCalculation;
use Vendiro\Connect\Api\Config\RepositoryInterface as ConfigProvider;
use Vendiro\Connect\Api\Log\RepositoryInterface as LogRepository;
use Vendiro\Connect\Exception as VendiroException;
use Vendiro\Connect\Model\Carrier\Vendiro as VendiroCarrier;
use Vendiro\Connect\Model\Payment\Vendiro as VendiroPayment;
use Vendiro\Connect\Service\Order\Create\CartManager;
use Vendiro\Connect\Service\Session\Manager as SessionManager;

class Create
{
    public const STORE_NOT_FOUND = 'Store with store-code %s not found';
    public const ORDER_NOTE = 'Order via Vendiro<br/>Marketplace: %s<br/>%s ID: %s';

    /**
     * @var CartManager
     */
    private $cart;
    /**
     * @var OrderStatusManager
     */
    private $orderStatusManager;
    /**
     * @var Product
     */
    private $product;
    /**
     * @var LogRepository
     */
    private $logger;
    /**
     * @var SessionManager
     */
    private $sessionManager;
    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var ConfigProvider
     */
    private $configProvider;
    /**
     * @var TaxCalculation
     */
    private $taxCalculation;

    /**
     * @param CartManager $cart
     * @param OrderStatusManager $orderStatusManager
     * @param Product $product
     * @param StoreRepositoryInterface $storeRepository
     * @param OrderRepositoryInterface $orderRepository
     * @param ConfigProvider $configProvider
     * @param TaxCalculation $taxCalculation
     * @param SessionManager $sessionManager
     * @param LogRepository $logger
     */
    public function __construct(
        CartManager $cart,
        OrderStatusManager $orderStatusManager,
        Product $product,
        StoreRepositoryInterface $storeRepository,
        OrderRepositoryInterface $orderRepository,
        ConfigProvider $configProvider,
        TaxCalculation $taxCalculation,
        SessionManager $sessionManager,
        LogRepository $logger
    ) {
        $this->cart = $cart;
        $this->orderStatusManager = $orderStatusManager;
        $this->orderRepository = $orderRepository;
        $this->product = $product;
        $this->configProvider = $configProvider;
        $this->storeRepository = $storeRepository;
        $this->taxCalculation = $taxCalculation;
        $this->sessionManager = $sessionManager;
        $this->logger = $logger;
    }

    /**
     * @param array $vendiroOrder
     * @return ?string
     * @throws VendiroException
     * @throws LocalizedException
     */
    public function execute(array $vendiroOrder): ?string
    {
        $store = $this->getStoreFromOrder($vendiroOrder);

        try {
            $this->sessionManager->setIsVendiroOrder();
            $createdCart = $this->cart->createCart($store->getCode());

            foreach ($vendiroOrder['orderlines'] as $apiProduct) {
                $this->addProducts($apiProduct, $createdCart, $store);
            }

            $this->addAddresses($vendiroOrder['invoice_address'], $vendiroOrder['delivery_address']);
            $shippingCost = $this->getShippingPrice($vendiroOrder, $store, $createdCart);
            $this->setMethods($shippingCost);
            $this->setPaymentData($createdCart, $vendiroOrder);

            if ($newOrderId = $this->prepareAndPlaceOrder($vendiroOrder)) {
                $order = $this->orderRepository->get($newOrderId);
                $this->updateOrderCommentAndStatus($order, $vendiroOrder);
                $this->orderStatusManager->createInvoice($order, $vendiroOrder);
                $this->orderStatusManager->createShipment($order, $vendiroOrder);
                return (string)$order->getIncrementId();
            }

            return null;
        } catch (\Exception $exception) {
            throw new VendiroException(__($exception->getMessage()));
        } finally {
            $this->sessionManager->unsIsVendiroOrder();
        }
    }

    /**
     * @param array $vendiroOrder
     * @return StoreInterface
     * @throws VendiroException
     */
    private function getStoreFromOrder(array $vendiroOrder): StoreInterface
    {
        try {
            $storeCode = $vendiroOrder['marketplace']['reference'];
            return $this->storeRepository->get($storeCode);
        } catch (NoSuchEntityException $e) {
            $errorMsg = sprintf(self::STORE_NOT_FOUND, $storeCode);
            throw new VendiroException(__($errorMsg));
        }
    }

    /**
     * @param CartInterface $createdCart
     * @param array $apiProduct
     * @param StoreInterface $store
     *
     * @throws LocalizedException
     * @throws VendiroException
     */
    private function addProducts(array $apiProduct, CartInterface $createdCart, StoreInterface $store)
    {
        $product = $this->product->getBySku($apiProduct['sku'], (int)$store->getId());
        $this->cart->addProduct($product, (int)$apiProduct['amount']);
        $price = $this->getProductPrice($apiProduct, $product, $store, $createdCart);

        $item = $createdCart->getItemByProduct($product);
        $item = $item->getParentItem() ?: $item;
        $item->setCustomPrice($price);
        $item->setOriginalCustomPrice($price);
        $item->getProduct()->setIsSuperMode(true);
    }

    /**
     * @param array $apiProduct
     * @param ProductInterface $product
     * @param StoreInterface $store
     * @param CartInterface $createdCart
     * @return float
     */
    private function getProductPrice(
        array $apiProduct,
        ProductInterface $product,
        StoreInterface $store,
        CartInterface $createdCart
    ): float {
        if ($this->configProvider->getPriceIncludedTax((int)$store->getId())) {
            return (float)$apiProduct['value'];
        }

        $request = $this->taxCalculation->getRateRequest(
            $createdCart->getShippingAddress(),
            $createdCart->getBillingAddress(),
            $this->configProvider->getDefaultCustomerTaxClass((int)$store->getId()),
            $store
        );

        $percent = $this->taxCalculation->getRate(
            $request->setData('product_class_id', $product->getData('tax_class_id'))
        );

        return (float)$apiProduct['value'] / (100 + $percent) * 100;
    }

    /**
     * @param array $billingAddress
     * @param array $shippingAddress
     */
    private function addAddresses(array $billingAddress, array $shippingAddress)
    {
        $this->cart->addAddress($billingAddress, 'Billing');
        $this->cart->addAddress($shippingAddress, 'Shipping');
    }

    /**
     * @param array $vendiroOrder
     * @param StoreInterface $store
     * @param CartInterface $createdCart
     * @return float|int|mixed
     */
    private function getShippingPrice(
        array $vendiroOrder,
        StoreInterface $store,
        CartInterface $createdCart
    ): float {
        $shippingCost = (float)$vendiroOrder['shipping_cost'] + (float)$vendiroOrder['administration_cost'];
        if ($this->configProvider->getShippingIncludedTax((int)$store->getId()) != 0) {
            return $shippingCost;
        }

        $request = $this->taxCalculation->getRateRequest(
            $createdCart->getShippingAddress(),
            $createdCart->getBillingAddress(),
            $this->configProvider->getDefaultCustomerTaxClass((int)$store->getId()),
            $store
        );

        $taxRateId = $this->configProvider->getShippingTaxClass((int)$store->getId());
        $percent = $this->taxCalculation->getRate($request->setData('product_tax_class_id', $taxRateId));
        return ($shippingCost / (100 + $percent) * 100);
    }

    /**
     * @param float $shippingCost
     */
    private function setMethods(float $shippingCost)
    {
        $this->cart->setShippingMethod(VendiroCarrier::SHIPPING_CARRIER_METHOD, $shippingCost);
        $this->cart->setPaymentMethod(VendiroPayment::PAYMENT_CODE);
    }

    /**
     * @param CartInterface $createdCart
     * @param array $vendiroOrder
     * @return void
     * @throws LocalizedException
     */
    private function setPaymentData(CartInterface $createdCart, array $vendiroOrder)
    {
        $createdCart->getPayment()->setAdditionalInformation([
            'marketplace_name' => $vendiroOrder['marketplace']['name'] ?? null,
            'marketplace_order_id' => $vendiroOrder['marketplace_order_id'] ?? null,
            'marketplace_reference' => $vendiroOrder['reference'] ?? null,
            'marketplace_id' => $vendiroOrder['marketplace']['id'] ?? null,
            'fulfilment_by_marketplace' => $vendiroOrder['fulfilment_by_marketplace'] ?? false,
        ]);
    }

    /**
     * @param array $vendiroOrder
     *
     * @return int
     * @throws VendiroException
     */
    private function prepareAndPlaceOrder(array $vendiroOrder): int
    {
        try {
            return $this->placeOrder($vendiroOrder);
        } catch (\Exception $e) {
            $exceptionMessage = __($e->getMessage() . ' [Ref: %1]', $vendiroOrder['marketplace_order_id']);
            $this->logger->addErrorLog('prepareAndPlaceOrder', $exceptionMessage);
            throw new VendiroException($exceptionMessage);
        }
    }

    /**
     * @param array $vendiroOrder
     *
     * @return int
     * @throws VendiroException
     */
    private function placeOrder(array $vendiroOrder): int
    {
        try {
            return $this->cart->placeOrder($vendiroOrder);
        } catch (\Exception $exception) {
            throw new VendiroException(__($exception->getMessage()));
        }
    }

    /**
     * @param OrderInterface $order
     * @param array $vendiroOrder
     * @return void
     */
    private function updateOrderCommentAndStatus(OrderInterface $order, array $vendiroOrder)
    {
        $comment = sprintf(
            self::ORDER_NOTE,
            $vendiroOrder['marketplace']['name'],
            $vendiroOrder['marketplace']['name'],
            $vendiroOrder['marketplace_order_id']
        );

        if (isset($vendiroOrder['fulfilment_by_marketplace']) && $vendiroOrder['fulfilment_by_marketplace'] == 'true') {
            $comment .= "<br/>Fulfilment by Marketplace";
        }

        $this->orderStatusManager->createComment($order, $comment);
    }
}
