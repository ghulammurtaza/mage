<?php
    /**
    * @copyright  Copyright (c) 2011 Capacity Web Solutions Pvt. Ltd  (http://www.capacitywebsolutions.com)
    * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
    */
?>
<?php

    class Uraan_Mostview_Block_Mostview extends Mage_Catalog_Block_Product_Abstract // Mage_Core_Block_Template
    {


        public function __construct()
        {
            $this->setHeader('Most View Books');
            $this->setLimit(5);
            $this->setItemsPerRow(5);
            $this->setStoreId(Mage::app()->getStore()->getId());
            $this->setImageHeight(150);
            $this->setImageWidth(150);
            $this->setTimePeriod();
            $this->setAddToCart(true);
            $this->setActive(true);
            $this->setAddToCompare(false);
        }
        public function getMostViewedProducts()
        {
            // number of products to display
            $productCount = 10;
            // store ID
            $storeId    = Mage::app()->getStore()->getId();

            // get today and last 30 days time
            $today = time();
            $last = $today - (60*60*24*7);

            $from = date("Y-m-d", $last);
            $to = date("Y-m-d", $today);

            // get most viewed products for last 30 days
            $products = Mage::getResourceModel('reports/product_collection')
            ->addAttributeToSelect('*')
            ->setStoreId($storeId)
            ->addStoreFilter($storeId)
            ->addViewsCount()
            ->addViewsCount($from, $to)
            ->setPageSize($productCount);

            Mage::getSingleton('catalog/product_status')
            ->addVisibleFilterToCollection($products);
            Mage::getSingleton('catalog/product_visibility')
            ->addVisibleInCatalogFilterToCollection($products);
            return $products;
        }

    }



?>