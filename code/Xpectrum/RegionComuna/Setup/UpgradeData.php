<?php

namespace Xpectrum\RegionComuna\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Model\AttributeFactory;
use Magento\Store\Model\WebsiteFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Model\Config;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface{
    /**
     * {@inheritdoc}
     */
    private $customerSetupFactory;
    private $_attrFactory;
    private $websiteFactory;
    protected $_eavConfig;
    private $_attrSetFactory;
    public function __construct(
        \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory,
        AttributeFactory $attrFactory,
        WebsiteFactory $websiteFactory,
        Config $eavConfig,
        AttributeSetFactory $attrSetFactory
        )
    {
        $this->_attrFactory = $attrFactory;
        $this->_attrSetFactory = $attrSetFactory;
        $this->websiteFactory = $websiteFactory;
        $this->_eavConfig = $eavConfig;
        $this->customerSetupFactory = $customerSetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context){
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $this->dataTableRegion($setup);
        }
        $setup->endSetup();
    }
    private function dataTableRegion($setup){
        $data = [
            [
                'id'     => '2000',
                'nombre'  =>'XV - Arica y Parinacota'
            ],
            [
                'id'     => '2001',
                'nombre'  =>'I - Tarapacá'
            ],
            [
                'id'     => '2002',
                'nombre'  =>'II - Antofagasta'
            ],
            [
                'id'     => '2003',
                'nombre'  =>'III - Atacama'
            ],
            [
                'id'     => '2004',
                'nombre'  =>'IV - Coquimbo'
            ],
            [
                'id'     => '2005',
                'nombre'  =>'V - Valparaíso'
            ],
            [
                'id'     => '2006',
                'nombre'  =>'RM - Región Metropolitana'
            ],
            [
                'id'     => '2007',
                'nombre'  =>'VI - Rancagua'
            ],
            [
                'id'     => '2008',
                'nombre'  =>'VII - Maule'
            ],
            [
                'id'     => '2009',
                'nombre'  =>'VIII - Biobío'
            ],
            [
                'id'     => '2010',
                'nombre'  =>'XI - La Araucanía'
            ],
            [
                'id'     => '2011',
                'nombre'  =>'XIV - Los Ríos'
            ],
            [
                'id'     => '2012',
                'nombre'  =>'X - Los Lagos'
            ],
            [
                'id'     => '2013',
                'nombre'  =>'XI - Aysén'
            ],
            [
                'id'     => '2014',
                'nombre'  =>'XII - Magallanes'
            ]
        ];
        foreach ($data as $bind) {
            $setup->getConnection()
            ->insertForce($setup->getTable('xpec_regiones'), $bind);
        }

        /* Attributo Drop Down List Regiones */
        $code_attribute     = 'xpec_region';
        $attrSet            = $this->_attrSetFactory->create();
        $entity_type        = $this->_eavConfig->getEntityType('customer_address');
        $entity_type_id     = $entity_type->getId();
        $attribute_set_id   = $entity_type->getDefaultAttributeSetId();
        $attribute_group_id = $attrSet->getDefaultGroupId($attribute_set_id);
        $customerSetup      = $this->customerSetupFactory->create(['setup' => $setup]);
        $order              = 99;
        
        

        $customerSetup->addAttribute('customer_address', $code_attribute,  array(
            "type"     => "int",
            "label"    => "Región",
            "input"    => "select",
            "visible"  => true,
            "required" => false,
            "system"   => 0,
            'user_defined' => true,
            'sort_order' => $order,
            "position" => $order,
            'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
            'source' => 'Xpectrum\RegionComuna\Model\Config\Source\OptionsRegion',
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE

        ));

        $customerSetup->addAttributeToGroup(
            $entity_type_id,
            $attribute_set_id,
            $attribute_group_id,
            $code_attribute,
            '33'
        );

        $dropdownlist       = $customerSetup->getEavConfig()->getAttribute('customer_address', $code_attribute);
        $used_in_forms      = array();
        $used_in_forms[]    = "adminhtml_customer_address";
        $used_in_forms[]    = "customer_address_edit";
        $used_in_forms[]    = "customer_register_address";

        $dropdownlist->setData("used_in_forms", $used_in_forms)
            ->setData("is_used_for_customer_segment", true)
            ->setData("is_system", 0)
            ->setData("is_user_defined", 1)
            ->setData("is_visible", 1)
            ->setData("sort_order", $order);
        $dropdownlist->save();
        $setup->endSetup();
        /* Attributo Drop Down List Region */
    }
}