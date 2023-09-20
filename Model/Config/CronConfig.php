<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Model\Config;

use Magento\Config\Model\ResourceModel\Config as ConfigWriter;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

class CronConfig extends Value
{
    /** Cron string path */
    public const CRON_STRING_PATH = 'crontab/default/jobs/%s/schedule/cron_expr';

    /**
     * @var ConfigWriter
     */
    private $configWriter;

    /**
     * CronConfig constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param AbstractResource|null $resource
     * @param ConfigWriter $configWriter
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        ConfigWriter $configWriter,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->configWriter = $configWriter;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * @inheritdoc
     */
    public function afterSave()
    {
        $cron = str_replace('%s', $this->getData('field'), CronConfig::CRON_STRING_PATH);
        $this->configWriter->saveConfig($cron, $this->getValue());
        return parent::afterSave();
    }
}
