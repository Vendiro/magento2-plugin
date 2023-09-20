<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Model\TrackQueue;

use Magento\Framework\Model\AbstractModel;
use Vendiro\Connect\Api\TrackQueue\DataInterface as TrackQueueInterface;
use Vendiro\Connect\Model\TrackQueue\ResourceModel as TrackQueueResource;

/**
 * Class TrackQueue
 */
class Data extends AbstractModel implements TrackQueueInterface
{

    /**
     * @return int
     */
    public function getTrackId(): int
    {
        return (int)$this->getData(self::TRACK_ID);
    }

    /**
     * @param $value
     *
     * @return TrackQueueInterface
     */
    public function setTrackId($value): TrackQueueInterface
    {
        return $this->setData(self::TRACK_ID, $value);
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return (string)$this->getData(self::STATUS);
    }

    /**
     * @param $value
     *
     * @return TrackQueueInterface
     */
    public function setStatus($value): TrackQueueInterface
    {
        return $this->setData(self::STATUS, $value);
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return (string)$this->getData(self::CREATED_AT);
    }

    /**
     * @param $value
     *
     * @return TrackQueueInterface
     */
    public function setCreatedAt($value): TrackQueueInterface
    {
        return $this->setData(self::CREATED_AT, $value);
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(TrackQueueResource::class);
    }

    /**
     * @inheritDoc
     */
    public function getShipmentCode(): string
    {
        return (string)$this->getData(self::SHIPMENT_CODE);
    }

    /**
     * @inheritDoc
     */
    public function getOrderIncrementId(): string
    {
        return (string)$this->getData(self::ORDER_INCREMENT_ID);
    }

    /**
     * @inheritDoc
     */
    public function getCarrierName(): string
    {
        return (string)$this->getData(self::CARRIER_NAME);
    }
}
