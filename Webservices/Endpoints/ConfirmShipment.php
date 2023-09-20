<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Webservices\Endpoints;

class ConfirmShipment extends AbstractEndpoint
{
    public const ENDPOINT_URL = 'orders/%s/shipment/?id_type=order_ref';
    public const METHOD = 'PUT';
}
