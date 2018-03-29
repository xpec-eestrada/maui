<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Model;

use Amasty\Shopby\Api\Data\GroupAttrValueInterface;

class GroupAttrValue extends \Magento\Framework\Model\AbstractModel implements GroupAttrValueInterface
{

    protected function _construct()
    {
        $this->_init('Amasty\Shopby\Model\ResourceModel\GroupAttrValue');
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * @return int
     */
    public function getGroupId()
    {
        return $this->getData(self::GROUP_ID);
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->getData(self::VALUE);
    }

    /**
     * @return int
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * @param $id
     * @return GroupAttrOptionInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @param $id
     * @return GroupAttrOptionInterface
     */
    public function setGroupId($id)
    {
        return $this->setData(self::GROUP_ID, $id);
    }

    /**
     * @param $option
     * @return GroupAttrOptionInterface
     */
    public function setValue($value)
    {
        return $this->setData(self::VALUE, $value);
    }

    /**
     * @param $sort
     * @return GroupAttrOptionInterface
     */
    public function setSortOrder($sort)
    {
        return $this->setData(self::SORT_ORDER, $sort);
    }
}