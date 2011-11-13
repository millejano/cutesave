<?php

class Fodcamp_Cutesave_Model_Product_Data extends Mage_ImportExport_Model_Mysql4_Import_Data {

    protected $_dataBunch = null;

    public function setDataBunch($data = array())
    {
        $this->_dataBunch = $data;
        $this->_iterator = null;
        return $this;
    }

    public function getDataBunch() {
        return $this->_dataBunch;
    }

    public function cleanBunches()
    {
        $this->_dataBunch = null;
        return 0;
    }

    public function getBehavior()
    {
        return 'replace';
    }

    public function getEntityTypeCode()
    {
        return 'catalog_product';
    }

    public function getNextBunch()
    {
        if (null === $this->_iterator) {
            $this->_iterator = $this->getIterator();
            $this->_iterator->rewind();
        }
        if ($this->_iterator->valid()) {
            $dataRow = $this->_iterator->current();
            $this->_iterator->next();
        } else {
            $this->_iterator = null;
            $dataRow = null;
        }
        return $dataRow;
    }

    public function getIterator()
    {
        return new ArrayIterator( array($this->_dataBunch) );
    }

    public function saveBunch($entity, $behavior, array $data)
    {
        return 0;
    }
}