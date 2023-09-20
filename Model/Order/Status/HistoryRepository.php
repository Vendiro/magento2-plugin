<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Model\Order\Status;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Sales\Api\Data\OrderStatusHistoryInterface;
use Magento\Sales\Api\Data\OrderStatusHistoryInterfaceFactory;
use Magento\Sales\Api\Data\OrderStatusHistorySearchResultInterfaceFactory;
use Magento\Sales\Model\Spi\OrderStatusHistoryResourceInterface;

class HistoryRepository extends \Magento\Sales\Model\Order\Status\HistoryRepository
{
    /**
     * @var OrderStatusHistoryInterfaceFactory
     */
    private $historyFactory;

    /**
     * @param OrderStatusHistoryResourceInterface $historyResource
     * @param OrderStatusHistoryInterfaceFactory $historyFactory
     * @param OrderStatusHistorySearchResultInterfaceFactory $searchResultFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        OrderStatusHistoryResourceInterface $historyResource,
        OrderStatusHistoryInterfaceFactory $historyFactory,
        OrderStatusHistorySearchResultInterfaceFactory $searchResultFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        parent::__construct($historyResource, $historyFactory, $searchResultFactory, $collectionProcessor);
        $this->historyFactory = $historyFactory;
    }

    /**
     * @param array $data
     *
     * @return OrderStatusHistoryInterface
     */
    public function create(array $data = []): OrderStatusHistoryInterface
    {
        return $this->historyFactory->create($data);
    }
}
