<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Service\Invoice;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\ShipmentTrackInterface;
use Magento\Sales\Api\OrderRepositoryInterface as MagentoOrderRepository;
use Magento\Sales\Model\Order as MagentoOrder;
use Magento\Sales\Model\Order\Invoice as MagentoInvoice;
use Magento\Sales\Model\Order\Shipment as MagentoShipment;
use Vendiro\Connect\Api\Order\DataInterface as OrderData;
use Vendiro\Connect\Api\TrackQueue\RepositoryInterface as TrackQueueRepository;
use Vendiro\Connect\Model\Config\Source\QueueStatus;

class Validate
{

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var MagentoOrderRepository
     */
    private $magentoOrderRepository;
    /**
     * @var TrackQueueRepository
     */
    private $trackQueueRepository;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param MagentoOrderRepository $magentoOrderRepository
     * @param TrackQueueRepository $trackQueueRepository
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        MagentoOrderRepository $magentoOrderRepository,
        TrackQueueRepository $trackQueueRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->magentoOrderRepository = $magentoOrderRepository;
        $this->trackQueueRepository = $trackQueueRepository;
    }

    /**
     * @param OrderData $vendiroOrder
     *
     * @return MagentoInvoice
     */
    public function getInvoice(OrderData $vendiroOrder): ?MagentoInvoice
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('increment_id', $vendiroOrder->getOrderId(), 'eq');

        $orderResult = $this->magentoOrderRepository->getList($searchCriteria->create());

        if ($orderResult->getTotalCount() < 1) {
            return null;
        }

        /** @var MagentoOrder $magentoOrder */
        $magentoOrder = $orderResult->getFirstItem();
        if (!$this->validateShipment($magentoOrder)) {
            return null;
        }

        $invoiceCollection = $magentoOrder->getInvoiceCollection();
        if ($invoiceCollection->getTotalCount() < 1) {
            return null;
        }

        /** @var MagentoInvoice $invoice */
        $invoice = $invoiceCollection->getFirstItem();
        return $invoice;
    }

    /**
     * @param MagentoOrder $magentoOrder
     *
     * @return bool
     */
    private function validateShipment(MagentoOrder $magentoOrder): bool
    {
        if (!$magentoOrder->hasShipments()) {
            return false;
        }

        /**
        $shipment = $magentoOrder->getShipmentsCollection()->getFirstItem();
        foreach ($shipment->getTracks() as $track) {
            $valid = $this->validateTracking($track);
        }
        return $valid ?? false;
        **/

        return true;
    }

    /**
     * @param ShipmentTrackInterface $track
     *
     * @return bool
     */
    private function validateTracking(ShipmentTrackInterface $track): bool
    {
        $trackItems = $this->trackQueueRepository->getByDataSet(['track_id' => $track->getEntityId()]);
        if (!$trackItems->getSize()) {
            return false;
        }

        $valid = false;
        foreach ($trackItems as $trackItem) {
            $valid = ($valid ?: $trackItem->getStatus() != QueueStatus::QUEUE_STATUS_NEW);
        }

        return $valid;
    }
}
