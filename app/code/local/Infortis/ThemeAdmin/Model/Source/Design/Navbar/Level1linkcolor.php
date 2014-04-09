<?php

class Infortis_ThemeAdmin_Model_Source_Design_Navbar_Level1LinkColor
{
    public function toOptionArray()
    {
        return array(
			array('value' => 'white',			'label' => Mage::helper('themeadmin')->__('White')),
			array('value' => 'black',			'label' => Mage::helper('themeadmin')->__('Black'))
        );
    }
}