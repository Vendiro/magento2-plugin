<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Service\TrackTrace;

use Vendiro\Connect\Api\Config\RepositoryInterface as ConfigProvider;
use Vendiro\Connect\Api\Log\RepositoryInterface as LogRepository;
use Vendiro\Connect\Api\TrackQueue\DataInterface;
use Vendiro\Connect\Api\TrackQueue\RepositoryInterface as TrackQueueRepository;
use Vendiro\Connect\Exception as VendiroException;
use Vendiro\Connect\Model\Config\Source\QueueStatus;
use Vendiro\Connect\Webservices\Endpoints\ConfirmShipment;

class Data
{
    /**
     * @var ConfirmShipment
     */
    private $confirmShipment;
    /**
     * @var TrackQueueRepository
     */
    private $trackQueueItemRepository;
    /**
     * @var ConfigProvider
     */
    private $configProvider;
    /**
     * @var LogRepository
     */
    private $logger;

    /**
     * @param ConfirmShipment $confirmShipment
     * @param TrackQueueRepository $trackQueueItemRepository
     * @param ConfigProvider $configProvider
     * @param LogRepository $logger
     */
    public function __construct(
        ConfirmShipment $confirmShipment,
        TrackQueueRepository $trackQueueItemRepository,
        ConfigProvider $configProvider,
        LogRepository $logger
    ) {
        $this->confirmShipment = $confirmShipment;
        $this->trackQueueItemRepository = $trackQueueItemRepository;
        $this->configProvider = $configProvider;
        $this->logger = $logger;
    }

    /**
     * @param DataInterface $trackQueueItem
     * @return void
     */
    public function shipmentCall(DataInterface $trackQueueItem)
    {
        try {
            $this->confirmShipment->setRequestData([
                'carrier_id' => $this->configProvider->getDefaultCarrier(),
                'shipment_code' => $trackQueueItem->getShipmentCode(),
                'carrier_name' => $trackQueueItem->getCarrierName()
            ]);

            $result = $this->confirmShipment->call($trackQueueItem->getOrderIncrementId(), true);
            if ($result['http_status'] == 422) {
                throw new VendiroException(__('Unable to process request'));
            }

            if (isset($result['message']) && $result['message']) {
                throw new VendiroException(__($result['message']));
            }

            $trackQueueItem->setStatus(QueueStatus::QUEUE_STATUS_SHIPMENT_CREATED);
            $this->trackQueueItemRepository->save($trackQueueItem);
        } catch (\Exception $exception) {
            $this->logger->addErrorLog('shipmentCall', $exception->getMessage());
        }
    }
}
