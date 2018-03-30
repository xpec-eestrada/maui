<?php
namespace Xpectrum\RegionComuna\Plugin\Model\Checkout;
class LayoutProcessor 
{
    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
     
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array  $jsLayout
    ) {
        $data=array();
        $data = $this->getAllOptions();
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['xpec_region'] = [
            'component' => 'Magento_Ui/js/form/element/select',
            'config' => [
                'customScope' => 'shippingAddress',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/select',
                'id' => 'xpec_region',
            ],
            'dataScope' => 'shippingAddress.xpec_region',
            'label' => 'Región',
            'provider' => 'checkoutProvider',
            'visible' => true,
            'validation' => [
                'required-entry' => true,
            ],
            'sortOrder' => 99,
            'id' => 'xpec_region',
            'options' => $data
        ];

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['region_id'] = [
            'component' => 'Magento_Ui/js/form/element/select',
            'config' => [
                'customScope' => 'shippingAddress',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/select',
                'id' => 'region_id',
            ],
            'dataScope' => 'shippingAddress.region_id',
            'label' => 'Comuna',
            'provider' => 'checkoutProvider',
            'visible' => true,
            'validation' => [
                'required-entry' => true,
            ],
            'sortOrder' => 100,
            'id' => 'region_id',
            'options' => [
                [
                    'value' => ' ',
                    'label' => 'Seleccione Región',
                ]
            ]
        ];

        
        return $jsLayout;
    }
    public function getAllOptions(){
        /* your Attribute options list*/
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('xpec_regiones');
        $sql='SELECT id,nombre
                    FROM 
                        '.$tableName.' 
                    ORDER BY
                        nombre ASC';
        $result = $connection->fetchAll($sql);
        $data=array();
        $data[]=array('label' => 'Seleccione Región','value' => ' ');
        foreach($result as $item){
            $data[]=array('label' => $item['nombre'],'value' => $item['id']);
        }
        return $data;
    }
}