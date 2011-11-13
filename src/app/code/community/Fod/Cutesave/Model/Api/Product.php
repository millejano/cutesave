<?php

class Fod_Cutesave_Model_Api_Product extends Mage_Api_Model_Resource_Abstract {

    /**
     * @return Mage_Core_Model_Session
     */
    protected function _getDataSession() {
        return Mage::getSingleton("core/session",  array("name"=>"api"));
    }

    protected function _getRowData() {
        return $this->_getDataSession()->getData('rows');
    }

    protected function _addRowData( Array $row ) {
        $rows = $this->_getRowData();
        $rows[] = $row;
        $this->_getDataSession()->setData('rows', $rows);
        return $this;
    }

    public function getAttributeSet() {
        return 'Default';
    }

    public function getWebsites() {
        return 'base';
    }

    public function getTaxClassId() {
        return 2;
    }

    public function getbasicattributes() {
        $templatexml = Mage::getConfig()->getNode('fod_cutesave/basic_attributes');
        /* @var $templatexml Mage_Core_Model_Config_Element */
        return $templatexml->asCanonicalArray();
    }

    protected function _addExtraRows( array $data ) {
        if ( is_array($data['categories']) && count($data['categories']) > 0 ) {
            foreach( $data['categories'] AS $category ) {
                $this->_addRowData( array('_category' => $category) );
            }
        }
    }

    public function addsimple( array $attributedata) {
        $base = array(
            '_type' => 'simple',
            '_attribute_set' => $this->getAttributeSet(),
            'tax_class_id'  => $this->getTaxClassId(),
            'visibility' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            'weight'    => 0,
            '_product_websites' => $this->getWebsites()
        );
        $this->_addRowData( array_merge( $base, $attributedata ) );
        $this->_addExtraRows($attributedata);

        return true;
    }

    public function addconfigurable( array $configurable_attributes, array $simple_products, array $attributedata ) {
        $this->_addRowData( $configurable_attributes );

        foreach( $simple_products AS $simpledata ) {
            $this->addsimple( $simpledata );
        }
        $base = array(
            '_type' => 'configurable',
            '_attribute_set' => $this->getAttributeSet(),
            'tax_class_id'  => $this->getTaxClassId(),
            'visibility' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            '_product_websites' => $this->getWebsites()
        );
        $this->_addRowData( array_merge( $base, $attributedata ) );
        $this->_addExtraRows($attributedata);

        foreach( $configurable_attributes AS $attribute_code ) {
            $this->_addRowData( array('_super_attribute_code' => $attribute_code) );
        }

        foreach( $simple_products AS $simple ) {
            $this->_addRowData( array('_super_products_sku' => $simple['sku'] ) );
        }

        return true;
    }

    public function write() {

        Mage::helper('fod_cutesave')->log( $this->_getRowData() );

        $sourceadapter = Mage::getModel('fod_cutesave/product_data');
        /* @var $sourceadapter Fod_Cutesave_Model_Product_Data */
        $sourceadapter->setDataBunch( $this->_getRowData() );

        $import = Mage::getModel('fod_cutesave/product');
        /* @var $import Fod_Cutesave_Model_Product */

        $import->setDataSourceModel( $sourceadapter );
        $import->importData();

        return $import->getErrorMessages();
    }

    public function reindex() {
        // Refresh Index-Stuff
        $processes = Mage::getSingleton('index/indexer')->getProcessesCollection();
        $processes->walk('reindexAll');
    }

    
    
}
