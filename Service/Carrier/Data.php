<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Service\Carrier;

use Magento\Framework\Exception\AuthenticationException;
use Vendiro\Connect\Api\Carrier\RepositoryInterface as CarrierRepository;
use Vendiro\Connect\Api\Log\RepositoryInterface as LogRepository;
use Vendiro\Connect\Webservices\Endpoints\GetCarriers;
use Vendiro\Connect\Api\Carrier\DataInterface;

class Data
{

    public const MESSAGES = [
        'success' => 'Total of %s shipment providers are successfully updated. ',
        'error' => 'Your Vendiro shipment providers could not be retrieved: %s'
    ];

    /**
     * @var GetCarriers
     */
    private $getCarriers;
    /**
     * @var CarrierRepository
     */
    private $carrierRepository;
    /**
     * @var LogRepository
     */
    private $logger;

    /**
     * @param GetCarriers $getCarriers
     * @param CarrierRepository $carrierRepository
     * @param LogRepository $logger
     */
    public function __construct(
        GetCarriers $getCarriers,
        CarrierRepository $carrierRepository,
        LogRepository $logger
    ) {
        $this->getCarriers = $getCarriers;
        $this->carrierRepository = $carrierRepository;
        $this->logger = $logger;
    }

    /**
     * @return array
     */
    public function updateCarriers(): array
    {
        $updates = 0;

        try {
            $carriers = $this->getCarriers();
            foreach ($carriers as $carrierId => $carrier) {
                $savedCarrier = $this->getCarrier($carrierId)
                    ->setCarrierId($carrierId)
                    ->setCarrier($carrier);
                $this->carrierRepository->save($savedCarrier);
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
     * Get all carriers from platform
     *
     * @return array
     * @throws AuthenticationException
     */
    public function getCarriers(): array
    {
        $this->getCarriers->setRequestData(['limit' => 250]);
        return $this->getCarriers->call();
    }

    /**
     * @param int $carrierId
     * @return DataInterface
     */
    private function getCarrier(int $carrierId): DataInterface
    {
        $dataSet = [DataInterface::CARRIER_ID => $carrierId];
        $carrier = $this->carrierRepository->getByDataSet($dataSet, true);

        return $carrier->getEntityId()
            ? $carrier
            : $this->carrierRepository->create($dataSet);
    }
}
