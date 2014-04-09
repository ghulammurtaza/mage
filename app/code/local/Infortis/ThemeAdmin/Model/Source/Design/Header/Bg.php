<?php

class Infortis_ThemeAdmin_Model_Source_Design_Header_Bg
{
    public function toOptionArray()
    {
        return array(
			array('value' => 'gray-vl',			'label' => Mage::helper('themeadmin')->__('Gray - Very Light')),
			array('value' => 'gray-l',			'label' => Mage::helper('themeadmin')->__('Gray - Light')),
			array('value' => 'gray',			'label' => Mage::helper('themeadmin')->__('Gray')),
			array('value' => 'gray-d',			'label' => Mage::helper('themeadmin')->__('Gray - Dark')),
			array('value' => 'gray-vd',			'label' => Mage::helper('themeadmin')->__('Gray - Very Dark')),
			array('value' => 'navy',			'label' => Mage::helper('themeadmin')->__('Blue - Navy')),
			array('value' => 'blue-pale-l',		'label' => Mage::helper('themeadmin')->__('Blue - Pale'))
        );
    }
}