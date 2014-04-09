<?php

class Infortis_ThemeAdmin_Model_Source_Design_Navbar_Level2Link
{
    public function toOptionArray()
    {
        return array(
			array('value' => 'gray-l-red',		'label' => Mage::helper('themeadmin')->__('Light Gray - Red')),
			array('value' => 'gray-l-gray',		'label' => Mage::helper('themeadmin')->__('Light Gray - Gray')),
			array('value' => 'blue-l-blue',		'label' => Mage::helper('themeadmin')->__('Light Blue - Blue'))
        );
    }
}