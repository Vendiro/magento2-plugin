<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Model\Carrier;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Psr\Log\LoggerInterface;
use Vendiro\Connect\Service\Session\Manager as SessionManager;

/**
 * Vendiro Carrier Model
 */
class Vendiro extends AbstractCarrier implements CarrierInterface
{

    public const SHIPPING_CARRIER_METHOD = 'vendiro_shipping';
    protected $_code = 'vendiro';

    /**
     * @var ResultFactory
     */
    private $resultFactory;
    /**
     * @var MethodFactory
     */
    private $methodFactory;
    /**
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $resultFactory
     * @param MethodFactory $methodFactory
     * @param SessionManager $sessionManager
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $resultFactory,
        MethodFactory $methodFactory,
        SessionManager $sessionManager,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
        $this->resultFactory = $resultFactory;
        $this->methodFactory = $methodFactory;
        $this->sessionManager = $sessionManager;
    }

    /**
     * @inheritDoc
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->sessionManager->isVendiroOrder()) {
            return false;
        }

        $amount = $this->getShippingCost($request);
        $method = $this->createMethod();
        $method->setPrice($amount);
        $method->setCost($amount);

        $result = $this->resultFactory->create();
        $result->append($method);

        return $result;
    }

    /**
     * @param RateRequest $request
     *
     * @return string|int|float
     */
    private function getShippingCost(RateRequest $request)
    {
        $quoteItem = $request->getAllItems()[0];
        $quote = $quoteItem->getQuote();
        return $quote->getVendiroShippingCost();
    }

    /**
     * @return Method|void
     */
    private function createMethod()
    {
        $title = $this->getConfigData('title');
        $name = $this->getConfigData('name');
        $code = $this->getCarrierCode();

        $method = $this->methodFactory->create();

        $method->setCarrier($code);
        $method->setCarrierTitle($title);
        $method->setMethod('shipping');
        $method->setMethodTitle($name);

        return $method;
    }

    /**
     * @inheritDoc
     */
    public function getAllowedMethods(): array
    {
        return [$this->getCarrierCode() => $this->getConfigData('name')];
    }
}
