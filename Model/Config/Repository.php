<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Model\Config;

use Vendiro\Connect\Api\Config\RepositoryInterface as ConfigRepositoryInterface;

/**
 * Config repository class
 */
class Repository extends System\SettingsRepository implements ConfigRepositoryInterface
{

    /**
     * {@inheritDoc}
     */
    public function getExtensionVersion(): string
    {
        return preg_replace(
            "/[^0-9.]/",
            '',
            (string)$this->getStoreValue(self::XPATH_VERSION)
        );
    }
    /**
     * @inheritDoc
     */
    public function getMagentoVersion(): string
    {
        return $this->metadata->getVersion();
    }

    /**
     * @inheritDoc
     */
    public function getExtensionCode(): string
    {
        return self::EXTENSION_CODE;
    }

    /**
     * @inheritDoc
     */
    public function getApiBaseUrl(): string
    {
        if ($this->isLiveMode()) {
            return $this->getStoreValue(static::XPATH_API_BASE_URL);
        }

        return $this->getStoreValue(static::XPATH_TEST_API_BASE_URL);
    }
}
