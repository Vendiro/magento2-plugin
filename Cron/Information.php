<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Cron;

use Vendiro\Connect\Api\Config\RepositoryInterface as ConfigProvider;
use Vendiro\Connect\Api\Log\RepositoryInterface as LogRepository;
use Vendiro\Connect\Service\Carrier\Data as CarrierData;
use Vendiro\Connect\Service\Marketplace\Data as MarketplaceData;

/**
 * Update Carrier Cron
 */
class Information
{
    /**
     * @var CarrierData
     */
    private $carrierData;
    /**
     * @var MarketplaceData
     */
    private $marketplaceData;
    /**
     * @var LogRepository
     */
    private $logger;
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @param ConfigProvider $configProvider
     * @param CarrierData $carrierData
     * @param MarketplaceData $marketplaceData
     * @param LogRepository $logger
     */
    public function __construct(
        ConfigProvider $configProvider,
        CarrierData $carrierData,
        MarketplaceData $marketplaceData,
        LogRepository $logger
    ) {
        $this->configProvider = $configProvider;
        $this->carrierData = $carrierData;
        $this->marketplaceData = $marketplaceData;
        $this->logger = $logger;
    }

    /**
     * @return void
     */
    public function updateInformation()
    {
        if (!$this->configProvider->isEnabled()) {
            return;
        }

        try {
            $this->carrierData->updateCarriers();
            $this->marketplaceData->updateMarketplaces();
        } catch (\Exception $exception) {
            $this->logger->addErrorLog('updateInformation Cron', $exception->getMessage());
        }
    }
}
