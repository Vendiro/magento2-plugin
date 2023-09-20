<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Block\Adminhtml\Config\Button;

use Magento\Backend\Block\Widget\Button;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class QueueAll extends Field
{
    /**
     * @var string
     */
    protected $_template = 'Vendiro_Connect::config/button/queue.phtml';

    /**
     * @inheritdoc
     */
    public function render(AbstractElement $element): string
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * @inheritdoc
     */
    public function _getElementHtml(AbstractElement $element): string
    {
        return $this->_toHtml();
    }

    /**
     * Return ajax url for collect button
     *
     * @return string
     */
    public function getAjaxUrl(): string
    {
        return $this->getUrl('vendiro/config_stock/QueueAll');
    }

    /**
     * Generate collect button html
     *
     * @return string
     */
    public function getButtonHtml(): string
    {
        try {
            return $this->getLayout()
                ->createBlock(Button::class)
                ->setData([
                    'id' => 'vendiro-button_queue',
                    'label' => __('Force queue all products')
                ])->toHtml();
        } catch (\Exception $e) {
            return '';
        }
    }
}
