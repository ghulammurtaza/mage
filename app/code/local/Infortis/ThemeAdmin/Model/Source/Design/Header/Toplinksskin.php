<?php

class Infortis_ThemeAdmin_Model_Source_Design_Header_TopLinksSkin
{
    public function toOptionArray()
    {
        return array(
			array('value' => 'default',		'label' => Mage::helper('themeadmin')->__('Transparent White, Gray Icons (default)')),
			array('value' => 'b0b',			'label' => Mage::helper('themeadmin')->__('Transparent Black, Black Icons')),
			array('value' => 'w3b',			'label' => Mage::helper('themeadmin')->__('Transparent White, Black Icons'))
        );
    }
}