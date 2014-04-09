<?php

class Infortis_ThemeAdmin_Model_Source_Category_Grid_ColumnCount
{
    public function toOptionArray()
    {
        return array(
			array('value' => 3, 'label' => Mage::helper('themeadmin')->__('3')),
            array('value' => 4, 'label' => Mage::helper('themeadmin')->__('4'))
        );
    }
}