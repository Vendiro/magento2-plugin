<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Api\Log;

/**
 * Config repository interface
 * @api
 */
interface RepositoryInterface
{

    /**
     * Add record to error log
     *
     * @param string $type
     * @param $data
     * @return void
     */
    public function addErrorLog(string $type, $data): void;

    /**
     * Add record to debug log
     *
     * @param string $type
     * @param mixed $data
     * @return void
     */
    public function addDebugLog(string $type, $data): void;
}
