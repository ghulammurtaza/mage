<?php

class Infortis_ThemeAdmin_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getCfg($optionString)
    {
        return Mage::getStoreConfig('themeadmin/' . $optionString);
    }
	
	public function getCfgDesign($optionString)
    {
        return Mage::getStoreConfig('themedesign/' . $optionString);
    }
	
	/*
	 * Check if "new" label is enabled and if product is marked as "new"
	 */
	public function isNew($product)
	{
		return $this->_nowIsBetween($product->getData('news_from_date'), $product->getData('news_to_date'));
	}
	
	/*
	 * Check if "sale" label is enabled and if product has special price
	 */
	public function isOnSale($product)
	{
		$specialPrice = number_format($product->getFinalPrice(), 2);
		$regularPrice = number_format($product->getPrice(), 2);
		
		if ($specialPrice != $regularPrice)
			return true;
		else
			return false;
			
//		if ($specialPrice != $regularPrice)
//			return $this->_nowIsBetween($product->getData('special_from_date'), $product->getData('special_to_date'));
//		else
//			return false;
	}
	
	private function _nowIsBetween($fromDate, $toDate)
	{
		if ($fromDate)
		{
			$fromDate = strtotime($fromDate);
			$toDate = strtotime($toDate);
			$now = strtotime(date("Y-m-d H:i:s"));
			
			if ($toDate)
			{
				if ($fromDate <= $now && $now <= $toDate)
					return true;
			}
			else
			{
				if ($fromDate <= $now)
					return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Get image URL for given product
	 *
	 * @param $t
	 * @param Mage_Catalog_Model_Product $prod Product
	 * @param int $w Image width
	 * @param int $h Image height
	 * @param string $imgVersion Image version: normal, small or thumbnail
	 * @param $f Specific file
	 * @return string
	 */
	function getImgUrl($t, $prod, $w, $h, $imgVersion='image', $f=NULL)
	{
		$imgUrl = '';
		if ($h <= 0)
			$imgUrl = $t->helper('catalog/image')->init($prod, $imgVersion, $f)
				->constrainOnly(TRUE)
				->keepAspectRatio(TRUE)
				->keepFrame(FALSE)
				->resize($w);
		else $imgUrl = $t->helper('catalog/image')->init($prod, $imgVersion, $f)->resize($w, $h);
		return $imgUrl;
	}
}