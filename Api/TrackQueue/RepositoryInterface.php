<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Api\TrackQueue;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Vendiro\Connect\Model\TrackQueue\Collection;
use Vendiro\Connect\Model\TrackQueue\Data;

/**
 * Interface for track queue repository
 * @api
 */
interface RepositoryInterface
{

    /**
     * Exceptions
     */
    public const INPUT_EXCEPTION = 'An ID is needed. Set the ID and try again.';
    public const NO_SUCH_ENTITY_EXCEPTION = 'The tracking with id "%1" does not exist.';
    public const COULD_NOT_DELETE_EXCEPTION = 'Could not delete tracking: %1';
    public const COULD_NOT_SAVE_EXCEPTION = 'Could not save tracking: %1';

    /**
     * Loads a specified tracking
     *
     * @param int $entityId
     *
     * @return DataInterface
     * @throws LocalizedException
     */
    public function get(int $entityId): DataInterface;

    /**
     * Save a tracking.
     *
     * @param DataInterface $entity
     * @return DataInterface
     * @throws CouldNotSaveException
     */
    public function save(DataInterface $entity): DataInterface;

    /**
     * Create a tracking.
     *
     * @param array $data
     * @return DataInterface
     */
    public function create(array $data = []): DataInterface;

    /**
     * Retrieves a tracking matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return SearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * Register tracking to delete
     *
     * @param DataInterface $entity
     *
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(DataInterface $entity): bool;

    /**
     * Deletes tracking entity by ID
     *
     * @param int $entityId
     * @param bool $force
     *
     * @return bool
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById(int $entityId, bool $force = false): bool;

    /**
     * Get data collection by set of attribute values
     *
     * @param array $dataSet
     * @param bool $getFirst
     * @param bool $joinTracking
     * @return Collection|Data
     */
    public function getByDataSet(array $dataSet, bool $getFirst = false, bool $joinTracking = false);
}
