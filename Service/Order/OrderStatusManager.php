<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Service\Order;

use Magento\Framework\DB\Transaction;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Model\Convert\Order as OrderConverter;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Config as OrderConfig;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Service\InvoiceService;
use Vendiro\Connect\Api\Log\RepositoryInterface as LogRepository;
use Vendiro\Connect\Model\Order\Status\HistoryRepository;

class OrderStatusManager
{

    /**
     * @var HistoryRepository
     */
    private $historyRepository;
    /**
     * @var Transaction
     */
    private $transaction;
    /**
     * @var LogRepository
     */
    private $logger;
    /**
     * @var InvoiceService
     */
    private $invoiceService;
    /**
     * @var OrderConverter
     */
    private $orderConverter;
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var OrderConfig
     */
    private $orderConfig;
    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @param HistoryRepository $historyRepository
     * @param Transaction $transaction
     * @param InvoiceService $invoiceService
     * @param OrderConverter $orderConverter
     * @param OrderConfig $orderConfig
     * @param OrderRepositoryInterface $orderRepository
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param LogRepository $logger
     */
    public function __construct(
        HistoryRepository $historyRepository,
        Transaction $transaction,
        InvoiceService $invoiceService,
        OrderConverter $orderConverter,
        OrderConfig $orderConfig,
        OrderRepositoryInterface $orderRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        LogRepository $logger
    ) {
        $this->historyRepository = $historyRepository;
        $this->transaction = $transaction;
        $this->invoiceService = $invoiceService;
        $this->logger = $logger;
        $this->orderConverter = $orderConverter;
        $this->orderConfig = $orderConfig;
        $this->orderRepository = $orderRepository;
        $this->shipmentRepository = $shipmentRepository;
    }

    /**
     * @param OrderInterface $order
     * @param string $comment
     */
    public function createComment(OrderInterface $order, string $comment)
    {
        $orderHistoryComment = $this->historyRepository->create()
            ->setComment($comment)
            ->setParentId($order->getId())
            ->setStatus($order->getStatus())
            ->setEntityName('order');

        try {
            $this->historyRepository->save($orderHistoryComment);
        } catch (CouldNotSaveException $exception) {
            $this->logger->addErrorLog(
                'Vendiro add history comment went wrong: ' . $exception->getMessage(),
                ['orderId' => $order->getId()]
            );
        }
    }

    /**
     * @param OrderInterface $order
     * @param array $vendiroOrder
     * @return void
     */
    public function createInvoice(OrderInterface $order, array $vendiroOrder)
    {
        if (!$order->canInvoice()) {
            return;
        }

        $order->setData('vendiro_discount', $vendiroOrder['discount']);
        $this->registerInvoiceAndTransaction($order);
    }

    /**
     * @param OrderInterface|Order $order
     */
    private function registerInvoiceAndTransaction($order)
    {
        try {
            $invoice = $this->invoiceService->prepareInvoice($order);
            $invoice->setRequestedCaptureCase(Invoice::CAPTURE_OFFLINE);
            $invoice->register();

            $this->transaction->addObject($invoice);

            $order->setState(Order::STATE_PROCESSING);
            $order->setStatus(
                $this->orderConfig->getStateDefaultStatus(Order::STATE_PROCESSING) ?: Order::STATE_PROCESSING
            );

            $this->transaction->addObject($order)->save();
        } catch (\Exception $exception) {
            $message = 'Could not create an invoice for order #' . $order->getId() . ': ' . $exception->getMessage();
            $this->logger->addErrorLog('createInvoice', $message);
        }
    }

    /**
     * @param OrderInterface $order
     * @param array $vendiroOrder
     * @return void
     * @throws LocalizedException
     */
    public function createShipment(OrderInterface $order, array $vendiroOrder)
    {
        if (!$order->canShip()) {
            return;
        }

        if (empty($vendiroOrder['fulfilment_by_marketplace']) || $vendiroOrder['fulfilment_by_marketplace'] != 'true') {
            return;
        }

        try {
            $shipment = $this->orderConverter->toShipment($order);
            foreach ($order->getAllItems() as $orderItem) {
                if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                    continue;
                }
                $qtyShipped = $orderItem->getQtyToShip();
                $shipmentItem = $this->orderConverter->itemToShipmentItem($orderItem)->setQty($qtyShipped);
                $shipment->addItem($shipmentItem);
            }

            $shipment->register();
            $shipment->getOrder()->setIsInProcess(true);

            if (method_exists($shipment->getExtensionAttributes(), 'setSourceCode')) {
                $shipment->getExtensionAttributes()->setSourceCode('default');
            }

            $this->shipmentRepository->save($shipment);
            $this->orderRepository->save($shipment->getOrder());
        } catch (\Exception $exception) {
            $message = 'Could not create an shipment for order #' . $order->getId() . ': ' . $exception->getMessage();
            $this->logger->addErrorLog('createShipment', $message);
        }
    }
}
