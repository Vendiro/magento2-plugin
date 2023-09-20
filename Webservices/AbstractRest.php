<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Webservices;

use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\HTTP\ClientInterface;
use Vendiro\Connect\Api\Config\RepositoryInterface as ConfigProvider;
use Vendiro\Connect\Api\Log\RepositoryInterface as LogRepository;
use Vendiro\Connect\Webservices\Endpoints\EndpointInterface;
use Magento\Framework\Serialize\Serializer\Json;

abstract class AbstractRest
{
    /**
     * @var ClientInterface
     */
    protected $client;
    /**
     * @var ConfigProvider
     */
    protected $configProvider;
    /**
     * @var LogRepository
     */
    private $logger;
    /**
     * @var Json
     */
    private $json;

    /**
     * @param ClientInterface $client
     * @param ConfigProvider $configProvider
     * @param Json $json
     * @param LogRepository $logger
     */
    public function __construct(
        ClientInterface $client,
        ConfigProvider $configProvider,
        Json $json,
        LogRepository $logger
    ) {
        $this->client = $client;
        $this->configProvider = $configProvider;
        $this->json = $json;
        $this->logger = $logger;
    }

    /**
     * @param EndpointInterface $endpoint
     * @param false $includeHttpStatus
     *
     * @return array
     * @throws AuthenticationException
     */
    public function getRequest(EndpointInterface $endpoint, bool $includeHttpStatus = false): array
    {
        $url = $this->getUrl($endpoint);
        $this->setHeaders($endpoint);
        $this->writeDebugLog($endpoint, $url);

        switch ($endpoint->getMethod()) {
            case 'GET':
                $this->client->get($url);
                break;
            case 'PUT':
                $this->client->put($url, $this->json->serialize($endpoint->getRequestData()));
                break;
            case 'POST':
                $this->client->post($url, $this->json->serialize($endpoint->getRequestData()));
                break;
        }

        return $this->processApiResult($includeHttpStatus);
    }

    /**
     * @param EndpointInterface $endpoint
     * @return string
     */
    private function getUrl(EndpointInterface $endpoint): string
    {
        $url = $this->configProvider->getApiBaseUrl() . $endpoint->getEndpointUrl();
        if ($endpoint->getMethod() == 'GET' && !empty($endpoint->getRequestData())) {
            $url .= '?' . http_build_query($endpoint->getRequestData());
        }

        return $url;
    }

    /**
     * @return void
     */
    private function setHeaders(EndpointInterface $endpoint)
    {
        $credentials = $endpoint->getCredentials();
        $this->client->setHeaders([
            'Authorization' => 'Basic ' . base64_encode($credentials['key'] . ':' . $credentials['token']),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json; charset=UTF-8',
            'User-Agent' => 'VendiroMagento2Plugin/' . $this->configProvider->getExtensionVersion()
        ]);
    }

    /**
     * @param EndpointInterface $endpoint
     * @param string $url
     * @return void
     */
    private function writeDebugLog(EndpointInterface $endpoint, string $url)
    {
        $this->logger->addDebugLog(
            'Execute API call',
            sprintf(
                '[%s] %s - %s',
                $endpoint->getMethod(),
                $url,
                json_encode($endpoint->getRequestData())
            )
        );
    }

    /**
     * @param bool $includeHttpStatus
     * @return array
     * @throws AuthenticationException
     */
    private function processApiResult(bool $includeHttpStatus = false): array
    {
        $this->logger->addDebugLog(
            'Result API call',
            sprintf('[%s] %s', $this->client->getStatus(), $this->client->getBody())
        );

        $response = $this->formatResponse($this->client->getBody());
        if ($this->client->getStatus() == 401) {
            throw new AuthenticationException(__($response['message'] ?? 'Unknown error'));
        }

        if ($includeHttpStatus) {
            $response['http_status'] = $this->client->getStatus();
        }

        return $response;
    }

    /**
     * @param $response
     *
     * @return array
     */
    private function formatResponse($response): array
    {
        if (is_string($response)) {
            $response = json_decode($response, true);
        }

        if (!is_array($response)) {
            $response = [$response];
        }

        return $response;
    }
}
