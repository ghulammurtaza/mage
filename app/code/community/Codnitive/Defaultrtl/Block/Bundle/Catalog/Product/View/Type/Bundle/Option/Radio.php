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
 * Bundle option renderer
 *
 * @category   Codnitive
 * @package    Codnitive_Defaultrtl
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Codnitive_Defaultrtl_Block_Bundle_Catalog_Product_View_Type_Bundle_Option_Radio 
    extends Mage_Bundle_Block_Catalog_Product_View_Type_Bundle_Option_Radio
{
    /**
     * Get title price for selection product
     *
     * @param Mage_Catalog_Model_Product $_selection
     * @param bool $includeContainer
     * @return string
     */
    public function getSelectionTitlePrice($_selection, $includeContainer = true)
    {
        $config = Mage::getModel('defaultrtl/config');
        if (!$config->isSetTemplate()) {
            return parent::getSelectionTitlePrice($_selection, $includeContainer);
        }
        
        $price = $this->getProduct()->getPriceModel()->getSelectionPreFinalPrice($this->getProduct(), $_selection, 1);
        $this->setFormatProduct($_selection);
        $priceTitle = '<span class="bundle-option-title">' . $this->escapeHtml($_selection->getName()) . '</span>';
        $priceTitle .= ' &nbsp; ' . ($includeContainer ? '<span class="price-notice">' : '')
            . '+' . $this->formatPriceString($price, $includeContainer)
            . ($includeContainer ? '</span>' : '');
        return $priceTitle;
    }
    
}
