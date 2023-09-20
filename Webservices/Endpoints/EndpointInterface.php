<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Webservices\Endpoints;

use Magento\Framework\Exception\AuthenticationException;

interface EndpointInterface
{
    /**
     * @param null $urlParameter
     * @param bool $includeHttpStatus
     *
     * @return array
     * @throws AuthenticationException
     */
    public function call($urlParameter = null, bool $includeHttpStatus = false): array;

    /**
     * @return string
     */
    public function getEndpointUrl(): string;

    /**
     * @return string
     */
    public function getMethod(): string;

    /**
     * @param string $urlArguments
     */
    public function setUrlArguments($urlArguments);

    /**
     * @return array
     */
    public function getUrlArguments();

    /**
     * @param array $requestData
     */
    public function setRequestData(array $requestData);

    /**
     * @return ?array
     */
    public function getRequestData(): ?array;

    /**
     * @param array $credentials
     */
    public function setCredentials(array $credentials);

    /**
     * @return ?array
     */
    public function getCredentials(): ?array;
}
