<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Api\Order;

/**
 * Interface for Order data
 * @api
 */
interface DataInterface
{
    /**
     * Constants for keys of data array.
     */
    public const ORDER_ID = 'order_id';
    public const ORDER_ENTITY_ID = 'order_entity_id';
    public const VENDIRO_ID = 'vendiro_id';
    public const MARKETPLACE_ORDER_ID = 'marketplace_order_id';
    public const MARKETPLACE_NAME = 'marketplace_name';
    public const MARKETPLACE_REFERENCE = 'marketplace_reference';
    public const STATUS = 'status';
    public const INVOICE_ID = 'invoice_id';
    public const MARKETPLACE_ID = 'marketplace_id';
    public const IMPORTED_AT = 'imported_at';

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
    public function getOrderId(): string;

    /**
     * @param $value
     *
     * @return self
     */
    public function setOrderId($value): self;

    /**
     * @return int
     */
    public function getOrderEntityId(): int;

    /**
     * @param $value
     *
     * @return self
     */
    public function setOrderEntityId($value): self;

    /**
     * @return int
     */
    public function getVendiroId(): int;

    /**
     * @param $value
     *
     * @return self
     */
    public function setVendiroId($value): self;

    /**
     * @return string
     */
    public function getMarketplaceOrderId(): string;

    /**
     * @param $value
     *
     * @return self
     */
    public function setMarketplaceOrderId($value): self;

    /**
     * @return string
     */
    public function getMarketplaceName(): string;

    /**
     * @param $value
     *
     * @return self
     */
    public function setMarketplaceName($value): self;

    /**
     * @return string
     */
    public function getMarketplaceReference(): string;

    /**
     * @param $value
     *
     * @return self
     */
    public function setMarketplaceReference($value): self;

    /**
     * @return string
     */
    public function getStatus(): string;

    /**
     * @param $value
     *
     * @return self
     */
    public function setStatus($value): self;

    /**
     * @return int
     */
    public function getInvoiceId(): int;

    /**
     * @param $value
     *
     * @return self
     */
    public function setInvoiceId($value): self;

    /**
     * @return int
     */
    public function getMarketplaceId(): int;

    /**
     * @param $value
     *
     * @return self
     */
    public function setMarketplaceId($value): self;

    /**
     * @return string
     */
    public function getImportedAt(): string;

    /**
     * @param $value
     *
     * @return self
     */
    public function setImportedAt($value): self;
}
