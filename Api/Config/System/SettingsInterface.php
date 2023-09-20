<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Api\Config\System;

/**
 * Config Settings interface
 * @api
 */
interface SettingsInterface
{

    /** General Group */
    public const XPATH_MODE = 'vendiro/general/mode';
    public const XPATH_VERSION = 'vendiro/general/version';
    public const XPATH_KEY = 'vendiro/general/api_key';
    public const XPATH_TOKEN = 'vendiro/general/api_token';

    /** Settings Group */
    public const XPATH_IMPORT_ORDERS = 'vendiro/settings/import_orders';
    public const XPATH_CONFIRM_SHIPMENT = 'vendiro/settings/confirm_shipment';
    public const XPATH_UPDATE_INVENTORY = 'vendiro/settings/update_inventory';
    public const XPATH_UPLOAD_INVOICE = 'vendiro/settings/upload_invoice';
    public const XPATH_SHIPPING_METHOD = 'vendiro/settings/default_shipment_method';

    /** Advanced Group */
    public const XPATH_DISABLE_REJECT = 'vendiro/advanced/disable_reject_orders';

    /** Limits Group */
    public const XPATH_API_LIMITS = 'vendiro/limits/%s';

    /** Debug Group */
    public const XPATH_DEBUG = 'vendiro/debug/enabled';

    /**
     * Enable flag
     *
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * Is connection set to live mode
     *
     * @return bool
     */
    public function isLiveMode(): bool;

    /**
     * Is connection set to live mode
     *
     * @return bool
     */
    public function isTestMode(): bool;

    /**
     * Credentials array
     *
     * @return array
     */
    public function getCredentials(): array;

    /**
     * Import orders flag
     *
     * @return bool
     */
    public function importOrders(): bool;

    /**
     * Confirm shipment flag
     *
     * @return bool
     */
    public function confirmShipments(): bool;

    /**
     * Is auto rejection enabled
     *
     * @return bool
     */
    public function disableReject(): bool;

    /**
     * Upload Invoices flag
     *
     * @return bool
     */
    public function uploadInvoices(): bool;

    /**
     * Update inventory flag
     *
     * @return bool
     */
    public function updateInventory(): bool;

    /**
     * Returns default carrier code
     * @see \Vendiro\Connect\Model\Config\Source\Carriers
     *
     * @return string|null
     */
    public function getDefaultCarrier(): ?string;

    /**
     * Returns limit for API calls
     *
     * @param string $type
     * @return int
     */
    public function getLimit(string $type): int;

    /**
     * Catalog Prices included tax flag
     *
     * @param int|null $storeId
     * @return bool
     */
    public function getPriceIncludedTax(int $storeId = null): bool;

    /**
     * Shipping Prices included tax flag
     *
     * @param int|null $storeId
     * @return bool
     */
    public function getShippingIncludedTax(int $storeId = null): bool;

    /**
     * Shipping Tax Class ID
     *
     * @param int|null $storeId
     *
     * @return string
     */
    public function getShippingTaxClass(int $storeId = null): string;

    /**
     * Default customer Tax Class ID
     *
     * @param int|null $storeId
     *
     * @return string
     */
    public function getDefaultCustomerTaxClass(int $storeId = null): string;

    /**
     * Log API calls to debug file flag
     *
     * @return bool
     */
    public function logDebug(): bool;
}
