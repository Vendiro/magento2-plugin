<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Block\Adminhtml\Config\Button;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Button;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Vendiro\Connect\Api\Log\RepositoryInterface as LogRepository;

/**
 * Credentials validation
 */
class Credentials extends Field
{

    /**
     * @var string
     */
    protected $_template = 'Vendiro_Connect::config/button/credentials.phtml';

    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var LogRepository
     */
    private $logger;

    /**
     * Credentials constructor.
     *
     * @param Context $context
     * @param LogRepository $logger
     * @param array $data
     */
    public function __construct(
        Context $context,
        LogRepository $logger,
        array $data = []
    ) {
        $this->request = $context->getRequest();
        $this->logger = $logger;
        parent::__construct($context, $data);
    }

    /**
     * @inheritDoc
     */
    public function render(AbstractElement $element): string
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * @inheritDoc
     */
    public function _getElementHtml(AbstractElement $element): string
    {
        return $this->_toHtml();
    }

    /**
     * @return string
     */
    public function getApiCheckUrl(): string
    {
        return $this->getUrl(
            'vendiro/credentials/check',
            [
                'store' => (int)$this->request->getParam('store')
            ]
        );
    }

    /**
     * @return string
     */
    public function getButtonHtml(): string
    {
        try {
            return $this->getLayout()->createBlock(Button::class)
                ->setData([
                    'id' => 'vendiro-button_credentials',
                    'label' => __('Validate credentials')
                ])->toHtml();
        } catch (Exception $e) {
            $this->logger->addErrorLog('Credentials check', $e->getMessage());
            return '';
        }
    }
}
