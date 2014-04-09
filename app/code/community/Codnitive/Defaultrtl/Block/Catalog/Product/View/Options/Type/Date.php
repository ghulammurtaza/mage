<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 * 
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Codnitive
 * @package    Codnitive_Defaultrtl
 * @author     Hassan Barza <support@codnitive.com>
 * @copyright  Copyright (c) 2012 CODNITIVE Co. (http://www.codnitive.com)
 */


/**
 * Product options text type block
 *
 * @category   Design
 * @package    Codnitive_Defaultrtl
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Codnitive_Defaultrtl_Block_Catalog_Product_View_Options_Type_Date extends Mage_Catalog_Block_Product_View_Options_Type_Date
{
    /**
     * HTML select element
     *
     * @param string $name Id/name of html select element
     * @return Mage_Core_Block_Html_Select
     */
    protected function _getHtmlSelect($name, $value = null)
    {
        $config = Mage::getModel('defaultrtl/config');
        if (!$config->isSetTemplate()) {
            return parent::_getHtmlSelect($name, $value);
        }
        
        $option = $this->getOption();

        // $require = $this->getOption()->getIsRequire() ? ' required-entry' : '';
        $require = '';
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setId('options_' . $this->getOption()->getId() . '_' . $name)
            ->setClass('product-custom-option datetime-picker ' . $name . $require)
            ->setExtraParams()
            ->setName('options[' . $option->getId() . '][' . $name . ']');

        $extraParams = 'style="width:auto"';
        if (!$this->getSkipJsReloadPrice()) {
            $extraParams .= ' onchange="opConfig.reloadPrice()"';
        }
        $select->setExtraParams($extraParams);

        if (is_null($value)) {
            $value = $this->getProduct()->getPreconfiguredValues()->getData('options/' . $option->getId() . '/' . $name);
        }
        if (!is_null($value)) {
            $select->setValue($value);
        }

        return $select;
    }
    
}
