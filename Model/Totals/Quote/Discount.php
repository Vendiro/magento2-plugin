<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Model\Totals\Quote;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\SalesRule\Model\DeltaPriceRound;

class Discount extends AbstractTotal
{
    public const VENDIRO_DISCOUNT_LABEL = 'Total';
    public const DELTA_ROUND_TYPE = 'vendiro';
    public const DELTA_ROUND_BASE_TYPE = 'vendiro_base';

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var DeltaPriceRound
     */
    private $deltaPriceRound;

    /**
     * @param PriceCurrencyInterface $priceCurrency
     *
     * Operations shouldn't be allowed in constructors, and thus the setCode() shouldn't be set here.
     * However, Magento Total classes work in a way that this is necessary, even Magento itself does this.
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        DeltaPriceRound $deltaPriceRound
    ) {
        $this->setCode('vendirodiscount');

        $this->priceCurrency = $priceCurrency;
        $this->deltaPriceRound = $deltaPriceRound;
    }

    /**
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     *
     * @return $this|AbstractTotal
     */
    public function collect(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Total $total)
    {
        parent::collect($quote, $shippingAssignment, $total);

        $items = $quote->getAllVisibleItems();
        if (!$items || count($items) === 0) {
            return $this;
        }

        // there's no need to keep going if no discount has been set
        if ($this->hasNoVendiroDiscount($quote)) {
            return $this;
        }

        $baseDiscount = $this->getBaseDiscount($quote, $total);
        $this->applyDiscount($baseDiscount, $quote, $total);

        $label = $total->getDiscountDescription() ? $total->getDiscountDescription() . ', ' : '';
        $total->setDiscountDescription($label . self::VENDIRO_DISCOUNT_LABEL);

        return $this;
    }

    /**
     * @param Quote $quote
     *
     * @return bool
     */
    private function hasNoVendiroDiscount($quote)
    {
        $baseDiscount = $quote->getVendiroDiscount();

        return ($baseDiscount === null || $baseDiscount == 0);
    }

    /**
     * @param Quote $quote
     * @param Total $total
     *
     * @return float|int
     */
    private function getBaseDiscount($quote, $total)
    {
        $baseDiscount = $quote->getVendiroDiscount();

        // Make sure the discount is consistently a negative number to avoid incorrect calculations
        if ($baseDiscount > 0) {
            $baseDiscount = $baseDiscount * -1;
        }

        return $baseDiscount + $total->getBaseDiscountAmount();
    }

    private function applyDiscount($baseDiscount, $quote, $total)
    {
        //only calculate discount when there is an amount
        if (!$total->getTotalAmount('subtotal')) {
            return;
        }

        $discount = $this->priceCurrency->convert($baseDiscount);

        // Visible items are the items shown in cart, we apply our discount on those "total" rows
        $items = $quote->getAllVisibleItems();
        if (!$items || count($items) === 0) {
            return;
        }

        $totalPrice = $this->getTotalPrice($items);
        foreach ($items as $item) {
            $itemPrice = $this->getItemPrice($item);
            $itemQty = $item->getTotalQty();
            $ratio = $itemPrice * $itemQty / $totalPrice;

            $amount = $this->deltaPriceRound->round($discount * $ratio, self::DELTA_ROUND_TYPE);
            $amountBase = $this->deltaPriceRound->round($baseDiscount * $ratio, self::DELTA_ROUND_BASE_TYPE);

            $item->setDiscountAmount(-$amount);
            $item->setBaseDiscountAmount(-$amountBase);
        }

        $total->setDiscountAmount($discount);
        $total->setBaseDiscountAmount($baseDiscount);

        $total->setSubtotalWithDiscount($total->getSubtotal() + $discount);
        $total->setBaseSubtotalWithDiscount($total->getBaseSubtotal() + $baseDiscount);

        $total->addTotalAmount($this->getCode(), $discount);
        $total->addBaseTotalAmount($this->getCode(), $baseDiscount);
    }

    /**
     * @param $items
     *
     * @return float|int
     */
    private function getTotalPrice($items)
    {
        $totalAmount = 0;
        foreach ($items as $item) {
            $itemPrice = $this->getItemPrice($item);
            $itemQty = $item->getTotalQty();

            $totalAmount += $itemPrice * $itemQty;
        }
        return $totalAmount;
    }

    /**
     * Return item base price
     * @param AbstractItem $item
     * @return float
     */
    private function getItemPrice(AbstractItem $item): float
    {
        $price = $item->getDiscountCalculationPrice();
        return $price !== null ? $price : $item->getCalculationPrice();
    }

    /**
     * @param Quote $quote
     * @param Total $total
     *
     * @return array
     */
    public function fetch(Quote $quote, Total $total): ?array
    {
        $result = null;
        $amount = $total->getTotalAmount($this->getCode());

        if ($amount != 0) {
            $description = $total->getDiscountDescription();
            $result = [
                'code' => $this->getCode(),
                'title' => strlen($description) ? __('Discount (%1)', $description) : __('Discount'),
                'value' => $amount
            ];
        }

        return $result;
    }
}
