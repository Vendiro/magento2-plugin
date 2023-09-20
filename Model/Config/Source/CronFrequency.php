<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * CronFrequency Option Source model
 */
class CronFrequency implements OptionSourceInterface
{

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => null,
                'label' => __('--None / Disabled')
            ],
            [
                'value' => '0 0 * * *',
                'label' => __('Daily at 0:00')
            ],
            [
                'value' => '0 3 * * *',
                'label' => __('Daily at 3:00')
            ],
            [
                'value' => '0 6 * * *',
                'label' => __('Daily at 6:00')
            ],
            [
                'value' => '0 */6 * * *',
                'label' => __('Every 6 hours')
            ],
            [
                'value' => '0 */4 * * *',
                'label' => __('Every 4 hours')
            ],
            [
                'value' => '0 */2 * * *',
                'label' => __('Every 2 hours')
            ],
            [
                'value' => '0 * * * *',
                'label' => __('Every hour')
            ],
            [
                'value' => '*/30 * * * *',
                'label' => __('Every 30 minutes')
            ],
            [
                'value' => '*/15 * * * *',
                'label' => __('Every 15 minutes')
            ],
            [
                'value' => '*/5 * * * *',
                'label' => __('Every 5 minutes')
            ]
        ];
    }
}
