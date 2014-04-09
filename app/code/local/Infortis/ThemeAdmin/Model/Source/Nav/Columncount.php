<?php

class Infortis_ThemeAdmin_Model_Source_Nav_ColumnCount
{
    public function toOptionArray()
    {
        return array(
			array('value' => 4, 'label' => Mage::helper('themeadmin')->__('4')),
            array('value' => 5, 'label' => Mage::helper('themeadmin')->__('5')),
			array('value' => 6, 'label' => Mage::helper('themeadmin')->__('6')),
			array('value' => 7, 'label' => Mage::helper('themeadmin')->__('7')),
			array('value' => 8, 'label' => Mage::helper('themeadmin')->__('8'))
        );
    }
}