<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Model\Log;

use Vendiro\Connect\Api\Config\RepositoryInterface as ConfigProvider;
use Vendiro\Connect\Api\Log\RepositoryInterface as LogRepositoryInterface;
use Vendiro\Connect\Logger\DebugLogger;
use Vendiro\Connect\Logger\ErrorLogger;

/**
 * Logs repository class
 */
class Repository implements LogRepositoryInterface
{

    private const SKIP_DEBUG_FIELDS = ['data'];

    /**
     * @var DebugLogger
     */
    private $debugLogger;
    /**
     * @var ErrorLogger
     */
    private $errorLogger;
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @param DebugLogger $debugLogger
     * @param ErrorLogger $errorLogger
     * @param ConfigProvider $configProvider
     */
    public function __construct(
        DebugLogger $debugLogger,
        ErrorLogger $errorLogger,
        ConfigProvider $configProvider
    ) {
        $this->debugLogger = $debugLogger;
        $this->errorLogger = $errorLogger;
        $this->configProvider = $configProvider;
    }

    /**
     * @inheritDoc
     */
    public function addErrorLog(string $type, $data): void
    {
        $this->errorLogger->addLog($type, $data);
    }

    /**
     * @inheritDoc
     */
    public function addDebugLog(string $type, $data): void
    {
        if ($this->configProvider->logDebug()) {
            $this->debugLogger->addLog($type, $this->cleanDataArray($data));
        }
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    private function cleanDataArray($data)
    {
        if (!is_array($data)) {
            return $data;
        }

        foreach (self::SKIP_DEBUG_FIELDS as $key) {
            if (isset($data[$key])) {
                unset($data[$key]);
            }
        }

        return $data;
    }
}
