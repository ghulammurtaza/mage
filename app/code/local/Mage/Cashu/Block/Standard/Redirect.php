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
 * @category   Mage
 * @package    Mage_Cashu
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Redirect to Cashu
 *
 * @category    Mage
 * @package     Mage_Cashu
 * @name        Mage_Cashu_Block_Standard_Redirect
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Cashu_Block_Standard_Redirect extends Mage_Core_Block_Abstract
{
	
    protected function _toHtml()
    {
        $standard = Mage::getModel('cashu/standard');
        $form = new Varien_Data_Form();
		$standard->getCashuUrl(); 
        $form->setAction($standard->getCashuUrl())
            ->setId('cashu_standard_checkout')
            ->setName('cashu_standard_checkout')
            ->setMethod('POST')
            ->setUseContainer(true);

        foreach ($standard->setOrder($this->getOrder())->getStandardCheckoutFormFields() as $field => $value) {
		
		// fetch and set order fields for sending to cashu
		if($field == 'return')
        	{
        		$returnurl=$value."?DR={DR}";
        	}

		
		if($field == 'amount')
			{
				$amount=$value;
			}
		if($field == 'cs1')
			{
				$referenceno=$value;
			}
		if($field == 'f_name')
			{
				$fname=$value;
			}
		if($field == 's_name')
			{
				$lname=$value;
			}
	     if($field == 'product_name')
			{
			$display_text=$value;
			}		 	
        if($field == 'zip')
			{
			$postalcode=$value;
			}
        if($field == 'street')
			{
			$street=$value;
			}
        if($field == 'street')
			{
			$street=$value;
			}
        if($field == 'city')
			{
			$city=$value;
			}	
        if($field == 'state')
			{
			$state=$value;
			}
		if($field == 'currency')
			{
				$currency=$value;
			}	
		
		
			
		   $form->addField($field, 'hidden', array('name' => $field, 'value' => $value));
		   
        }
		$merchant_id = Mage::getSingleton('cashu/config')->getMerchantId(); // merchant id
		$secret_key = Mage::getSingleton('cashu/config')->getSecretKey(); // secret key
		
		$token = md5($merchant_id.':'.$amount.':'.strtolower($currency).':'.$secret_key); // prepare token
		$name=$fname." ".$lname; // name
		$address=$street.",".$city.",".$state;		 // address
		$mode=Mage::getSingleton('cashu/config')->getTransactionMode(); // mode of payment
	
		if($mode == '1')
		{
		$mode="1";
		}
		else
		{
		$mode="0";
	    }
		
		
		$form->addField('display_text', 'hidden', array('name'=>'display_text', 'value'=>$display_text));
		$form->addField('txt1', 'hidden', array('name'=>'txt1', 'value'=>$address));
		$form->addField('token', 'hidden', array('name'=>'token', 'value'=>$token));
		$form->addField('test_mode', 'hidden', array('name'=>'test_mode', 'value'=>$mode));
      
		// prepare form  
        $html = '<html><body>';
        $html.= $this->__('You will be redirected to Cashu in a few seconds.');
        $html.= $form->toHtml();
        $html.= '<script type="text/javascript">document.getElementById("cashu_standard_checkout").submit();</script>';
        $html.= '</body></html>';

        return $html;
    }
}