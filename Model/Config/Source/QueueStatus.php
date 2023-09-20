<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Model\Config\Source;

class QueueStatus
{
    public const QUEUE_STATUS_NEW = 'new';
    public const QUEUE_STATUS_IMPORTED = 'imported';
    public const QUEUE_STATUS_SHIPMENT_CREATED = 'shipment_created';
    public const QUEUE_STATUS_STOCK_UPDATED = 'stock_updated';
    public const QUEUE_STATUS_FORCE_STOCK_UPDATE = 'force_stock_update';
    public const QUEUE_STATUS_COMPLETE = 'complete';
    public const QUEUE_STATUS_FAILED = 'failed';
}
