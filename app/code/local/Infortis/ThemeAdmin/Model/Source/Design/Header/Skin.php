<?php

class Infortis_ThemeAdmin_Model_Source_Design_Header_Skin
{
    public function toOptionArray()
    {
        return array(
			array('value' => 'l',			'label' => Mage::helper('themeadmin')->__('Light')),
			array('value' => 'default',		'label' => Mage::helper('themeadmin')->__('Medium Light (default)')),
			array('value' => 'md',			'label' => Mage::helper('themeadmin')->__('Medium Dark')),
			array('value' => 'd',			'label' => Mage::helper('themeadmin')->__('Dark'))
        );
    }
}