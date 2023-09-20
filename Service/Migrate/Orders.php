<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Service\Migrate;

use Magento\Framework\App\ResourceConnection;

class Orders
{

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @return array
     */
    public function execute(): array
    {
        $connection = $this->resourceConnection->getConnection();
        $oldTable = $connection->getTableName('tig_vendiro_order');
        $newTable = $connection->getTableName('vendiro_order');

        if (!$connection->isTableExists($oldTable)) {
            return ['success' => false, 'message' => 'Old tig_vendiro_order table not found'];
        }

        $select = $connection->select()
            ->from(
                ['tig_vendiro_order' => $oldTable]
            )->joinLeft(
                ['vendiro_order' => $newTable],
                'tig_vendiro_order.order_id = vendiro_order.order_id',
                []
            )->where(
                'vendiro_order.entity_id is NULL'
            );

        $oldOrders = $connection->fetchAssoc($select);
        foreach ($oldOrders as $key => $oldOrder) {
            $oldOrders[$key]['marketplace_order_id'] = $oldOrder['marketplace_orderid'];
            $oldOrders[$key]['imported_at'] = $oldOrder['created_at'];
            unset($oldOrders[$key]['marketplace_orderid']);
            unset($oldOrders[$key]['created_at']);
        }

        $count = !empty($oldOrders) ? $connection->insertMultiple($newTable, $oldOrders) : 0;

        return [
            'success' => $count > 0,
            'message' => __('%1 order(s) migrated', $count > 0 ? $count : 'No')
        ];
    }
}
