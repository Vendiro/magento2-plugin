<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Block\Adminhtml\Config\Button;

use Exception;
use Magento\Backend\Block\Widget\Button;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Error log check button class
 */
class ErrorCheck extends Field
{

    /**
     * @var string
     */
    protected $_template = 'Vendiro_Connect::config/button/error.phtml';

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
    public function getErrorCheckUrl(): string
    {
        return $this->getUrl('vendiro/log/stream', ['type' => 'error']);
    }

    /**
     * @return string
     */
    public function getButtonHtml(): string
    {
        try {
            return $this->getLayout()
                ->createBlock(Button::class)
                ->setData([
                    'id' => 'vendiro-button_error',
                    'label' => __('Check last 50 error log records')
                ])->toHtml();
        } catch (Exception $e) {
            return '';
        }
    }
}
