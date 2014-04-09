<?php

class Infortis_ThemeAdmin_Model_Source_Replacerelated
{
    public function toOptionArray()
    {
        return array(
			array('value' => 0, 'label' => Mage::helper('themeadmin')->__('Never Replace')),
            array('value' => 1, 'label' => Mage::helper('themeadmin')->__('Always Replace Related Products')),
            array('value' => 2, 'label' => Mage::helper('themeadmin')->__('Replace Only if There Are No Related Products')),
        );
    }
}