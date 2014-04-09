<?php

class Infortis_SuperSlideshow_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getCfg($optionString)
    {
        return Mage::getStoreConfig('superslideshow/' . $optionString);
    }
	
	public function getCfgGeneral($optionString)
    {
        return Mage::getStoreConfig('superslideshow/general/' . $optionString);
    }
}
