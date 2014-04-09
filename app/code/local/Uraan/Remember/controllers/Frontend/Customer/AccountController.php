<?php
require_once Mage::getModuleDir('controllers', 'Mage_Customer').DS.'AccountController.php';

class Uraan_Remember_Frontend_Customer_AccountController extends Mage_Customer_AccountController
{
    public function loginPostAction() 
    { 
        if ($this->_getSession()->isLoggedIn()) { 
            $this->_redirect('*/*/'); 
            return; 
        } 
        $session = $this->_getSession();
        //exit('my local');
        if ($this->getRequest()->isPost()) { 
            $login = $this->getRequest()->getPost('login'); 
            if (!empty($login['username']) && !empty($login['password'])) { 
                try { 
                    $_SESSION['user_name'] = $login['username']; 
                    $_SESSION['user_password'] = $login['password']; 
                    $session->login($login['username'], $login['password']); 
                    if ($session->getCustomer()->getIsJustConfirmed()) { 
                        $this->_welcomeCustomer($session->getCustomer(), true); 
                    } 
                } 
                catch (Exception $e) { 
                    switch ($e->getCode()) { 
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED: 
                            $message = Mage::helper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', 
                            Mage::helper('customer')->getEmailConfirmationUrl($login['username']) 
                            ); 
                            break; 
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD: 
                            $message = $e->getMessage(); 
                            break; 
                        default: 
                            $message = $e->getMessage(); 
                    } 
                    $session->addError($message); 
                    $session->setUsername($login['username']); 
                } 
            } else { 
                $session->addError($this->__('Login and password are required')); 
            } 
        }

        $this->_loginPostRedirect(); 
    }

    protected function _loginPostRedirect() 
    { 
        $session = $this->_getSession(); 

        if (!$session->getBeforeAuthUrl() || $session->getBeforeAuthUrl() == Mage::getBaseUrl() ) {

            // Set default URL to redirect customer to 
            $session->setBeforeAuthUrl(Mage::helper('customer')->getAccountUrl());

            // Redirect customer to the last page visited after logging in 
            if ($session->isLoggedIn()) 
            { 
                if (!Mage::getStoreConfigFlag('customer/startup/redirect_dashboard')) { 
                    if ($referer = $this->getRequest()->getParam(Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME)) { 
                        $referer = Mage::helper('core')->urlDecode($referer); 
                        if ($this->_isUrlInternal($referer)) { 
                            $session->setBeforeAuthUrl($referer); 
                        } 
                    } 
                } 
            } else { 
                $session->setBeforeAuthUrl(Mage::helper('customer')->getLoginUrl()); 
            } 
        } 
        //$customer12 = Mage::getModel(’core/cookie’); 
        //echo $lifetime = Mage::getStoreConfig(self::XML_PATH_COOKIE_LIFETIME_LONG, $customer12->getStore()); 

        if(isset($_REQUEST['rememberme']) && $_REQUEST['rememberme'] == 'remember') 
        { 
            $user_name = $_SESSION['user_name']; 
            $pass_user_name = $_SESSION['user_password']; 
            $cooki = Mage::getModel('core/cookie'); 
            $cooki->set('user_name',$user_name); 
            $cooki->set('pass_user_name',$pass_user_name); 
        }
        else{
            $cooki = Mage::getModel('core/cookie'); 
            $cooki->set('user_name',''); 
            $cooki->set('pass_user_name','');
        } 
        $this->_redirectUrl($session->getBeforeAuthUrl(true)); 
    }

    public function logoutAction()
    {
        //Remove the cookie if someone clicked logout
        if (isset($_COOKIE['info']))
            setcookie('info','',time()-60*60*24*30,'/');

        //Do whatever original method does
        parent::logoutAction();
    }
}
