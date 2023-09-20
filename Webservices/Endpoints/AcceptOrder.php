<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Webservices\Endpoints;

class AcceptOrder extends AbstractEndpoint
{
    public const ENDPOINT_URL = 'orders/%s/accept/';
    public const METHOD = 'PUT';
}
