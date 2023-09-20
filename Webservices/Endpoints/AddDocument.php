<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Webservices\Endpoints;

class AddDocument extends AbstractEndpoint
{
    public const ENDPOINT_URL = 'orders/%s/document/?id_type=order_ref';
    public const METHOD = 'PUT';
}
