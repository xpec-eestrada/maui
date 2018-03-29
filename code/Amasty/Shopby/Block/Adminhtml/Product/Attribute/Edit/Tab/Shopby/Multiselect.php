<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Amasty\Shopby\Block\Adminhtml\Product\Attribute\Edit\Tab\Shopby;

use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

class Multiselect extends \Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element implements RendererInterface
{
    protected $_template = 'Amasty_Shopby::form/renderer/fieldset/multiselect.phtml';
}
