<?php
class Infortis_ThemeAdmin_Block_Product_List_Featured extends Mage_Catalog_Block_Product_List
{
	/**
	 * Retrieve loaded category collection.
	 * Variables collected from CMS markup: category_id, product_count, is_random
	 *
	 * @var $categoryID
	 * @var $productCount
	 */
	protected function _getProductCollection()
	{
		if (is_null($this->_productCollection))
		{
			$categoryID = $this->getCategoryId();
			if($categoryID)
			{
				$category = new Mage_Catalog_Model_Category();
				$category->load($categoryID);
				$collection = $category->getProductCollection();
			}
			else
			{
				$collection = Mage::getResourceModel('catalog/product_collection');
			}
			Mage::getModel('catalog/layer')->prepareProductCollection($collection);
			
			$isRandom = $this->getIsRandom();
			if ($isRandom)
				$collection->getSelect()->order('rand()');
			
			$collection->addStoreFilter();
			$productCount = $this->getProductCount() ? $this->getProductCount() : 4;
			$collection->setPage(1, $productCount)->load();
			
			$this->_productCollection = $collection;
		}
		return $this->_productCollection;
	}
	
	//Example based on Mage_Catalog_Block_Product_List_Random
//	protected function _getProductCollection()
//    {
//        if (is_null($this->_productCollection)) {
//            $collection = Mage::getResourceModel('catalog/product_collection');
//            Mage::getModel('catalog/layer')->prepareProductCollection($collection);
//            $collection->getSelect()->order('rand()');
//            $collection->addStoreFilter();
//            $productCount = $this->getNumProducts() ? $this->getNumProducts() : 0;
//            $collection->setPage(1, $productCount);
//
//            $this->_productCollection = $collection;
//        }
//        return $this->_productCollection;
//    }
}
