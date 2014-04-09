<?php
/**
 * @copyright  Copyright (c) 2011 Capacity Web Solutions Pvt. Ltd  (http://www.capacitywebsolutions.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
?>
<?php

class CapacityWebSolutions_Bestseller_Block_Bestseller extends Mage_Catalog_Block_Product_Abstract // Mage_Core_Block_Template
{
 
      
  public function __construct()
  {
  		$this->setHeader(Mage::getStoreConfig("bestseller/general/heading"));
		$this->setLimit((int)Mage::getStoreConfig("bestseller/general/number_of_items"));
		$this->setItemsPerRow((int)Mage::getStoreConfig("bestseller/general/number_of_items_per_row"));
		$this->setStoreId(Mage::app()->getStore()->getId());
		$this->setImageHeight((int)Mage::getStoreConfig("bestseller/general/thumbnail_height"));
		$this->setImageWidth((int)Mage::getStoreConfig("bestseller/general/thumbnail_width"));
		$this->setTimePeriod((int)Mage::getStoreConfig("bestseller/general/time_period"));
		$this->setAddToCart((bool)Mage::getStoreConfig('bestseller/general/add_to_cart'));
		$this->setActive((bool)Mage::getStoreConfigFlag("bestseller/general/active"));
		$this->setAddToCompare((bool)Mage::getStoreConfig("bestseller/general/add_to_compare"));
  }
  function getBestsellerProduct()
  { 
		 $timePeriod = ($this->getTimePeriod()) ? $this->getTimePeriod() : 60;
		 $date = date('Y-m-d');
		 $newdate = strtotime ( '-'.$timePeriod.' day' , strtotime ( $date ) ) ;
		 $newdate = date ( 'Y-m-j' , $newdate ); 
         $write = Mage::getSingleton('core/resource')->getConnection('core_write');
		 $read = Mage::getSingleton('core/resource')->getConnection('core_read');
		 $table_prefixx = Mage::getConfig()->getTablePrefix(); 
		 $upperLimit = ($this->getLimit()) ? $this->getLimit() : 4; 
/*		 $res = $write->query("select max(qo) as des_qty,`product_id` FROM (select sum(`qty_ordered`) AS qo,`product_id`,created_at,store_id from ".$table_prefixx."sales_flat_order_item Group By `product_id`) AS t1 where store_id = ".$this->getStoreId()." Group By `product_id` order By des_qty desc LIMIT 0, ".$upperLimit.""); 
*/		 
		 	//	echo "select max(qo) as des_qty,`product_id` FROM (select sum(`qty_ordered`) AS qo,`product_id`,created_at,store_id from ".$table_prefixx."sales_flat_order_item Group By `product_id`) AS t1 where store_id = ".$this->getStoreId()." AND created_at between'".$newdate."' AND '".$date."'  Group By `product_id` order By des_qty desc LIMIT 0, ".$upperLimit."";
		
        //select max(qo) as des_qty,`product_id` FROM (select sum(`qty_ordered`) AS qo,`product_id`,created_at,store_id from sales_flat_order_item Group By `product_id`) AS t1 where store_id = 3 AND created_at between'2012-10-18' AND '2012-12-17' Group By `product_id` order By des_qty desc LIMIT 0, 20
        		
		$res = $write->query("select max(qo) as des_qty,`product_id` FROM (select sum(`qty_ordered`) AS qo,`product_id`,created_at,store_id from ".$table_prefixx."sales_flat_order_item Group By `product_id`) AS t1 where store_id = ".$this->getStoreId()." AND created_at between'".$newdate."' AND '".$date."' Group By `product_id` order By des_qty desc LIMIT 0, ".$upperLimit.""); 
	//echo "<pre>";	
		//print_r($res);
		
		
		
		while ($row = $res->fetch()) 
		{ 
			$maxQty[]=$row['product_id'];
		}

		return $maxQty;
  }
}



?>