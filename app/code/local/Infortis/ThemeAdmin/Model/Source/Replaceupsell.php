<?php

class Infortis_ThemeAdmin_Model_Source_Replaceupsell
{
    public function toOptionArray()
    {
        return array(
			array('value' => 0, 'label' => Mage::helper('themeadmin')->__('Never Replace')),
            array('value' => 1, 'label' => Mage::helper('themeadmin')->__('Always Replace Up-sell Products')),
            array('value' => 2, 'label' => Mage::helper('themeadmin')->__('Replace Only if There Are No Up-sell Products')),
        );
    }
}