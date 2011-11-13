<?php

class Fod_Cutesave_Model_Product extends Mage_ImportExport_Model_Import_Entity_Product {

    protected $_fileDirectory = null;
    protected $_dataSourceModel = null;

    public function getDataSourceModel()
    {
        return $this->_dataSourceModel;
    }

    public function setDataSourceModel( Mage_ImportExport_Model_Mysql4_Import_Data $datasource ) {
        $this->_dataSourceModel = $datasource;
        return $this;
    }

    public function validateRow(array $rowData, $rowNum)
    {
        $result = parent::validateRow($rowData, $rowNum);
        $this->_currentItem = $rowData;
        return $result;
    }

    public function addRowError($errorCode, $errorRowNum, $colName = null)
    {
        $sku = isset($this->_currentItem[self::COL_SKU]) ? $this->_currentItem[self::COL_SKU] : 'unknown';

        Mage::helper('fod_cutesave')->log('Product ('.$sku.') Import Error: '.$errorCode.' '.$colName);

        return parent::addRowError($errorCode, $errorRowNum, $colName);
    }

}