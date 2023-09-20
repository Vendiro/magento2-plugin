<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Service\Order\Create;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\StoreManagerInterface;
use Vendiro\Connect\Api\Log\RepositoryInterface as LogRepository;
use Vendiro\Connect\Exception as VendiroException;

class CartManager
{

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var CartManagementInterface
     */
    private $cartManagement;
    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;
    /**
     * @var LogRepository
     */
    private $logger;
    /**
     * @var CartInterface|Quote
     */
    private $cart;

    /**
     * @param StoreManagerInterface $storeManager
     * @param CartManagementInterface $cartManagement
     * @param CartRepositoryInterface $cartRepository
     * @param LogRepository $logger
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        CartManagementInterface $cartManagement,
        CartRepositoryInterface $cartRepository,
        LogRepository $logger
    ) {
        $this->storeManager = $storeManager;
        $this->cartManagement = $cartManagement;
        $this->cartRepository = $cartRepository;
        $this->logger = $logger;
    }

    /**
     * @param string $storeCode
     *
     * @return CartInterface
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     */
    public function createCart(string $storeCode = 'default')
    {
        $store = $this->storeManager->getStore($storeCode);
        $this->storeManager->setCurrentStore($store->getId());

        $cartId = $this->cartManagement->createEmptyCart();
        $this->cart = $this->cartRepository->get($cartId);
        $this->cart->setStoreId($store->getId());
        $this->cart->setCurrency();
        $this->cart->setCheckoutMethod(CartManagementInterface::METHOD_GUEST);

        return $this->cart;
    }

    /**
     * @param ProductInterface $product
     * @param int $quantity
     * @throws LocalizedException
     */
    public function addProduct(ProductInterface $product, int $quantity)
    {
        $this->cart->addProduct($product, $quantity);
        $quoteItem = $this->cart->getItemByProduct($product);
        $quoteItem->setNoDiscount(1);
    }

    /**
     * @param array $data
     * @param string $type
     */
    public function addAddress(array $data, string $type = 'Billing')
    {
        $addressMethod = 'get' . ucfirst($type) . 'Address';
        $formattedAddress = $this->formatAddress($data);
        $cartAddress = $this->cart->$addressMethod();
        $cartAddress->addData($formattedAddress);
    }

    /**
     * @param array $address
     *
     * @return array
     */
    private function formatAddress(array $address)
    {
        return [
            'firstname' => $address['name'],
            'lastname' => $address['lastname'],
            'company' => $address['name2'],
            'street' => [0 => $address['street'], 1 => $address['street2']],
            'city' => $address['city'],
            'country_id' => $address['country'],
            'postcode' => $address['postalcode'],
            'telephone' => $address['phone'],
            'email' => $address['email'],
            'vat_id' => $address['vat_number']
        ];
    }

    /**
     * @param string $method
     * @param string|int|float $shippingCost
     */
    public function setShippingMethod(string $method, $shippingCost)
    {
        $this->cart->setVendiroShippingCost($shippingCost);
        $cartShippingAddress = $this->cart->getShippingAddress();
        $cartShippingAddress->setCollectShippingRates(true);
        $cartShippingAddress->collectShippingRates();
        $cartShippingAddress->setShippingMethod($method);
    }

    /**
     * @param string $method
     */
    public function setPaymentMethod(string $method)
    {
        $this->cart->setPaymentMethod($method);
        $this->cart->setInventoryProcessed(false);
        $payment = $this->cart->getPayment();

        try {
            $payment->importData(['method' => $method]);
        } catch (LocalizedException $exception) {
            $this->logger->addErrorLog(
                'Vendiro import set payment method went wrong: ' . $exception->getMessage(),
                ['payment_method' => $method]
            );
        }
    }

    /**
     * @param array $vendiroOrder
     *
     * @return int
     * @throws VendiroException
     */
    public function placeOrder(array $vendiroOrder): int
    {
        $this->cart->collectTotals();
        $this->cartRepository->save($this->cart);

        try {
            $this->cart = $this->cartRepository->get($this->cart->getId());
            $this->setCartCurrency($vendiroOrder['marketplace']['reference']);
            $this->cart->setVendiroDiscount($vendiroOrder['discount']);
            return (int)$this->cartManagement->placeOrder($this->cart->getId());
        } catch (LocalizedException $exception) {
            $this->logger->addErrorLog('placeOrder', $exception->getMessage());
            throw new VendiroException(__($exception->getMessage()));
        }
    }

    /**
     * @param $storeCode
     *
     * @throws NoSuchEntityException
     */
    public function setCartCurrency($storeCode)
    {
        $store = $this->storeManager->getStore($storeCode);
        $currency = $store->getDefaultCurrencyCode();

        $this->cart->setQuoteCurrencyCode($currency);
        $this->cart->setStoreCurrencyCode($currency);
        $this->cart->setBaseCurrencyCode($currency);
    }
}
