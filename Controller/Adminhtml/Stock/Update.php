<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Controller\Adminhtml\Stock;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\ResultFactory;
use Vendiro\Connect\Service\Stock\Update as UpdateStock;

class Update extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Vendiro_Connect::general';

    /**
     * @var UpdateStock
     */
    private $updateStock;
    /**
     * @var RedirectInterface
     */
    private $redirect;

    /**
     * @param Action\Context $context
     * @param UpdateStock $updateStock
     * @param RedirectInterface $redirect
     */
    public function __construct(
        Action\Context $context,
        UpdateStock $updateStock,
        RedirectInterface $redirect
    ) {
        $this->updateStock = $updateStock;
        $this->redirect = $redirect;
        parent::__construct($context);
    }

    /**
     * @return Redirect
     */
    public function execute(): Redirect
    {
        $entityId = $this->getRequest()->getParam('entity_id')
            ? [$this->getRequest()->getParam('entity_id')]
            : null;

        $result = $this->updateStock->execute($entityId);
        if (!empty($result['success'])) {
            $this->messageManager->addSuccessMessage(__($result['message']));
        } else {
            $this->messageManager->addErrorMessage(__($result['message']));
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath(
            $this->redirect->getRefererUrl()
        );
    }
}
