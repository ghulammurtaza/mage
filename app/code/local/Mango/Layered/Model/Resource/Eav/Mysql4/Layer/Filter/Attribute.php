<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog Layer Attribute Filter Resource Model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mango_Layered_Model_Resource_Eav_Mysql4_Layer_Filter_Attribute extends Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Attribute
{
    /**
     * Initialize connection and define main table name
     *
     */


public function setConditions( $attribute, $conditions, $tablename , $tablealias ){
    
    
    $_all_conditions = array();
    
    if(Mage::registry("filter_conditions")){
        Mage::unregister("filter_conditions");
    }else{
        $_all_conditions = Mage::registry("filter_conditions");
        Mage::unregister("filter_conditions");
    }

    $_new_conditions = array(  "tablealias"=>$tablealias , "tablename"=>$tablename , 'conditions' => $conditions     );
    
    $_all_conditions[$attribute] = $_new_conditions; 
    
    Mage::register("filter_conditions" , $_all_conditions);
    
    return;
    
}

public function getConditions(){
    if(Mage::registry("filter_conditions")) return Mage::registry("filter_conditions");
    return false;
}


    protected function _construct()
    {
        $this->_init('catalog/product_index_eav', 'entity_id');
    }

    /**
     * Apply attribute filter to product collection
     *
     * @param Mage_Catalog_Model_Layer_Filter_Attribute $filter
     * @param int $value
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Attribute
     */
    public function applyFilterToCollection($filter, $value)
    {



        $collection = $filter->getLayer()->getProductCollection();
        $attribute  = $filter->getAttributeModel();
        $connection = $this->_getReadAdapter();
        $tableAlias = $attribute->getAttributeCode() . '_idx';
        $conditions = array(
            "{$tableAlias}.entity_id = e.entity_id",
            $connection->quoteInto("{$tableAlias}.attribute_id = ?", $attribute->getAttributeId()),
            $connection->quoteInto("{$tableAlias}.store_id = ?", $collection->getStoreId()),
            $connection->quoteInto("{$tableAlias}.value in (?)", $value)
        );

        $collection->getSelect()->join(
            array($tableAlias => $this->getMainTable()),
            join(' AND ', $conditions),
            array()
        );
        $collection->getSelect()->distinct();
        $this->setConditions( $attribute, $conditions, $this->getMainTable() , $tableAlias );

         return $this;
    }

    /**
     * Retrieve array with products counts per attribute option
     *
     * @param Mage_Catalog_Model_Layer_Filter_Attribute $filter
     * @return array
     */
    public function getCount($filter)
    {
        // clone select from collection with filters
        $select = clone $filter->getLayer()->getProductCollection()->getSelect();



        



        // reset columns, order and limitation conditions
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);

        $connection = $this->_getReadAdapter();
        $attribute  = $filter->getAttributeModel();
        $tableAlias = $attribute->getAttributeCode() . '_idy';
        $conditions = array(
            "{$tableAlias}.entity_id = e.entity_id",
            $connection->quoteInto("{$tableAlias}.attribute_id = ?", $attribute->getAttributeId()),
            $connection->quoteInto("{$tableAlias}.store_id = ?", $filter->getStoreId()),
        );



        $_from = $select->getPart(Zend_Db_Select::FROM);
        
        $_all_conditions = $this->getConditions();
        foreach( $_from as $index=>$condition){


            if($index==($attribute->getAttributeCode() . "_idx")) unset($_from[$attribute->getAttributeCode() . "_idx"]);
        }

        $select->setPart(Zend_Db_Select::FROM, $_from);

        $select
            ->join(
                array($tableAlias => $this->getMainTable()),
                join(' AND ', $conditions),
                array('value', 'count' => "COUNT( distinct({$tableAlias}.entity_id))"))
            ->group("{$tableAlias}.value");

         $select->distinct();

        return $connection->fetchPairs($select);
    }
}
