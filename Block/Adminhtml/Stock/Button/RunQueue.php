<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Block\Adminhtml\Stock\Button;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * RunQueue Button
 */
class RunQueue implements ButtonProviderInterface
{
    /**
     * @var Context
     */
    private $context;

    /**
     * GenericButton constructor.
     *
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }

    /**
     * @inheritDoc
     */
    public function getButtonData(): array
    {
        return [
            'label' => __('Update Manually')->render(),
            'url' => $this->getUrl(),
            'class' => 'primary',
            'sort_order' => 10
        ];
    }

    /**
     * Get Actions URL for button
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->context->getUrlBuilder()->getUrl('*/*/update', []);
    }
}
