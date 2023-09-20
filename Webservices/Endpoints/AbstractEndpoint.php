<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Webservices\Endpoints;

use Vendiro\Connect\Api\Config\RepositoryInterface as ConfigProvider;
use Vendiro\Connect\Webservices\AbstractRest;

abstract class AbstractEndpoint implements EndpointInterface
{

    public const ENDPOINT_URL = '/';
    public const METHOD = 'GET';

    private $requestData = [];
    private $urlArguments;
    private $credentials = [];

    /**
     * @var AbstractRest
     */
    private $restApi;
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @param AbstractRest $restApi
     * @param ConfigProvider $configProvider
     */
    public function __construct(
        AbstractRest $restApi,
        ConfigProvider $configProvider
    ) {
        $this->restApi = $restApi;
        $this->configProvider = $configProvider;
    }

    /**
     * @inheritDoc
     */
    public function call($urlParameter = null, bool $includeHttpStatus = false): array
    {
        $this->setUrlArguments($urlParameter);
        return $this->restApi->getRequest($this, $includeHttpStatus);
    }

    /**
     * @inheritDoc
     */
    public function getEndpointUrl(): string
    {
        return sprintf(static::ENDPOINT_URL, $this->getUrlArguments());
    }

    /**
     * @inheritDoc
     */
    public function getUrlArguments()
    {
        return $this->urlArguments;
    }

    /**
     * @inheritDoc
     */
    public function setUrlArguments($urlArguments)
    {
        $this->urlArguments = $urlArguments;
    }

    /**
     * @inheritDoc
     */
    public function getMethod(): string
    {
        return static::METHOD;
    }

    /**
     * @inheritDoc
     */
    public function getRequestData(): ?array
    {
        return $this->requestData;
    }

    /**
     * @inheritDoc
     */
    public function setRequestData(array $requestData)
    {
        $this->requestData = $requestData;
    }

    /**
     * @inheritDoc
     */
    public function getCredentials(): ?array
    {
        if (!$this->credentials) {
            $this->setCredentials(
                $this->configProvider->getCredentials()
            );
        }

        return $this->credentials;
    }

    /**
     * @inheritDoc
     */
    public function setCredentials(array $credentials)
    {
        $this->credentials = $credentials;
    }
}
