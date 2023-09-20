<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Model\Order;

use Magento\Framework\Model\AbstractModel;
use Vendiro\Connect\Api\Order\DataInterface as OrderInterface;

class Data extends AbstractModel implements OrderInterface
{

    /**
     * @return string
     */
    public function getOrderId(): string
    {
        return (string)$this->getData(self::ORDER_ID);
    }

    /**
     * @param $value
     *
     * @return OrderInterface
     */
    public function setOrderId($value): OrderInterface
    {
        return $this->setData(self::ORDER_ID, $value);
    }

    /**
     * @return int
     */
    public function getOrderEntityId(): int
    {
        return (int)$this->getData(self::ORDER_ENTITY_ID);
    }

    /**
     * @param $value
     *
     * @return OrderInterface
     */
    public function setOrderEntityId($value): OrderInterface
    {
        return $this->setData(self::ORDER_ENTITY_ID, $value);
    }

    /**
     * @return int
     */
    public function getVendiroId(): int
    {
        return (int)$this->getData(self::VENDIRO_ID);
    }

    /**
     * @param $value
     *
     * @return OrderInterface
     */
    public function setVendiroId($value): OrderInterface
    {
        return $this->setData(self::VENDIRO_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMarketplaceOrderId(): string
    {
        return (string)$this->getData(self::MARKETPLACE_ORDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setMarketplaceOrderId($value): OrderInterface
    {
        return $this->setData(self::MARKETPLACE_ORDER_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMarketplaceName(): string
    {
        return (string)$this->getData(self::MARKETPLACE_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setMarketplaceName($value): OrderInterface
    {
        return $this->setData(self::MARKETPLACE_NAME, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMarketplaceReference(): string
    {
        return (string)$this->getData(self::MARKETPLACE_REFERENCE);
    }

    /**
     * @inheritDoc
     */
    public function setMarketplaceReference($value): OrderInterface
    {
        return $this->setData(self::MARKETPLACE_REFERENCE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): string
    {
        return (string)$this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($value): OrderInterface
    {
        return $this->setData(self::STATUS, $value);
    }

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
     * @return OrderInterface
     */
    public function setInvoiceId($value): OrderInterface
    {
        return $this->setData(self::INVOICE_ID, $value);
    }

    /**
     * @return int
     */
    public function getMarketplaceId(): int
    {
        return (int)$this->getData(self::MARKETPLACE_ID);
    }

    /**
     * @param $value
     *
     * @return OrderInterface
     */
    public function setMarketplaceId($value): OrderInterface
    {
        return $this->setData(self::MARKETPLACE_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getImportedAt(): string
    {
        return (string)$this->getData(self::IMPORTED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setImportedAt($value): OrderInterface
    {
        return $this->setData(self::IMPORTED_AT, $value);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}
