<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Model\Carrier;

use Magento\Framework\Model\AbstractModel;
use Vendiro\Connect\Api\Carrier\DataInterface as CarrierInterface;
use Vendiro\Connect\Model\Carrier\ResourceModel as CarrierResource;

/**
 * Carrier DataModel
 */
class Data extends AbstractModel implements CarrierInterface
{

    /**
     * @return int
     */
    public function getCarrierId(): int
    {
        return (int)$this->getData(self::CARRIER_ID);
    }

    /**
     * @param int $value
     *
     * @return CarrierInterface
     */
    public function setCarrierId(int $value): CarrierInterface
    {
        return $this->setData(self::CARRIER_ID, $value);
    }

    /**
     * @return string
     */
    public function getCarrier(): string
    {
        return (string)$this->getData(self::CARRIER);
    }

    /**
     * @param string $value
     *
     * @return CarrierInterface
     */
    public function setCarrier(string $value): CarrierInterface
    {
        return $this->setData(self::CARRIER, $value);
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(CarrierResource::class);
    }
}
