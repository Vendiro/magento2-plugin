<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Cron;

use Vendiro\Connect\Api\Config\RepositoryInterface as ConfigProvider;
use Vendiro\Connect\Service\Invoice\Data as InvoiceService;

/**
 * Import Order Cron
 */
class Invoice
{
    /**
     * @var InvoiceService
     */
    private $invoiceService;
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @param InvoiceService $invoiceService
     * @param ConfigProvider $configProvider
     */
    public function __construct(
        InvoiceService $invoiceService,
        ConfigProvider $configProvider
    ) {
        $this->invoiceService = $invoiceService;
        $this->configProvider = $configProvider;
    }

    /**
     * @return void
     */
    public function sendInvoices()
    {
        if (!$this->isEnabled()) {
            return;
        }

        $this->invoiceService->sendInvoice();
    }

    /**
     * @return bool
     */
    private function isEnabled(): bool
    {
        return $this->configProvider->isEnabled() && $this->configProvider->uploadInvoices();
    }
}
