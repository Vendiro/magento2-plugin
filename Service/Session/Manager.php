<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Service\Session;

use Magento\Framework\Session\SessionManagerInterface as CoreSession;

class Manager
{

    /**
     * @var CoreSession
     */
    private $coreSession;

    /**
     * @param CoreSession $coreSession
     */
    public function __construct(
        CoreSession $coreSession
    ) {
        $this->coreSession = $coreSession;
    }

    /**
     * @return bool
     */
    public function isVendiroOrder(): bool
    {
        return (bool)$this->coreSession->getIsVendiroOrder();
    }

    /**
     * @return void
     */
    public function setIsVendiroOrder()
    {
        $this->coreSession->setIsVendiroOrder(true);
    }

    /**
     * @return void
     */
    public function unsIsVendiroOrder()
    {
        $this->coreSession->unsIsVendiroOrder();
    }
}
