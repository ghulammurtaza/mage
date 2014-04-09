<?php

class TM_FireCheckout_Model_Observer
{
    public function setCustomerComment($data)
    {
        $comment = trim(Mage::getSingleton('customer/session')->getOrderCustomerComment());
        if (!empty($comment)) {
            $data['order']->setCustomerOrderComment($comment);
            $data['order']->addStatusHistoryComment($comment)
                ->setIsVisibleOnFront(true)
                ->setIsCustomerNotified(false);
        }
    }

    public function unsetCustomerComment()
    {
        Mage::getSingleton('customer/session')->setOrderCustomerComment(null);
    }

    public function addToCartComplete(Varien_Event_Observer $observer)
    {
        $generalConfig = Mage::getStoreConfig('firecheckout/general');
        if ($generalConfig['enabled'] && $generalConfig['redirect_to_checkout']) {
            $observer->getResponse()
                ->setRedirect(
                    Mage::helper('firecheckout/url')->getCheckoutUrl()
                );
            Mage::getSingleton('checkout/session')->setNoCartRedirect(true);
        }
    }

    /**
     * Called before captcha check
     */
    public function setCheckoutMethod($observer)
    {
        $data  = $observer->getControllerAction()->getRequest()->getPost('billing', array());
        $checkout = Mage::getSingleton('firecheckout/type_standard');
        $quote = $checkout->getQuote();
        if (isset($data['register_account']) && $data['register_account']) {
            $quote->setCheckoutMethod(TM_FireCheckout_Model_Type_Standard::METHOD_REGISTER);
        } else if ($checkout->getCustomerSession()->isLoggedIn()) {
            $quote->setCheckoutMethod(TM_FireCheckout_Model_Type_Standard::METHOD_CUSTOMER);
        } else {
            $quote->setCheckoutMethod(TM_FireCheckout_Model_Type_Standard::METHOD_GUEST);
        }
        return $this;
    }

/* See Mage_Captcha_Model_Observer for the source of the next methods */

    /**
     * Check Captcha On Forgot Password Page
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Captcha_Model_Observer
     */
    public function checkForgotpassword($observer)
    {
        $formId = 'user_forgotpassword';
        $captchaModel = Mage::helper('captcha')->getCaptcha($formId);
        if ($captchaModel->isRequired()) {
            $controller = $observer->getControllerAction();
//            if (!$captchaModel->isCorrect($this->_getCaptchaString($controller->getRequest(), $formId))) {
//                Mage::getSingleton('customer/session')->addError(Mage::helper('captcha')->__('Incorrect CAPTCHA.'));
//                $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
//                $controller->getResponse()->setRedirect(Mage::getUrl('*/*/forgotpassword'));
//            }

            if (!$captchaModel->isCorrect($this->_getCaptchaString($controller->getRequest(), $formId))) {
                $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                $result = array(
                    'success' => false,
                    'error' => Mage::helper('captcha')->__('Incorrect CAPTCHA.')
                );
                $controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            }
        }
        return $this;
    }

    /**
     * Check Captcha On User Login Page
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Captcha_Model_Observer
     */
    public function checkUserLogin($observer)
    {
        $formId = 'user_login';
        $captchaModel = Mage::helper('captcha')->getCaptcha($formId);
        $controller = $observer->getControllerAction();
        $loginParams = $controller->getRequest()->getPost('login');
        $login = array_key_exists('username', $loginParams) ? $loginParams['username'] : null;
        if ($captchaModel->isRequired($login)) {
            $word = $this->_getCaptchaString($controller->getRequest(), $formId);
            if (!$captchaModel->isCorrect($word)) {
//                Mage::getSingleton('customer/session')->addError(Mage::helper('captcha')->__('Incorrect CAPTCHA.'));
                $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                Mage::getSingleton('customer/session')->setUsername($login);
//                $beforeUrl = Mage::getSingleton('customer/session')->getBeforeAuthUrl();
//                $url =  $beforeUrl ? $beforeUrl : Mage::helper('customer')->getLoginUrl();
//                $controller->getResponse()->setRedirect($url);

                $result = array(
                    'success' => false,
                    'error' => Mage::helper('captcha')->__('Incorrect CAPTCHA.')
                );
                $controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            }
        }
        $captchaModel->logAttempt($login);
        return $this;
    }

    /**
     * Get Captcha String
     *
     * @param Varien_Object $request
     * @param string $formId
     * @return string
     */
    protected function _getCaptchaString($request, $formId)
    {
        $captchaParams = $request->getPost(Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE);
        return $captchaParams[$formId];
    }

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
		
        if ($orderCount < 2)
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
            $providerName = Mage::getStoreConfig('trans_email/ident_sales/name'); 
            $providerEmail = Mage::getStoreConfig('trans_email/ident_sales/email'); 	
            $msg = '<table cellspacing="0" cellpadding="0" border="0" height="100%" width="100%"><tr><td><div><h1>Coupon Code Information</h1><p>Hi '.$name.' your Coupon Code is: '.$codeName.' <br/> Valid from from '.$fromDate.' to '.$toDate.'.This is just inform you that by this coupon you will get discount of 10%.</p></div></td></tr></table>';
            $mail = Mage::getModel('core/email');
            $mail->setToName($name);
            $mail->setToEmail($email);
            $mail->setBody($msg);
            $mail->setSubject('Coupon Code information');
            $mail->setFromEmail($providerEmail);
            $mail->setFromName($providerName);
            $mail->setType('html');
         
            try {
                $mail->send();
            }
            catch (Exception $e) {
                $error = Mage::logException($e);
			echo $error ; exit;
            }
        }
        //echo $orderCount."<br>".$providerName."<br><pre>"; print_r($providers); exit; 
    }  

}
