<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Api\Stock;

/**
 * Interface for Stock data
 * @api
 */
interface DataInterface
{

    /**
     * Constants for keys of data array.
     */
    public const ENTITY_ID = 'entity_id';
    public const PRODUCT_SKU = 'product_sku';
    public const SYNCED_AT = 'synced_at';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const DELETED = 'deleted_at';
    public const NEEDS_UPDATE = 'needs_update';
    public const PRODUCT_UPDATED_AT = 'product_updated_at';
      /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $entityId
     *
     * @return self
     */
    public function setEntityId($entityId);

    /**
     * @return string
     */
    public function getProductSku(): string;

    /**
     * @param string $sku
     *
     * @return self
     */
    public function setProductSku(string $sku): self;

    /**
     * @return int
     */
    public function getDeleted(): int;

    /**
     * @param int $deleted
     *
     * @return self
     */
    public function setDeleted(int $deleted): self;

    /**
     * Get updated time
     *
     * @return string
     */
    public function getProductUpdatedAt(): string;

    /**
     * Set updated time
     *
     * @param string $productUpdatedAt
     *
     * @return $this
     */
    public function setProductUpdatedAt(string $productUpdatedAt): self;

    /**
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * @param string $createdAt
     *
     * @return self
     */
    public function setCreatedAt(string $createdAt): self;

    /**
     * @return string
     */
    public function getSyncedAt(): ?string;

    /**
     * @param string $syncedAt
     *
     * @return self
     */
    public function setSyncedAt(string $syncedAt): self;

    /**
     * @return string
     */
    public function getUpdatedAt(): string;

    /**
     * @param string $updatedAt
     *
     * @return self
     */
    public function setUpdatedAt(string $updatedAt): self;

    /**
     * @return int
     */
    public function getNeedsUpdate(): int;

    /**
     * @param int $needsUpdate
     *
     * @return self
     */
    public function setNeedsUpdate(int $needsUpdate): self;
}
