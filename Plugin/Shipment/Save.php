<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Plugin\Shipment;

use Exception;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Sales\Api\Data\ShipmentInterface;
use Vendiro\Connect\Api\Config\RepositoryInterface as ConfigProvider;
use Vendiro\Connect\Api\TrackQueue\DataInterface;
use Vendiro\Connect\Api\TrackQueue\RepositoryInterface as TrackQueueRepositoryInterface;
use Vendiro\Connect\Model\Carrier\Vendiro;
use Vendiro\Connect\Model\Config\Source\QueueStatus;

class Save
{

    /**
     * @var TrackQueueRepositoryInterface
     */
    private $trackQueueRepository;
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @param ConfigProvider $configProvider
     * @param TrackQueueRepositoryInterface $trackQueueRepository
     */
    public function __construct(
        ConfigProvider $configProvider,
        TrackQueueRepositoryInterface $trackQueueRepository
    ) {
        $this->configProvider = $configProvider;
        $this->trackQueueRepository = $trackQueueRepository;
    }

    /**
     * @param $subject
     * @param $shipment
     * @return array|null[]
     */
    public function beforeSave($subject, $shipment = null): array
    {
        if (!$shipment) {
            $shipment = $subject;
        }

        if ($shipment->getOrder()->getShippingMethod() === Vendiro::SHIPPING_CARRIER_METHOD) {
            $this->saveVendiroCarrier($shipment);
        }

        return [$shipment];
    }

    /**
     * Save default Vendiro Carrier to Magento Shipment.
     *
     * @param ShipmentInterface $shipment
     */
    private function saveVendiroCarrier(ShipmentInterface $shipment)
    {
        $shipment->setData('vendiro_carrier', $this->configProvider->getDefaultCarrier());
    }

    /**
     * @param $subject
     * @param null $shipment
     *
     * @return null
     * @throws Exception
     */
    public function afterSave($subject, $shipment = null)
    {
        if ($shipment->getOrder()->getShippingMethod() != Vendiro::SHIPPING_CARRIER_METHOD) {
            return $shipment;
        }

        if ($tracks = $this->getTracks($subject, $shipment)) {
            $this->saveTracks($tracks);
        }

        return $shipment;
    }

    /**
     * @param $subject
     * @param $shipment
     * @return array|null
     * @throws CouldNotSaveException
     */
    public function getTracks($subject, $shipment = null): ?array
    {
        $tracks = $shipment ? $shipment->getTracks() : null;
        if (!$tracks && $subject instanceof ShipmentInterface) {
            $tracks = $subject->getTracks();
        }

        return $tracks;
    }

    /**
     * @param $tracks
     * @return void
     * @throws CouldNotSaveException
     */
    public function saveTracks($tracks): void
    {
        foreach ($tracks as $track) {
            $vendiroTrackQueueItem = $this->getTrack((int)$track->getId());
            $vendiroTrackQueueItem->setTrackId((int)$track->getId())
                ->setStatus(QueueStatus::QUEUE_STATUS_NEW);

            $this->trackQueueRepository->save($vendiroTrackQueueItem);
        }
    }

    /**
     * @param int $trackId
     * @return DataInterface
     */
    private function getTrack(int $trackId): DataInterface
    {
        $dataSet = [DataInterface::TRACK_ID => $trackId];
        $carrier = $this->trackQueueRepository->getByDataSet($dataSet, true);

        return $carrier->getEntityId()
            ? $carrier
            : $this->trackQueueRepository->create($dataSet);
    }
}
