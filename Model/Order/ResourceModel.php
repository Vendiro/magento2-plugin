<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Model\Order;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ResourceModel extends AbstractDb
{
    /**
     * Table name
     */
    public const ENTITY_TABLE = 'vendiro_order';

    /**
     * Primary field name
     */
    public const PRIMARY = 'entity_id';

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(static::ENTITY_TABLE, static::PRIMARY);
    }

    /**
     * @inheritDoc
     */
    public function _beforeSave(AbstractModel $object)
    {
        if ($object->getData('order_id') && !$object->getData('order_entity_id')) {
            $object->setData('order_entity_id', $this->getEntityIdFromOrderId($object->getData('order_id')));
        }

        return parent::_beforeSave($object);
    }

    /**
     * Check is entity exists
     *
     * @param int $primaryId
     * @return bool
     */
    public function isExists(int $primaryId): bool
    {
        $condition = sprintf('%s = :%s', static::PRIMARY, static::PRIMARY);
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable(static::ENTITY_TABLE),
            static::PRIMARY
        )->where($condition);
        $bind = [sprintf(':%s', static::PRIMARY) => $primaryId];
        return (bool)$connection->fetchOne($select, $bind);
    }

    /**
     * @param string $incrementId
     * @return int
     */
    private function getEntityIdFromOrderId(string $incrementId): int
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTable('sales_order'), 'entity_id')
            ->where('increment_id = :increment_id');
        $bind = [':increment_id' => $incrementId];
        return (int)$connection->fetchOne($select, $bind);
    }
}
