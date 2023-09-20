<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Model\Totals\Invoice;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

class Discount extends AbstractTotal
{
    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        parent::__construct($data);
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @param Invoice $invoice
     *
     * @return $this
     */
    public function collect(Invoice $invoice): self
    {
        if ($this->hasNoVendiroDiscount($invoice)) {
            return $this;
        }

        $baseDiscount = $this->getBaseDiscount($invoice);
        $this->applyDiscount($baseDiscount, $invoice);

        return $this;
    }

    /**
     * @param Invoice $invoice
     *
     * @return bool
     */
    private function hasNoVendiroDiscount(Invoice $invoice): bool
    {
        return $invoice->getVendiroDiscount() == 0;
    }

    /**
     * @param Invoice $invoice
     *
     * @return float|int
     */
    private function getBaseDiscount(Invoice $invoice)
    {
        return abs((float)$invoice->getVendiroDiscount()) * -1;
    }

    /**
     * @param int|float $baseDiscount
     * @param Invoice $invoice
     */
    private function applyDiscount($baseDiscount, Invoice $invoice)
    {
        $discount = $this->priceCurrency->convert($baseDiscount);

        $invoice->setDiscountAmount($invoice->getDiscountAmount() + $discount);
        $invoice->setBaseDiscountAmount($invoice->getBaseDiscountAmount() + $baseDiscount);

        $grandTotal = $invoice->getGrandTotal() + $discount;
        $baseGrandTotal = $invoice->getBaseGrandTotal() + $baseDiscount;

        $invoice->setGrandTotal($grandTotal < 0.0001 ? 0 : $grandTotal);
        $invoice->setBaseGrandTotal($baseGrandTotal < 0.0001 ? 0 : $baseGrandTotal);
    }
}
