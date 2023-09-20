<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Cron;

use Vendiro\Connect\Api\Config\RepositoryInterface as ConfigProvider;
use Vendiro\Connect\Api\Log\RepositoryInterface as LogRepository;
use Vendiro\Connect\Service\Order\Import as OrderImportService;

/**
 * Import Order Cron
 */
class Order
{
    /**
     * @var OrderImportService
     */
    private $orderImportService;
    /**
     * @var ConfigProvider
     */
    private $configProvider;
    /**
     * @var LogRepository
     */
    private $logger;

    /**
     * @param OrderImportService $orderImportService
     * @param ConfigProvider $configProvider
     * @param LogRepository $logger
     */
    public function __construct(
        OrderImportService $orderImportService,
        ConfigProvider $configProvider,
        LogRepository $logger
    ) {
        $this->orderImportService = $orderImportService;
        $this->configProvider = $configProvider;
        $this->logger = $logger;
    }

    /**
     * @return void
     */
    public function importToMagento()
    {
        if (!$this->isEnabled()) {
            return;
        }

        try {
            $this->orderImportService->execute();
        } catch (\Exception $exception) {
            $this->logger->addErrorLog('ImportOrders Cron', $exception->getMessage());
        }
    }

    /**
     * @return bool
     */
    private function isEnabled(): bool
    {
        return $this->configProvider->isEnabled() && $this->configProvider->importOrders();
    }
}
