<?php

class Fodcamp_Cutesave_Helper_Data extends Mage_Core_Helper_Abstract {

    public static function log($message, $level = null) {
         Mage::log($message, $level, 'importapi.log', true);
    }

}