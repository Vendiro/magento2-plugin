<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Cron;

use Magento\Framework\App\ResourceConnection;
use Vendiro\Connect\Api\Config\RepositoryInterface as ConfigProvider;
use Vendiro\Connect\Api\Log\RepositoryInterface as LogRepository;
use Vendiro\Connect\Service\Stock\Update as UpdateStock;

/**
 * Update Stock Cron
 */
class Stock
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;
    /**
     * @var LogRepository
     */
    private $logger;
    /**
     * @var UpdateStock
     */
    private $updateStock;
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param ConfigProvider $configProvider
     * @param LogRepository $logger
     * @param UpdateStock $updateStock
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ConfigProvider $configProvider,
        LogRepository $logger,
        UpdateStock $updateStock,
        ResourceConnection $resourceConnection
    ) {
        $this->configProvider = $configProvider;
        $this->logger = $logger;
        $this->updateStock = $updateStock;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @return void
     */
    public function updateStock(): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        try {
            $this->updateStock->execute(null);
        } catch (\Exception $exception) {
            $this->logger->addErrorLog('updateStock Cron', $exception->getMessage());
        }
    }

    /**
     * @return bool
     */
    private function isEnabled(): bool
    {
        return $this->configProvider->isEnabled() && $this->configProvider->updateInventory();
    }

    /**
     * @return void
     */
    public function forceStockQueue(): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        try {
            $connection = $this->resourceConnection->getConnection();
            $connection->update(
                $this->resourceConnection->getTableName('vendiro_stock'),
                ['needs_update' => 1]
            );
        } catch (\Exception $exception) {
            $this->logger->addErrorLog('forceStockQueue Cron', $exception->getMessage());
        }
    }
}
