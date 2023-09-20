<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Model\Invoice;

use Magento\Framework\Model\AbstractModel;
use Vendiro\Connect\Api\Invoice\DataInterface as InvoiceInterface;
use Vendiro\Connect\Model\Invoice\ResourceModel as InvoiceResource;

/**
 * Class Invoice
 */
class Data extends AbstractModel implements InvoiceInterface
{

    /**
     * @return int
     */
    public function getInvoiceId(): int
    {
        return (int)$this->getData(self::INVOICE_ID);
    }

    /**
     * @param $value
     *
     * @return InvoiceInterface
     */
    public function setInvoiceId($value): InvoiceInterface
    {
        return $this->setData(self::INVOICE_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getOrderId(): int
    {
        return (int)$this->getData(self::ORDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setOrderId($value): InvoiceInterface
    {
        return $this->setData(self::ORDER_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMarketplaceId(): int
    {
        return (int)$this->getData(self::MARKETPLACE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setMarketplaceId($value): InvoiceInterface
    {
        return $this->setData(self::MARKETPLACE_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMarketplaceOrderId(): string
    {
        return $this->getData(self::MARKETPLACE_ORDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setMarketplaceOrderId($value): InvoiceInterface
    {
        return $this->setData(self::MARKETPLACE_ORDER_ID, $value);
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(InvoiceResource::class);
    }
}
