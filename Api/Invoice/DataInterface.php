<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Api\Invoice;

/**
 * Interface for invoice data
 * @api
 */
interface DataInterface
{
    /**
     * Constants for keys of data array.
     */
    public const INVOICE_ID = 'invoice_id';
    public const ORDER_ID = 'order_id';
    public const MARKETPLACE_ID = 'marketplace_id';
    public const MARKETPLACE_ORDER_ID = 'marketplace_order_id';

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
    public function getOrderId(): int;

    /**
     * @param $value
     *
     * @return self
     */
    public function setOrderId($value): self;

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
    public function getMarketplaceOrderId(): string;

    /**
     * @param $value
     *
     * @return self
     */
    public function setMarketplaceOrderId($value): self;
}
