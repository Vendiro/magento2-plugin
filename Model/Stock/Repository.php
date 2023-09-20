<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Model\Stock;

use Exception;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Vendiro\Connect\Api\Stock\DataInterface;
use Vendiro\Connect\Api\Stock\DataInterfaceFactory;
use Vendiro\Connect\Api\Stock\SearchResultsInterface;
use Vendiro\Connect\Api\Stock\SearchResultsInterfaceFactory;
use Vendiro\Connect\Api\Stock\RepositoryInterface;
use Vendiro\Connect\Api\Log\RepositoryInterface as LogRepository;

/**
 * Stock Repository class
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
            $this->logger->addErrorLog('Delete carrier', $exception->getMessage());
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
    public function getByDataSet(array $dataSet, bool $getFirst = false)
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
            $this->logger->addErrorLog('Save carrier', $exception->getMessage());
            $exceptionMsg = self::COULD_NOT_SAVE_EXCEPTION;
            throw new CouldNotSaveException(__(
                $exceptionMsg,
                $exception->getMessage()
            ));
        }
        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function updateMultiple(array $data, array $condition = []): int
    {
        $connection = $this->resource->getConnection();
        $tableName = $this->resource->getTable(ResourceModel::ENTITY_TABLE);
        return $connection->update($tableName, $data, $condition);
    }
}
