<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Service\Stock;

use Exception;
use Magento\Framework\App\ResourceConnection;

/**
 * Set products stock service class
 */
class Set
{

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * Repository constructor.
     * @param ResourceConnection $resourceConnection
     * @throws Exception
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param $ids
     */
    public function execute($ids)
    {
        $connection = $this->resourceConnection->getConnection();
        $connection->beginTransaction();

        // get all id's that already in vendiro_stock table
        $indexedIds = $connection->fetchCol(
            $connection->select()->from(
                $this->resourceConnection->getTableName('vendiro_stock'),
                ['entity_id']
            )
        );

        $select = $connection->select()->from(
            ['cpe' => $this->resourceConnection->getTableName('catalog_product_entity')],
            ['entity_id', 'updated_at', 'sku']
        );

        if ($ids) {
            // fetch assoc array of requested product ids
            $dataArray = $connection->fetchAssoc(
                $select->where('cpe.entity_id in (?)', $ids)
            );
        } else {
            if ($indexedIds) {
                // fetch assoc array of product ids that not in the vendiro_stock table
                $dataArray = $connection->fetchAssoc(
                    $select->where('cpe.entity_id not in (?)', $indexedIds)
                );
            } else {
                // fetch assoc array of all products
                $dataArray = $connection->fetchAssoc(
                    $select
                );
            }
        }

        foreach ($dataArray as $data) {
            if (in_array($data['entity_id'], $indexedIds)) {
                $connection->update(
                    $this->resourceConnection->getTableName('vendiro_stock'),
                    [
                        'product_updated_at' => $data['updated_at'],
                        'needs_update' => 1
                    ],
                    ['entity_id = ?' => $data['entity_id']]
                );
            } else {
                $connection->insert(
                    $this->resourceConnection->getTableName('vendiro_stock'),
                    [
                        'entity_id' => $data['entity_id'],
                        'product_sku' => $data['sku'],
                        'product_updated_at' => $data['updated_at'],
                        'needs_update' => 1
                    ]
                );
            }
        }
        $connection->commit();
    }
}
