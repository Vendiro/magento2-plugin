<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Service\Marketplace;

use Magento\Framework\Exception\AuthenticationException;
use Vendiro\Connect\Api\Log\RepositoryInterface as LogRepository;
use Vendiro\Connect\Api\Marketplace\DataInterface as MarketplaceData;
use Vendiro\Connect\Api\Marketplace\RepositoryInterface as MarketplaceRepository;
use Vendiro\Connect\Webservices\Endpoints\GetMarketplaces;

class Data
{
    public const MESSAGES = [
        'success' => 'Total of %s marketplaces are successfully updated.',
        'error' => 'Your Vendiro marketplaces could not be retrieved: %s'
    ];

    /**
     * @var MarketplaceRepository $marketplaceRepository
     */
    private $marketplaceRepository;
    /**
     * @var GetMarketplaces $getCarriers
     */
    private $getMarketplaces;
    /**
     * @var LogRepository $logger
     */
    private $logger;

    /**
     * @param LogRepository $logger
     * @param GetMarketplaces $getMarketplaces
     * @param MarketplaceRepository $marketplaceRepository
     */
    public function __construct(
        LogRepository $logger,
        GetMarketplaces $getMarketplaces,
        MarketplaceRepository $marketplaceRepository
    ) {
        $this->logger = $logger;
        $this->getMarketplaces = $getMarketplaces;
        $this->marketplaceRepository = $marketplaceRepository;
    }

    /**
     * @return array
     */
    public function updateMarketplaces(): array
    {
        $updates = 0;

        try {
            $marketplaces = $this->getMarketplaces();
            foreach ($marketplaces as $marketplace) {
                if (empty($marketplace['id'])) {
                    continue;
                }
                $documentTypesEncoded = $marketplace['allowed_document_types'];
                $savedMarketplace = $this->getMarketplace((int)$marketplace['id'])
                    ->setMarketplaceId((int)$marketplace['id'])
                    ->setCountryCode($marketplace['country_code'])
                    ->setCurrency($marketplace['currency'])
                    ->setName($marketplace['name'])
                    ->setAllowedDocumentTypes($documentTypesEncoded);
                $this->marketplaceRepository->save($savedMarketplace);
                $updates++;
            }
            return [
                'error' => false,
                'message' => sprintf(self::MESSAGES['success'], $updates)
            ];
        } catch (\Exception $exception) {
            $this->logger->addErrorLog('updateCarriers', $exception->getMessage());
            return [
                'error' => true,
                'message' => sprintf(self::MESSAGES['error'], $exception->getMessage())
            ];
        }
    }

    /**
     * @return array
     * @throws AuthenticationException
     */
    public function getMarketplaces(): array
    {
        $this->getMarketplaces->setRequestData(['limit' => 250]);
        return $this->getMarketplaces->call();
    }

    /**
     * @param int $marketplaceId
     * @return MarketplaceData
     */
    private function getMarketplace(int $marketplaceId): MarketplaceData
    {
        $dataSet = [MarketplaceData::MARKETPLACE_ID => $marketplaceId];
        $marketplace = $this->marketplaceRepository->getByDataSet($dataSet, true);

        return $marketplace->getEntityId()
            ? $marketplace
            : $this->marketplaceRepository->create($dataSet);
    }
}
