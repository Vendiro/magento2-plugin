<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Model\Payment;

use Magento\Payment\Model\Method\AbstractMethod;

class Vendiro extends AbstractMethod
{
    public const PAYMENT_CODE = 'vendiro';

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = self::PAYMENT_CODE;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isOffline = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canUseInternal = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canUseCheckout = false;

    /**
     * @var string
     */
    protected $_infoBlockType = \Vendiro\Connect\Block\Info\Vendiro::class;
}
