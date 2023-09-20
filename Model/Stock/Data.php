<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Model\Stock;

use Magento\Framework\Model\AbstractModel;
use Vendiro\Connect\Api\Stock\DataInterface as StockInterface;

/**
 * Class Stock
 */
class Data extends AbstractModel implements StockInterface
{

    /**
     * @inheritDoc
     */
    public function getProductUpdatedAt(): string
    {
        return (string)$this->getData(self::PRODUCT_UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setProductUpdatedAt(string $productUpdatedAt): StockInterface
    {
        return $this->setData($productUpdatedAt, self::PRODUCT_UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt(): string
    {
        return (string)$this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($createdAt): StockInterface
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt(): string
    {
        return (string)$this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt($updatedAt): StockInterface
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * @inheritDoc
     */
    public function getDeleted(): int
    {
        return (int)$this->getData(self::DELETED);
    }

    /**
     * @inheritDoc
     */
    public function setDeleted(int $deleted): StockInterface
    {
        return $this->setData(self::DELETED, $deleted);
    }

    /**
     * @inheritDoc
     */
    public function getNeedsUpdate(): int
    {
        return (int)$this->getData(self::NEEDS_UPDATE);
    }

    /**
     * @inheritDoc
     */
    public function setNeedsUpdate(int $needsUpdate): StockInterface
    {
        return $this->setData(self::NEEDS_UPDATE, $needsUpdate);
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * @inheritDoc
     */
    public function getProductSku(): string
    {
        return (string)$this->getData(self::PRODUCT_SKU);
    }

    /**
     * @inheritDoc
     */
    public function setProductSku(string $sku): StockInterface
    {
        return $this->setData(self::PRODUCT_SKU, $sku);
    }

    /**
     * @inheritDoc
     */
    public function getSyncedAt(): ?string
    {
        return $this->getData(self::SYNCED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setSyncedAt(string $syncedAt): StockInterface
    {
        return $this->setData(self::SYNCED_AT, $syncedAt);
    }
}
