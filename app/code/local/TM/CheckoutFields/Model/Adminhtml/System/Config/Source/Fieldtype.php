<?php

class TM_CheckoutFields_Model_Adminhtml_System_Config_Source_Fieldtype
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'checkbox', 'label' => Mage::helper('checkoutfields')->__('Checkbox')),
            // array('value' => 'multiselect', 'label' => Mage::helper('checkoutfields')->__('Multiselect')),
            array('value' => 'text', 'label' => Mage::helper('checkoutfields')->__('Text')),
            array('value' => 'textarea', 'label' => Mage::helper('checkoutfields')->__('Textarea')),
            array('value' => 'select', 'label' => Mage::helper('checkoutfields')->__('Select'))
        );
    }
}
