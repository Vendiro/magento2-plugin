<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Api\Config;

use Magento\Store\Api\Data\StoreInterface;

/**
 * Config repository interface
 * @api
 */
interface RepositoryInterface extends System\SettingsInterface
{

    public const EXTENSION_CODE = 'Vendiro_Connect';
    public const XPATH_API_BASE_URL = 'vendiro/endpoints/api_base_url';
    public const XPATH_TEST_API_BASE_URL = 'vendiro/endpoints/test_api_base_url';

    /**
     * Get extension version
     *
     * @return string
     */
    public function getExtensionVersion(): string;

    /**
     * Get extension code
     *
     * @return string
     */
    public function getExtensionCode(): string;

    /**
     * Get Magento Version
     *
     * @return string
     */
    public function getMagentoVersion(): string;

    /**
     * Get Base url of API
     *
     * @return mixed
     */
    public function getApiBaseUrl(): string;

    /**
     * Get store
     *
     * @param null $storeId
     * @return StoreInterface
     */
    public function getStore($storeId = null): StoreInterface;
}
