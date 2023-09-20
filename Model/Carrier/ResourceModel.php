<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Model\Carrier;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ResourceModel extends AbstractDb
{
    /**
     * Table name
     */
    public const ENTITY_TABLE = 'vendiro_carrier';

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
}
