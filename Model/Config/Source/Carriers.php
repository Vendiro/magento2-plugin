<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Vendiro\Connect\Api\Carrier\RepositoryInterface as CarrierRepository;

class Carriers implements OptionSourceInterface
{

    /**
     * @var CarrierRepository
     */
    private $carrierRepository;

    /**
     * Carrier constructor.
     *
     * @param CarrierRepository $carrierRepository
     */
    public function __construct(
        CarrierRepository $carrierRepository
    ) {
        $this->carrierRepository = $carrierRepository;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        $options[] = [
            'value' => '0',
            'label' => __('--none--')
        ];

        $carriers = $this->carrierRepository->getByDataSet([]);
        foreach ($carriers as $carrier) {
            $options[] = ['value' => $carrier->getCarrierId(), 'label' => __($carrier->getCarrier())];
        }

        return $options;
    }
}
