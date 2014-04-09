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
 * Catalog category
 *
 * @category   Design
 * @package    Codnitive_Defaultrtl
 * @author     Hassan Barza <h.barza@gmail.com>
 */
class Codnitive_Defaultrtl_Model_Config
{

    /**
     * Check for theme is set or not
     *
     * @return boolean
     */
    public function isSetTemplate()
    {
        $template = Mage::getStoreConfig('design/theme/template');
        if ($template === 'default_rtl') {
            return true;
        }
        return false;
    }

}
