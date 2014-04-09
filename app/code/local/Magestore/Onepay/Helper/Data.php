<?php

class Magestore_Onepay_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getOnepayUrlReturn() {
		return Mage::getUrl('onepay/index/return', array('_secure'=>true));
	}
	
	public function getLocaleOption() {
		return array('en'=>Mage::helper('onepay')->__('English'),'vn'=>Mage::helper('onepay')->__('Vietnamese'));
	}
}