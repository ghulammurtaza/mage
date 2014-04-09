<?php
/**
 * @copyright  Copyright (c) 2011 Capacity Web Solutions Pvt. Ltd  (http://www.capacitywebsolutions.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
?>
<?php

class CapacityWebSolutions_Bestseller_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
     $this->loadLayout(array('default'));
     $this->renderLayout();
    }
}


?>