<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Amasty\Shopby\Api\Data;

interface GroupAttrInterface
{
    const ID = 'group_id';
    const ATTRIBUTE_ID = 'attribute_id';
    const NAME = 'name';
    const GROUP_CODE = 'group_code';
    const URL = 'url';
    const POSITION = 'position';
    const VISUAL = 'visual';
    const TYPE = 'type';
    const ENABLED = 'enabled';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return int
     */
    public function getAttributeId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getGroupCode();

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @return int
     */
    public function getPosition();

    /**
     * @return string
     */
    public function getVisual();

    /**
     * @return int
     */
    public function getType();

    /**
     * @return bool
     */
    public function getEnabled();

    /**
     * @param $id
     * @return GroupAttrInterface
     */
    public function setId($id);

    /**
     * @param $id
     * @return GroupAttrInterface
     */
    public function setAttributeId($id);

    /**
     * @param $name
     * @return GroupAttrInterface
     */
    public function setName($name);

    /**
     * @param $code
     * @return GroupAttrInterface
     */
    public function setGroupCode($code);

    /**
     * @param $url
     * @return GroupAttrInterface
     */
    public function setUrl($url);

    /**
     * @param $pos
     * @return GroupAttrInterface
     */
    public function setPosition($pos);

    /**
     * @param $visual
     * @return GroupAttrInterface
     */
    public function setVisual($visual);

    /**
     * @param $type
     * @return GroupAttrInterface
     */
    public function setType($type);

    /**
     * @param $enabled
     * @return GroupAttrInterface
     */
    public function setEnabled($enabled);
}