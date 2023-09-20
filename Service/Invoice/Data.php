<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Service\Invoice;

use Exception;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Sales\Model\Order\Invoice as MagentoInvoice;
use Magento\Sales\Model\Order\Pdf\Invoice;
use Vendiro\Connect\Api\Invoice\RepositoryInterface as InvoiceRepository;
use Vendiro\Connect\Api\Log\RepositoryInterface as LogRepository;
use Vendiro\Connect\Api\Order\DataInterface as OrderData;
use Vendiro\Connect\Api\Order\RepositoryInterface as OrderRepository;
use Vendiro\Connect\Webservices\Endpoints\AddDocument;

class Data
{

    /**
     * @var LogRepository
     */
    private $logRepository;
    /**
     * @var OrderRepository
     */
    private $orderRepository;
    /**
     * @var Validate
     */
    private $validate;
    /**
     * @var Invoice
     */
    private $pdfInvoice;
    /**
     * @var AddDocument
     */
    private $addDocument;
    /**
     * @var InvoiceRepository
     */
    private $invoiceRepository;

    /**
     * @param LogRepository $logRepository
     * @param OrderRepository $orderRepository
     * @param InvoiceRepository $invoiceRepository
     * @param Validate $validate
     * @param Invoice $pdfInvoice
     * @param AddDocument $addDocument
     */
    public function __construct(
        LogRepository $logRepository,
        OrderRepository $orderRepository,
        InvoiceRepository $invoiceRepository,
        Validate $validate,
        Invoice $pdfInvoice,
        AddDocument $addDocument
    ) {
        $this->logRepository = $logRepository;
        $this->orderRepository = $orderRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->validate = $validate;
        $this->pdfInvoice = $pdfInvoice;
        $this->addDocument = $addDocument;
    }

    /**
     * Check if any invoices can be sent to Vendiro, and does so if there are any
     */
    public function sendInvoice()
    {
        $vendiroOrders = $this->orderRepository->getInvoicesToSend();
        if (!$vendiroOrders->getSize()) {
            return;
        }

        foreach ($vendiroOrders as $vendiroOrder) {
            $this->uploadInvoice($vendiroOrder);
        }
    }

    /**
     * @param OrderData $vendiroOrder
     */
    private function uploadInvoice(OrderData $vendiroOrder)
    {
        if (!$invoice = $this->validate->getInvoice($vendiroOrder)) {
            return;
        }

        try {
            $requestData = $this->getInvoiceData($invoice);
        } catch (\Exception $exception) {
            $errorMessage = 'Could not get Invoice PDF: ' . $exception->getMessage();
            $this->logRepository->addErrorLog('uploadInvoice', $errorMessage);
            return;
        }

        try {
            $this->addDocument->setRequestData($requestData);
            $result = $this->addDocument->call($vendiroOrder->getOrderId());
        } catch (\Exception $exception) {
            $errorMessage = sprintf(
                'Vendiro Add Document for order #%s went wrong: %s',
                $vendiroOrder->getEntityId(),
                $exception->getMessage()
            );
            $this->logRepository->addErrorLog('uploadInvoice', $errorMessage);
            return;
        }

        if (!array_key_exists('message', $result)) {
            $this->saveInvoice($vendiroOrder, (int)$invoice->getEntityId());
        } else {
            try {
                $vendiroOrder->setInvoiceId(-1);
                $this->orderRepository->save($vendiroOrder);
            } catch (\Exception $exception) {
                $errorMessage = 'Could not save Vendiro Invoice Data: ' . $exception->getMessage();
                $this->logRepository->addErrorLog('saveInvoice', $errorMessage);
            }
        }
    }

    /**
     * @param MagentoInvoice $invoice
     *
     * @return array
     * @throws Exception
     */
    private function getInvoiceData(MagentoInvoice $invoice): array
    {
        return [
            "reference" => $invoice->getIncrementId(),
            "type" => "invoice",
            "data" => base64_encode($this->pdfInvoice->getPdf([$invoice])->render()),
            "total_value" => $invoice->getGrandTotal(),
            "vat_value" => $invoice->getTaxAmount()
        ];
    }

    /**
     * @param OrderData $vendiroOrder
     * @param int $magentoInvoiceId
     */
    private function saveInvoice(OrderData $vendiroOrder, int $magentoInvoiceId): void
    {
        $vendiroInvoice = $this->invoiceRepository->create()
            ->setInvoiceId($magentoInvoiceId)
            ->setOrderId($vendiroOrder->getVendiroId())
            ->setMarketplaceId($vendiroOrder->getMarketplaceId())
            ->setMarketplaceOrderId($vendiroOrder->getMarketplaceOrderid());

        try {
            $vendiroInvoice = $this->invoiceRepository->save($vendiroInvoice);
            $vendiroOrder->setInvoiceId($vendiroInvoice->getEntityId());
            $this->orderRepository->save($vendiroOrder);
        } catch (CouldNotSaveException $exception) {
            $errorMessage = 'Could not save Vendiro Invoice Data: ' . $exception->getMessage();
            $this->logRepository->addErrorLog('saveInvoice', $errorMessage);
        }
    }
}
