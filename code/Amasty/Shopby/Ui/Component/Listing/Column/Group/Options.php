<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Ui\Component\Listing\Column\Group;

use Magento\Framework\View\Element\UiComponentInterface;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Options extends Column
{

    /**
     * @var \Amasty\Shopby\Model\GroupAttrFactory
     */
    private $groupAttrFactory;

    private $encoder;

    private $swatchHelper;

    /**
     * Options constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Amasty\Shopby\Model\GroupAttrFactory $groupAttrFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Amasty\Shopby\Model\GroupAttrFactory $groupAttrFactory,
        \Magento\Framework\Json\Encoder $encoder,
        \Magento\Swatches\Helper\Media $swatchHelper,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->groupAttrFactory = $groupAttrFactory;
        $this->encoder = $encoder;
        $this->swatchHelper = $swatchHelper;
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getData('name')] = $this->prepareItem($item);
            }
        }

        return $dataSource;
    }

    /**
     * Get data
     *
     * @param array $item
     * @return string
     */
    protected function prepareItem(array $item)
    {
        list($items, $object) = $this->groupAttrFactory->create()->getResource()->getOptions($item);
        $array = ['items' => $items, 'code' => $item['attribute_id'], 'type' => $object];
        return $this->encoder->encode($array);
    }
}
