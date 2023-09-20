<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Vendiro\Connect\Service\Migrate\Orders as OrderMigrateService;

/**
 * Setup data patch class to copy old orders
 */
class ImportOldOrders implements DataPatchInterface
{

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var OrderMigrateService
     */
    private $orderMigrateService;

    /**
     * ImportOldOrders constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param OrderMigrateService $orderMigrateService
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        OrderMigrateService $orderMigrateService
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->orderMigrateService = $orderMigrateService;
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @return DataPatchInterface
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->orderMigrateService->execute();
        $this->moduleDataSetup->getConnection()->endSetup();
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }
}
