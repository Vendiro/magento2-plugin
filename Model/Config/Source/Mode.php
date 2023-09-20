<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Mode implements OptionSourceInterface
{

    public const DISABLED = 0;
    public const LIVE = 1;
    public const TEST = 2;

    public const MODES = [
        self::DISABLED => 'Off',
        self::LIVE => 'Live',
        self::TEST => 'Test',
    ];

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        $options = [];
        foreach (self::MODES as $k => $v) {
            $options[] = [
                'value' => $k,
                'label' => __($v)
            ];
        }

        return $options;
    }
}
