<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Model\TrackQueue;

use Exception;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Vendiro\Connect\Api\Log\RepositoryInterface as LogRepository;
use Vendiro\Connect\Api\TrackQueue\DataInterface;
use Vendiro\Connect\Api\TrackQueue\DataInterfaceFactory;
use Vendiro\Connect\Api\TrackQueue\RepositoryInterface;
use Vendiro\Connect\Api\TrackQueue\SearchResultsInterface;
use Vendiro\Connect\Api\TrackQueue\SearchResultsInterfaceFactory;

/**
 * TrackQueue Repository class
 */
class Repository implements RepositoryInterface
{

    /**
     * @var SearchResultsInterfaceFactory
     */
    private $searchResultFactory;
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var DataInterfaceFactory
     */
    private $dataFactory;
    /**
     * @var ResourceModel
     */
    private $resource;
    /**
     * @var LogRepository
     */
    private $logger;

    /**
     * @param SearchResultsInterfaceFactory $searchResultFactory
     * @param CollectionFactory $collectionFactory
     * @param ResourceModel $resource
     * @param DataInterfaceFactory $dataFactory
     * @param LogRepository $logger
     */
    public function __construct(
        SearchResultsInterfaceFactory $searchResultFactory,
        CollectionFactory $collectionFactory,
        ResourceModel $resource,
        DataInterfaceFactory $dataFactory,
        LogRepository $logger
    ) {
        $this->searchResultFactory = $searchResultFactory;
        $this->collectionFactory = $collectionFactory;
        $this->resource = $resource;
        $this->dataFactory = $dataFactory;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        /* @var Collection $collection */
        $collection = $this->collectionFactory->create();
        return $this->searchResultFactory->create()
            ->setSearchCriteria($searchCriteria)
            ->setItems($collection->getItems())
            ->setTotalCount($collection->getSize());
    }

    /**
     * @inheritDoc
     */
    public function create(array $data = []): DataInterface
    {
        return $this->dataFactory->create($data);
    }

    /**
     * @inheritDoc
     */
    public function deleteById(int $entityId, bool $force = false): bool
    {
        $entity = $this->get($entityId);
        return $this->delete($entity, $force);
    }

    /**
     * @inheritDoc
     */
    public function get(int $entityId): DataInterface
    {
        if (!$entityId) {
            $exceptionMsg = static::INPUT_EXCEPTION;
            throw new InputException(__($exceptionMsg));
        } elseif (!$this->resource->isExists($entityId)) {
            $exceptionMsg = self::NO_SUCH_ENTITY_EXCEPTION;
            throw new NoSuchEntityException(__($exceptionMsg, $entityId));
        }
        return $this->dataFactory->create()->load($entityId);
    }

    /**
     * @inheritDoc
     */
    public function delete(DataInterface $entity, bool $force = false): bool
    {
        try {
            $this->resource->delete($entity);
        } catch (Exception $exception) {
            $this->logger->addErrorLog('Delete tracking', $exception->getMessage());
            $exceptionMsg = self::COULD_NOT_DELETE_EXCEPTION;
            throw new CouldNotDeleteException(__(
                $exceptionMsg,
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getByDataSet(array $dataSet, bool $getFirst = false, bool $joinTracking = false)
    {
        $collection = $this->collectionFactory->create();
        foreach ($dataSet as $attribute => $value) {
            if ($value === null) {
                $collection->addFieldToFilter($attribute, ['null' => true]);
            } elseif (is_array($value)) {
                $collection->addFieldToFilter($attribute, ['in' => $value]);
            } else {
                $collection->addFieldToFilter($attribute, $value);
            }
        }

        if ($joinTracking) {
            $collection->getSelect()->joinLeft(
                ['sales_shipment_track' => $collection->getTable('sales_shipment_track')],
                'main_table.track_id = sales_shipment_track.entity_id',
                [
                    'shipment_code' => 'sales_shipment_track.track_number',
                    'carrier_name' => 'sales_shipment_track.title',
                ]
            );
            $collection->getSelect()->joinLeft(
                ['sales_order' => $collection->getTable('sales_order')],
                'sales_shipment_track.order_id = sales_order.entity_id',
                [
                    'order_increment_id' => 'sales_order.increment_id',
                ]
            );
        }

        if ($getFirst) {
            return $collection->getFirstItem();
        } else {
            return $collection;
        }
    }

    /**
     * @inheritDoc
     */
    public function save(DataInterface $entity): DataInterface
    {
        try {
            $this->resource->save($entity);
        } catch (Exception $exception) {
            $this->logger->addErrorLog('Save tracking', $exception->getMessage());
            $exceptionMsg = self::COULD_NOT_SAVE_EXCEPTION;
            throw new CouldNotSaveException(__(
                $exceptionMsg,
                $exception->getMessage()
            ));
        }
        return $entity;
    }
}
