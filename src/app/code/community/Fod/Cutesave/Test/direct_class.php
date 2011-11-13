<?php

require dirname(__FILE__).'/../../../../../Mage.php';
Mage::app('admin');

//Mage::setIsDeveloperMode(true);

$product = Mage::getModel('fodcamp_cutesave/api_product');
/* @var $product Fodcamp_Cutesave_Model_Api_Product */

print_r( $product->getbasicattributes() );

exit;

$row = array(
    'sku' => '1234',
    '_type' => 'simple',
    'name'  => 'blub',
    '_attribute_set' => 'Default',
    'price' => 10,
    'status' => 1,
    'visibility' => 4,
    'weight' => 0,
    'description' => 'dummy',
    'short_description' => 'shorrt_dummy',
    'tax_class_id' => 2
);
$product->add( $row );
$product->add( $row );
$product->add( $row );

print_r( $product->write() );