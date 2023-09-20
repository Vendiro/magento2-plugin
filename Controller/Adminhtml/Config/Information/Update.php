<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Controller\Adminhtml\Config\Information;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Vendiro\Connect\Service\Carrier\Data as CarrierData;
use Vendiro\Connect\Service\Marketplace\Data as MarketplaceData;

class Update extends Action
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
     * @var Json
     */
    private $resultJson;

    /**
     * @param Action\Context $context
     * @param CarrierData $carrierData
     * @param MarketplaceData $marketplaceData
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Action\Context $context,
        CarrierData $carrierData,
        MarketplaceData $marketplaceData,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->carrierData = $carrierData;
        $this->marketplaceData = $marketplaceData;
        $this->resultJson = $resultJsonFactory->create();
    }

    /**
     * @return Json
     */
    public function execute(): Json
    {
        $marketplaceUpdate = $this->marketplaceData->updateMarketplaces();
        $carrierUpdate =  $this->carrierData->updateCarriers();

        $results = [
            'error' => $marketplaceUpdate['error'] || $carrierUpdate['error'],
            'message' => sprintf('%s <br/> %s', $marketplaceUpdate['message'], $carrierUpdate['message'])
        ];

        return $this->resultJson->setData(
            $results
        );
    }
}
