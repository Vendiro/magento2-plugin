<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Service\Order;

use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\LocalizedException;
use Vendiro\Connect\Api\Log\RepositoryInterface as LogRepository;
use Vendiro\Connect\Api\Order\RepositoryInterface as OrderRepositoryInterface;
use Vendiro\Connect\Exception;
use Vendiro\Connect\Model\Config\Source\QueueStatus;

class Import
{

    public const IMPORT_ERROR = 'Order #%s: Could not be imported: "%s"';
    public const SAVE_ERROR = 'Order #%s: Could not saved: "%s.';
    public const ALREADY_IMPORTED = 'Order #%s (already imported): %s';
    public const IMPORTED = 'Order #%s: successfully imported';
    public const NO_UPDATES = 'No orders found to update';

    public $importResult = [];

    /**
     * @var ApiStatusManager
     */
    private $apiStatusManager;
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var Create
     */
    private $createOrder;
    /**
     * @var LogRepository
     */
    private $logger;

    /**
     * @param ApiStatusManager $apiStatusManager
     * @param OrderRepositoryInterface $orderRepository
     * @param Create $createOrder
     * @param LogRepository $logger
     */
    public function __construct(
        ApiStatusManager $apiStatusManager,
        OrderRepositoryInterface $orderRepository,
        Create $createOrder,
        LogRepository $logger
    ) {
        $this->apiStatusManager = $apiStatusManager;
        $this->orderRepository = $orderRepository;
        $this->createOrder = $createOrder;
        $this->logger = $logger;
    }

    /**
     * @param int|null $limit
     * @param string|null $forceStore
     * @param bool $forceExisting
     * @return array
     * @throws AuthenticationException
     */
    public function execute(?int $limit = null, ?string $forceStore = null, bool $forceExisting = false): array
    {
        try {
            $orders = $this->apiStatusManager->getOrders($limit);
        } catch (\Exception $exception) {
            $this->importResult['error'][] = $exception->getMessage();
            $this->logger->addErrorLog('ImportOrder', $exception->getMessage());
            return $this->importResult;
        }

        $orderIds = array_column($orders, 'id');
        if (empty($orderIds)) {
            $this->importResult['error'][] = self::NO_UPDATES;
            return $this->importResult;
        }

        foreach ($orders as $order) {
            try {
                $this->createOrder($order, $forceStore, $forceExisting);
            } catch (\Exception $exception) {
                $errorMsg = sprintf(self::IMPORT_ERROR, $order['id'], $exception->getMessage());
                $this->logger->addErrorLog('createOrder', $errorMsg);
                $this->importResult['error'][] = $errorMsg;
                $this->apiStatusManager->rejectOrder($order['id'], $exception->getMessage());
            }
        }

        return $this->importResult;
    }

    /**
     * @param array $order
     * @param string|null $forceStore
     * @param bool $forceExisting
     * @return void
     * @throws AuthenticationException
     * @throws Exception
     * @throws LocalizedException
     */
    private function createOrder(array $order, ?string $forceStore, bool $forceExisting = false)
    {
        $vendiroOrder = $this->orderRepository->getByDataSet(['vendiro_id' => $order['id']], true);
        if ($vendiroOrder->getEntityId() && !$forceExisting) {
            $result = $this->apiStatusManager->acceptOrder($order['id'], $vendiroOrder->getOrderId());
            $this->importResult['success'][] = sprintf(
                self::ALREADY_IMPORTED,
                $order['id'],
                $result['message'] ?? ''
            );
            return;
        }

        if ($forceStore) {
            $order['marketplace']['reference'] = $forceStore;
        }

        $newOrderId = $this->createOrder->execute($order);
        $this->importResult['success'][] = sprintf(self::IMPORTED, $order['id']);
        $this->apiStatusManager->acceptOrder($order['id'], $newOrderId);

        if ($newOrderId) {
            $this->saveOrder($newOrderId, $order);
        }
    }

    /**
     * @param string $newOrderId
     * @param array $order
     * @return void
     */
    private function saveOrder(string $newOrderId, array $order)
    {
        $vendiroOrder = $this->orderRepository->create()
            ->setOrderId($newOrderId)
            ->setVendiroId($order['id'])
            ->setMarketplaceOrderId($order['marketplace_order_id'])
            ->setMarketplaceName($order['marketplace']['name'])
            ->setMarketplaceReference($order['marketplace']['reference'])
            ->setMarketplaceId($order['marketplace']['id'])
            ->setStatus(QueueStatus::QUEUE_STATUS_IMPORTED);

        try {
            $this->orderRepository->save($vendiroOrder);
        } catch (\Exception $exception) {
            $errorMessage = sprintf(self::SAVE_ERROR, $vendiroOrder['id'], $exception->getMessage());
            $this->logger->addErrorLog('saveVendiroOrder', $errorMessage);
        }
    }
}
