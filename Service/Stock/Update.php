<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Service\Stock;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Vendiro\Connect\Api\Config\RepositoryInterface as ConfigProvider;
use Vendiro\Connect\Api\Log\RepositoryInterface as LogRepository;
use Vendiro\Connect\Api\Stock\RepositoryInterface as StockRepositoryInterface;
use Vendiro\Connect\Webservices\Endpoints\UpdateProductsStock;

/**
 * Set products stock service class
 */
class Update
{

    /**
     * @var StockRepositoryInterface
     */
    private $stockRepository;
    /**
     * @var UpdateProductsStock
     */
    private $updateProductsStock;
    /**
     * @var LogRepository
     */
    private $logRepository;
    /**
     * @var Data\Collector
     */
    private $collector;
    /**
     * @var DateTime
     */
    private $dateTime;
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * Update constructor.
     * @param StockRepositoryInterface $stockRepository
     * @param UpdateProductsStock $updateProductsStock
     * @param Data\Collector $collector
     * @param DateTime $dateTime
     * @param LogRepository $logRepository
     */
    public function __construct(
        StockRepositoryInterface $stockRepository,
        UpdateProductsStock $updateProductsStock,
        ConfigProvider $configProvider,
        Data\Collector $collector,
        DateTime $dateTime,
        LogRepository $logRepository
    ) {
        $this->stockRepository = $stockRepository;
        $this->updateProductsStock = $updateProductsStock;
        $this->collector = $collector;
        $this->dateTime = $dateTime;
        $this->logRepository = $logRepository;
        $this->configProvider = $configProvider;
    }

    /**
     * @param array|null $entityIds
     * @return array
     */
    public function execute(?array $entityIds): array
    {
        try {
            if (!empty($entityIds)) {
                $collection = $this->stockRepository->getByDataSet(['main_table.entity_id' => $entityIds]);
            } else {
                $collection = $this->stockRepository->getByDataSet(['needs_update' => 1]);
            }

            $collection->setPageSize($this->configProvider->getLimit('stock'));

            list($requestData, $updatedSkus) = $this->collector->execute($collection->getAllIds());
            $this->updateProductsStock->setRequestData($requestData);
            $response = $this->updateProductsStock->call();

            if (!isset($response['count_processed_skus'])) {
                $this->logRepository->addDebugLog('Stock Api', $response['message']);
                return ['message' => $response['message']];
            }

            if (!empty($updatedSkus)) {
                $this->updateMultipleStockBySku($updatedSkus);
            }

            return [
                'success' => true,
                'message' => sprintf('%s product(s) have been updated', count($updatedSkus))
            ];
        } catch (\Exception $exception) {
            return [
                'success' => false,
                'message' => $exception->getMessage()
            ];
        }
    }

    /**
     * @param array $skus
     */
    private function updateMultipleStockBySku(array $skus)
    {
        try {
            $this->stockRepository->updateMultiple(
                ['needs_update' => 0, 'synced_at' => $this->dateTime->date("Y-m-d H:i:s")],
                ['product_sku in (?)' => $skus]
            );
        } catch (LocalizedException $exception) {
            $this->logRepository->addErrorLog(
                'updateMultipleStockBySku',
                __("Vendiro stock notice: Could not update the stock queue for SKUS %1", implode(', ', $skus))
            );
        }
    }
}
