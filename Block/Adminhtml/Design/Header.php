<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Block\Adminhtml\Design;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Header extends Field
{

    /**
     * @var string
     */
    protected $_template = 'Vendiro_Connect::config/fieldset/header.phtml';

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element): string
    {
        $element->addClass('vendiro');
        return $this->toHtml();
    }
}
