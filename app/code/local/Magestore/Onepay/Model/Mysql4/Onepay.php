<?php

class Magestore_Onepay_Model_Mysql4_Onepay extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the onepay_id refers to the key field in your database table.
        $this->_init('onepay/onepay', 'vpc_merchtxnref');
    }
}