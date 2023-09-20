<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Cron;

use Vendiro\Connect\Api\Config\RepositoryInterface as ConfigProvider;
use Vendiro\Connect\Api\TrackQueue\RepositoryInterface as TrackQueueRepositoryInterface;
use Vendiro\Connect\Model\Config\Source\QueueStatus;
use Vendiro\Connect\Service\TrackTrace\Data;

/**
 * TrackTrace Cron
 */
class TrackTrace
{
    /**
     * @var TrackQueueRepositoryInterface
     */
    private $trackQueueItemRepository;
    /**
     * @var ConfigProvider
     */
    private $configProvider;
    /**
     * @var Data
     */
    private $shipmentService;

    /**
     * @param Data $shipmentService
     * @param ConfigProvider $configProvider
     * @param TrackQueueRepositoryInterface $trackQueueItemRepository
     */
    public function __construct(
        Data $shipmentService,
        ConfigProvider $configProvider,
        TrackQueueRepositoryInterface $trackQueueItemRepository
    ) {
        $this->shipmentService = $shipmentService;
        $this->configProvider = $configProvider;
        $this->trackQueueItemRepository = $trackQueueItemRepository;
    }

    /**
     * Confirm newly added Track&Trace data.
     *
     * @return void
     */
    public function confirmShipment(): void
    {
        if (!$this->configProvider->confirmShipments()) {
            return;
        }

        $queueItems = $this->trackQueueItemRepository
            ->getByDataSet(['main_table.status' => QueueStatus::QUEUE_STATUS_NEW], false, true)
            ->setPageSize($this->configProvider->getLimit('shipments'));

        if (!$queueItems->getSize()) {
            return;
        }

        foreach ($queueItems as $trackQueueItem) {
            $this->shipmentService->shipmentCall($trackQueueItem);
        }
    }
}
