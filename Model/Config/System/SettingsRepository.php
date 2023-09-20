<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Model\Config\System;

use Magento\Tax\Model\Config as TaxConfig;
use Vendiro\Connect\Api\Config\System\SettingsInterface;
use Vendiro\Connect\Model\Config\Source\Mode;

/**
 * Config Settings provider class
 */
class SettingsRepository extends BaseRepository implements SettingsInterface
{

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        return $this->isLiveMode() || $this->isTestMode();
    }

    /**
     * @inheritDoc
     */
    public function isLiveMode(): bool
    {
        return $this->getMode() == Mode::LIVE;
    }

    /**
     * Get saved integration model
     *
     * @return int
     */
    private function getMode(): int
    {
        return (int)$this->getStoreValue(static::XPATH_MODE);
    }

    /**
     * @inheritDoc
     */
    public function isTestMode(): bool
    {
        return $this->getMode() == Mode::TEST;
    }

    /**
     * @inheritDoc
     */
    public function getCredentials(): array
    {
        return [
            'key' => $this->getKey(),
            'token' => $this->getToken()
        ];
    }

    /**
     * Get saved key
     *
     * @return string|null
     */
    private function getKey(): ?string
    {
        if ($value = $this->getStoreValue(static::XPATH_KEY)) {
            return $this->encryptor->decrypt($value);
        }

        return null;
    }

    /**
     * Get saved token
     *
     * @return string|null
     */
    private function getToken(): ?string
    {
        if ($value = $this->getStoreValue(static::XPATH_TOKEN)) {
            return $this->encryptor->decrypt($value);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function importOrders(): bool
    {
        return $this->isSetFlag(static::XPATH_IMPORT_ORDERS);
    }

    /**
     * @inheritDoc
     */
    public function confirmShipments(): bool
    {
        return $this->isSetFlag(static::XPATH_CONFIRM_SHIPMENT);
    }

    /**
     * @inheritDoc
     */
    public function uploadInvoices(): bool
    {
        return $this->isSetFlag(static::XPATH_UPLOAD_INVOICE);
    }

    /**
     * @inheritDoc
     */
    public function getDefaultCarrier(): ?string
    {
        return $this->getStoreValue(static::XPATH_SHIPPING_METHOD);
    }

    /**
     * @inheritDoc
     */
    public function updateInventory(): bool
    {
        return $this->isSetFlag(static::XPATH_UPDATE_INVENTORY);
    }

    /**
     * @inheritDoc
     */
    public function getLimit(string $type): int
    {
        return (int)$this->getStoreValue(sprintf(self::XPATH_API_LIMITS, $type));
    }

    /**
     * @inheritDoc
     */
    public function logDebug(): bool
    {
        return $this->isSetFlag(static::XPATH_DEBUG);
    }

    /**
     * @inheritDoc
     */
    public function disableReject(): bool
    {
        return $this->isSetFlag(static::XPATH_DISABLE_REJECT);
    }

    /**
     * @inheritDoc
     */
    public function getPriceIncludedTax(int $storeId = null): bool
    {
        return $this->isSetFlag(TaxConfig::CONFIG_XML_PATH_PRICE_INCLUDES_TAX, (int)$storeId);
    }

    /**
     * @inheritDoc
     */
    public function getShippingIncludedTax(int $storeId = null): bool
    {
        return $this->isSetFlag(TaxConfig::CONFIG_XML_PATH_SHIPPING_INCLUDES_TAX, (int)$storeId);
    }

    /**
     * @inheritDoc
     */
    public function getShippingTaxClass(int $storeId = null): string
    {
        return $this->getStoreValue(TaxConfig::CONFIG_XML_PATH_SHIPPING_TAX_CLASS, (int)$storeId);
    }

    /**
     * @inheritDoc
     */
    public function getDefaultCustomerTaxClass(int $storeId = null): string
    {
        return $this->getStoreValue('tax/classes/default_customer_tax_class', (int)$storeId);
    }
}
