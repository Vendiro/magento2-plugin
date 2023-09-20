<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Service\Order;

use Magento\Framework\Exception\AuthenticationException;
use Vendiro\Connect\Webservices\Endpoints\AcceptOrder;
use Vendiro\Connect\Webservices\Endpoints\GetOrders;
use Vendiro\Connect\Webservices\Endpoints\RejectOrder;
use Vendiro\Connect\Api\Config\RepositoryInterface as ConfigProvider;

class ApiStatusManager
{
    /**
     * @var GetOrders
     */
    private $getOrders;
    /**
     * @var AcceptOrder
     */
    private $acceptOrder;
    /**
     * @var RejectOrder
     */
    private $rejectOrder;
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @param GetOrders $getOrders
     * @param AcceptOrder $acceptOrder
     * @param RejectOrder $rejectOrder
     * @param ConfigProvider $configProvider
     */
    public function __construct(
        GetOrders $getOrders,
        AcceptOrder $acceptOrder,
        RejectOrder $rejectOrder,
        ConfigProvider $configProvider
    ) {
        $this->getOrders = $getOrders;
        $this->acceptOrder = $acceptOrder;
        $this->rejectOrder = $rejectOrder;
        $this->configProvider = $configProvider;
    }

    /**
     * @param int|null $limit
     * @return array
     * @throws AuthenticationException
     */
    public function getOrders(?int $limit = null): array
    {
        $limit = $limit ?? $this->configProvider->getLimit('orders');
        $requestData = ['order_status' => 'new', 'include_addresses' => 'true', 'limit' => $limit];
        $this->getOrders->setRequestData($requestData);
        return $this->getOrders->call();
    }

    /**
     * @param $vendiroOrderId
     * @param $magentoOrderId
     * @return array
     * @throws AuthenticationException
     */
    public function acceptOrder($vendiroOrderId, $magentoOrderId): array
    {
        $requestData = ['order_ref' => $magentoOrderId];
        $this->acceptOrder->setRequestData($requestData);
        return $this->acceptOrder->call($vendiroOrderId);
    }

    /**
     * @param $vendiroOrderId
     * @param $reason
     * @return array
     * @throws AuthenticationException
     */
    public function rejectOrder($vendiroOrderId, $reason): array
    {
        if ($this->configProvider->disableReject()) {
            return [];
        }

        $requestData = ['reason' => $reason];
        $this->rejectOrder->setRequestData($requestData);
        return $this->rejectOrder->call($vendiroOrderId);
    }
}
