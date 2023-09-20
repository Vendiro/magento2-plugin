<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Plugin\Order;

use Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection;

class GridCollection
{

    /**
     * @param CollectionFactory $subject
     * @param Collection $collection
     * @param string $requestName
     *
     * @return Collection
     */
    public function afterGetReport($subject, $collection, $requestName)
    {
        if ($requestName !== 'sales_order_grid_data_source') {
            return $collection;
        }

        if ($collection->getMainTable() === $collection->getResource()->getTable('sales_order_grid')) {
            $joinTable = $collection->getResource()->getTable('vendiro_order');
            $collection->getSelect()->joinLeft(
                ['vendiro_order' => $joinTable],
                'main_table.entity_id = vendiro_order.order_entity_id',
                [
                    'vendiro_id' => 'vendiro_order.vendiro_id',
                    'vendiro_marketplace_order_id' => 'vendiro_order.marketplace_order_id',
                    'vendiro_marketplace_name' => 'vendiro_order.marketplace_name',
                    'vendiro_marketplace_reference' => 'vendiro_order.marketplace_reference',
                ]
            );
        }
        return $collection;
    }
}
