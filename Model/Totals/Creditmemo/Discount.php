<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Model\Totals\Creditmemo;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;
use Vendiro\Connect\Api\Order\RepositoryInterface as OrderRepositoryInterface;

class Discount extends AbstractTotal
{
    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param PriceCurrencyInterface $priceCurrency
     * @param OrderRepositoryInterface $orderRepository
     * @param array $data
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        OrderRepositoryInterface $orderRepository,
        array $data = []
    ) {
        parent::__construct($data);

        $this->priceCurrency = $priceCurrency;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param Creditmemo $creditmemo
     * @return $this
     */
    public function collect(Creditmemo $creditmemo): self
    {
        $order = $creditmemo->getOrder();
        if (!$this->validVendiroOrder($order)) {
            return $this;
        }

        $baseDiscount = $this->getBaseDiscount($order);
        $discount = $this->priceCurrency->convert($baseDiscount);
        $creditmemo->setDiscountAmount($creditmemo->getDiscountAmount() + $discount);
        $creditmemo->setBaseDiscountAmount($creditmemo->getBaseDiscountAmount() + $baseDiscount);

        $grandTotal = $creditmemo->getGrandTotal() + $discount;
        $baseGrandTotal = $creditmemo->getBaseGrandTotal() + $baseDiscount;
        $creditmemo->setGrandTotal($grandTotal);
        $creditmemo->setBaseGrandTotal($baseGrandTotal);

        return $this;
    }

    /**
     * @param Order $order
     *
     * @return bool
     */
    private function validVendiroOrder(Order $order): bool
    {
        if (strpos($order->getDiscountDescription() ?? '', "Vendiro") === false) {
            return false;
        }

        $vendiroOrder = $this->orderRepository->getByDataSet(['order_id' => $order->getIncrementId()], true);
        if (!$vendiroOrder->getEntityId()) {
            return false;
        }

        $baseDiscount = $order->getBaseDiscountAmount();
        if ($baseDiscount == 0) {
            return false;
        }

        return true;
    }

    /**
     * @param Order $order
     *
     * @return float|int
     */
    private function getBaseDiscount(Order $order)
    {
        return abs((float)$order->getBaseDiscountAmount()) * -1;
    }
}
