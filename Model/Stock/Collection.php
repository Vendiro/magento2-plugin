<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Model\Stock;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            Data::class,
            ResourceModel::class
        );
    }

    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->joinLeft(
            ['catalog_product_entity' => $this->getTable('catalog_product_entity')],
            'main_table.entity_id = catalog_product_entity.entity_id',
            [
                'sku',
            ]
        );
    }
}
