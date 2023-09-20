<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Api\Carrier;

/**
 * Interface for carrier data
 * @api
 */
interface DataInterface
{
    /**
     * Constants for keys of data array.
     */
    public const CARRIER_ID = 'carrier_id';
    public const CARRIER = 'carrier';

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $entityId
     * @return self
     */
    public function setEntityId($entityId);

    /**
     * @return int
     */
    public function getCarrierId(): int;

    /**
     * @param int $value
     * @return self
     */
    public function setCarrierId(int $value): self;

    /**
     * @return string
     */
    public function getCarrier(): string;

    /**
     * @param string $value
     * @return self
     */
    public function setCarrier(string $value): self;
}
