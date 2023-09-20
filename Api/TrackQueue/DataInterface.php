<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Api\TrackQueue;

/**
 * Interface for Track Queue data
 * @api
 */
interface DataInterface
{
    /**
     * Constants for keys of data array.
     */
    public const TRACK_ID = 'track_id';
    public const STATUS = 'status';
    public const CREATED_AT = 'created_at';

    /**
     * Constants for keys of joined fields
     */
    public const SHIPMENT_CODE = 'shipment_code';
    public const ORDER_INCREMENT_ID = 'order_increment_id';
    public const CARRIER_NAME = 'carrier_name';

    /**
     * @return int
     */
    public function getTrackId(): int;

    /**
     * @param $value
     *
     * @return self
     */
    public function setTrackId($value): self;

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
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * @param $value
     *
     * @return self
     */
    public function setCreatedAt($value): self;

    /**
     * @return string
     */
    public function getShipmentCode(): string;

    /**
     * @return string
     */
    public function getOrderIncrementId(): string;

    /**
     * @return string
     */
    public function getCarrierName(): string;
}
