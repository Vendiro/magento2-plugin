<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Block\Info;

use Magento\Payment\Block\Info;

/**
 * Payment info block
 */
class Vendiro extends Info
{

    /**
     * @var string
     */
    protected $_template = 'Vendiro_Connect::info/vendiro.phtml';
}
