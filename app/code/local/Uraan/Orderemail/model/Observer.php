<?php  
/** 
* Catch: 
*     Mage::dispatchEvent('checkout_type_onepage_save_order', array('order'=>$order, 'quote'=>$quote)); 
* From: 
*     Sales/Model/Service/Quote.php 
*  
* Ben George 
* 03/11/2010 
*/  

class Uraan_Orderemail_Model_Observer  
{      
    /** 
    * Add a customer order comment when the order is placed 
    * @param object $event 
    * @return  
    */  
    public function setOrderComment(Varien_Event_Observer $observer)  
    {  
        $session = Mage::getSingleton('customer/session');
        if($session->isLoggedIn()) {
            $customer = $session->getCustomer();
            $name = $customer->getName();
            $email = $customer->getEmail();
        }
        $orders = Mage::getResourceModel('sales/order_collection')
        ->addFieldToSelect('*')
        ->addFieldToFilter('customer_id', $customer->getId()); 
        $orderCount = $orders->getSize();
        if ($orderCount < 1) //send coupon at first order
        {


            $fromDate = date('Y-n-j',time());
            $toDate = date('Y-n-j',time()+(60*60*24*14));
            $codeName = rand();
            //echo $fromDate.'<br>'.$toDate; exit;
            //$coupon = Mage::getModel('salesrule/rule')->load(1);
            //var_dump($coupon->getData());
            $coupon = Mage::getModel('salesrule/rule');
            $coupon->setName($name)
            ->setDescription('Coupon definition')
            ->setFromDate($fromDate)
            ->setToDate($toDate)
            ->setCouponType(2)
            ->setCouponCode($codeName)
            ->setUsesPerCoupon(1)
            ->setUsesPerCustomer(1)
            ->setCustomerGroupIds(array(1)) //an array of customer grou pids
            ->setIsActive(1)
            //serialized conditions.  the following examples are empty
            ->setConditionsSerialized('a:6:{s:4:"type";s:32:"salesrule/rule_condition_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";}')
            ->setActionsSerialized('a:6:{s:4:"type";s:40:"salesrule/rule_condition_product_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";}')
            ->setStopRulesProcessing(0)
            ->setIsAdvanced(1)
            ->setProductIds('')
            ->setSortOrder(0)
            ->setSimpleAction('by_percent')
            ->setDiscountAmount(10)
            ->setDiscountQty(null)
            ->setDiscountStep('0')
            ->setSimpleFreeShipping('0')
            ->setApplyToShipping('1')
            ->setIsRss(0)
            ->setWebsiteIds(array(1));      
            $coupon->save();

            /*$emailTemplate  = Mage::getModel('core/email_template')
            ->loadDefault('custom_email_template1');                                 

            //Create an array of variables to assign to template
            $emailTemplateVariables = array();
            $emailTemplateVariables['from_date'] = $fromDate;
            $emailTemplateVariables['to_date'] = $toDate;
            $emailTemplateVariables['coupon_code'] = $codeName;
            $emailTemplateVariables['name'] = $name;
            $emailTemplateVariables['discount'] = '10%';
             */
            /**
            * The best part <img src="http://inchoo.net/wp-includes/images/smilies/icon_smile.gif" alt=":)" class="wp-smiley"> 
            * Opens the activecodeline_custom_email1.html, throws in the variable array 
            * and returns the 'parsed' content that you can use as body of email
            */
            //$processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);

            /*
            * Or you can send the email directly, 
            * note getProcessedTemplate is called inside send()
            */
            //$emailTemplate->send($email,'Coupon Code information', $emailTemplateVariables);
            
            if (!$generalEmail = Mage::getSingleton('core/config_data')->getCollection()->getItemByColumnValue('path', 'trans_email/ident_sales/email')) {
                $conf = Mage::getSingleton('core/config')->init()->getXpath('/config/default/trans_email/ident_sales/email');
                $generalEmail = array_shift($conf);
            } else {
                $generalEmail = $generalEmail->getValue();
            }
            
            $providerName = Mage::getStoreConfig('trans_email/ident_sales/name'); 
            $providerEmail = Mage::getStoreConfig('trans_email/ident_sales/email'); 
            Mage::log("Sending email to $email");
            //$msg = '<table cellspacing="0" cellpadding="0" border="0" height="100%" width="100%"><tr><td><div><h1>Coupon Code Information</h1><p>Hi '.$name.' your Coupon Code is: '.$codeName.' <br/> Valid from from '.$fromDate.' to '.$toDate.'.This is just inform you that by this coupon you will get discount of 10%.</p></div></td></tr></table>';
            $mail = Mage::getModel('core/email');
            $mail->setToName($name);
            $mail->setToEmail($email);
            $mail->setBody('my body');
            $mail->setSubject('Coupon Code information');
            $mail->setFromEmail($providerEmail);
            $mail->setFromName($providerName);
            $mail->setType('html');
         
            try {
                $mail->send();
            }
            catch (Exception $e) {
                Mage::logException($e);
            }
            
            
        }
       // echo $orderCount."<pre>"; print_r($coupon); exit; 
    }  
}  
	