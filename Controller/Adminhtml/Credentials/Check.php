<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Controller\Adminhtml\Credentials;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Vendiro\Connect\Api\Config\RepositoryInterface as ConfigProvider;
use Vendiro\Connect\Webservices\Endpoints\GetAccount;

class Check extends Action
{

    private const MESSAGES = [
        'success' => 'Successfully connected to account %s. Don\'t forget to save changes.',
        'notice' => 'Successfully connected, but no account found.',
        'error' => 'Your API Credentials could not be validated: %s'
    ];

    /**
     * @var GetAccount
     */
    private $getAccount;
    /**
     * @var Json
     */
    private $resultJson;
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @param Action\Context $context
     * @param ConfigProvider $configProvider
     * @param GetAccount $getAccount
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Action\Context $context,
        ConfigProvider $configProvider,
        GetAccount $getAccount,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->configProvider = $configProvider;
        $this->getAccount = $getAccount;
        $this->resultJson = $resultJsonFactory->create();
    }

    /**
     * @return Json
     */
    public function execute(): Json
    {
        return $this->resultJson->setData(
            $this->validateAccount()
        );
    }

    /**
     * @return array
     */
    private function validateAccount(): array
    {
        try {
            $this->getAccount->setCredentials($this->getCredentials());
            $accountResult = $this->getAccount->call();
            return [
                'success' => true,
                'message' => !empty($accountResult['account'])
                    ? sprintf(self::MESSAGES['success'], $accountResult['account'])
                    : self::MESSAGES['notice']
            ];
        } catch (\Exception $exception) {
            return [
                'success' => false,
                'message' => sprintf(self::MESSAGES['error'], $exception->getMessage())
            ];
        }
    }

    /**
     * @return array
     */
    private function getCredentials(): array
    {
        $apiKey = $this->getRequest()->getParam('api_key');
        $apiToken = $this->getRequest()->getParam('api_token');

        if (!$apiKey || $apiKey == '******') {
            $apiKey = $this->configProvider->getCredentials()['key'];
        }

        if (!$apiToken || $apiToken == '******') {
            $apiToken = $this->configProvider->getCredentials()['token'];
        }

        return [
            'key' => $apiKey,
            'token' => $apiToken
        ];
    }
}
