<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Service\Stock\Data;

use Exception;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Module\Manager as ModuleManager;

/**
 * Service class for stock data
 */
class Collector
{
    public const REQUIRE = [
        'entity_ids'
    ];

    /**
     * @var ResourceConnection
     */
    private $resource;
    /**
     * @var array[]
     */
    private $entityIds;
    /**
     * @var ModuleManager
     */
    private $moduleManager;
    /**
     * @var string
     */
    private $linkField;

    /**
     * Price constructor.
     *
     * @param ResourceConnection $resource
     * @param ModuleManager $moduleManager
     * @param MetadataPool $metadataPool
     * @throws Exception
     */
    public function __construct(
        ResourceConnection $resource,
        ModuleManager $moduleManager,
        MetadataPool $metadataPool
    ) {
        $this->resource = $resource;
        $this->moduleManager = $moduleManager;
        $this->linkField = $metadataPool->getMetadata(ProductInterface::class)->getLinkField();
    }

    /**
     * Get stock data
     *
     * @param array[] $entityIds
     * @return array[]
     */
    public function execute(array $entityIds = []): array
    {
        $this->setData('entity_ids', $entityIds);
        return ($this->moduleManager->isEnabled('Magento_Inventory'))
            ? $this->getMsiStock()
            : $this->getNoMsiStock();
    }

    /**
     * @param string $type
     * @param mixed $data
     */
    public function setData(string $type, $data)
    {
        if (!$data) {
            return;
        }
        if ($type == 'entity_ids') {
            $this->entityIds = $data;
        }
    }

    /**
     * Collect MSI stock data
     *
     * @return array[]
     */
    private function getMsiStock(): array
    {
        $channel = 1;
        $select = $this->resource->getConnection()->select()
            ->from(
                ['cpe' => $this->resource->getTableName('catalog_product_entity')],
                []
            )->where('cpe.entity_id IN (?)', $this->entityIds);

        $table = sprintf('inventory_stock_%s', (int)$channel);
        $select->joinLeft(
            [$table => $this->resource->getTableName($table)],
            "cpe.sku = {$table}.sku",
            [
                'stock' => "{$table}.quantity",
                'sku' => "{$table}.sku"
            ]
        );

        return $this->reformatStockArray(
            $this->resource->getConnection()->fetchAll($select)
        );
    }

    /**
     * @param $stockData
     * @return array[]
     */
    private function reformatStockArray($stockData): array
    {
        $updatedSkus = [];
        $requestData = [];

        foreach ($stockData as $row) {
            $requestData[] = ['sku' => $row['sku'], 'stock' => (int)$row['stock']];
            $updatedSkus[] = $row['sku'];
        }

        return [$requestData, $updatedSkus];
    }

    /**
     * Get stock qty for products without MSI
     * @return array[]
     */
    private function getNoMsiStock(): array
    {
        $select = $this->resource->getConnection()
            ->select()
            ->from(
                ['cataloginventory_stock_item' => $this->resource->getTableName('cataloginventory_stock_item')],
                [
                    'stock' => 'qty'
                ]
            )->joinLeft(
                ['catalog_product_entity' => $this->resource->getTableName('catalog_product_entity')],
                "catalog_product_entity.{$this->linkField} = cataloginventory_stock_item.product_id",
                ['sku']
            )->where('cataloginventory_stock_item.product_id IN (?)', $this->entityIds);

        return $this->reformatStockArray(
            $this->resource->getConnection()->fetchAll($select)
        );
    }

    /**
     * @return string[]
     */
    public function getRequiredParameters(): array
    {
        return self::REQUIRE;
    }

    /**
     * @param string $type
     */
    public function resetData(string $type = 'all')
    {
        if ($type == 'all') {
            unset($this->entityIds);
        }
        if ($type == 'entity_ids') {
            unset($this->entityIds);
        }
    }
}
