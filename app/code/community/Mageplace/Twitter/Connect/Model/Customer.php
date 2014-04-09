<?php
/**
 * Mageplace Twitter Connector
 *
 * @category	Mageplace_Twitter
 * @package		Mageplace_Twitter_Connect
 * @copyright	Copyright (c) 2011 Mageplace. (http://www.mageplace.com)
 * @license		http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Twitter_Connect_Model_Customer extends Mage_Customer_Model_Customer
{
	protected $_twitter;

	/**
	 * Load customer by twitter user id
	 *
	 * @return  Mageplace_Twitter_Connect_Model_Customer
	 */
	public function loadByTwitterUserId($twitterCustomerId=null)
	{
	    if(is_null($twitterCustomerId) && ($userInfo=$this->getUserInfo())) {
			$twitterCustomerId = $userInfo->id;
		}

		if(!$twitterCustomerId) {
            return $this;
		}

		if(!$customerId = $this->getConnection()->getCustomerId()) {
            $synch = Mage::getResourceModel('twitterconnect/synch_collection')->getSynchByTwitterId($twitterCustomerId);
            if(!($synch instanceof Mageplace_Twitter_Connect_Model_Synch) || (!$customerId = $synch->getCustomerId())) {
                return $this;
            }
		}

        $this->load($customerId);
        if (!$this->getId()) {
            return $this;
        }

		return $this;
	}

	public function create()
	{
		$customerData = $this->getUserInfo();
		if(empty($customerData->screen_name)) {
			Mage::getSingleton('customer/session')->addException(null, $this->__('Cannot create the customer.'));
		}

		$importData['website']		= Mage::app()->getStore()->getWebsite()->getCode();
		$importData['email']		= $customerData->screen_name;

		$name_parts = explode(' ', $customerData->name);
		if(is_array($name_parts)) {
		    $parts_count = count($name_parts);
		    if($parts_count == 2) {
                $importData['firstname'] = $name_parts[0];
                $importData['lastname']  = $name_parts[1];
		    } else if($parts_count == 3) {
                if(strpos($name_parts[0], '.')) {
                    $importData['prefix']    = $name_parts[0];
                    $importData['firstname'] = $name_parts[1];
                    $importData['lastname']  = $name_parts[2];
                } else if(strpos($name_parts[2], '.')) {
                    $importData['firstname'] = $name_parts[0];
                    $importData['lastname']  = $name_parts[1];
                    $importData['suffix']    = $name_parts[2];
                } else {
                    $importData['firstname']  = $name_parts[0];
                    $importData['middlename'] = $name_parts[1];
                    $importData['lastname']   = $name_parts[2];
                }
		    } else if($parts_count == 4) {
                if(strpos($name_parts[3], '.')) {
                    if(strpos($name_parts[0], '.')) {
                        $importData['prefix']    = $name_parts[0];
                        $importData['firstname'] = $name_parts[1];
                        $importData['lastname']  = $name_parts[2];
                    } else {
                        $importData['firstname']  = $name_parts[0];
                        $importData['middlename'] = $name_parts[1];
                        $importData['lastname']   = $name_parts[2];
                    }
                    $importData['suffix']   = $name_parts[3];
                } else {
                    $importData['prefix']     = $name_parts[0];
                    $importData['firstname']  = $name_parts[0];
                    $importData['middlename'] = $name_parts[1];
                    $importData['lastname']   = $name_parts[2];
                }
		    } else if($parts_count == 5) {
                    $importData['prefix']     = $name_parts[0];
                    $importData['firstname']  = $name_parts[1];
                    $importData['middlename'] = $name_parts[2];
                    $importData['lastname']   = $name_parts[3];
                    $importData['suffix']     = $name_parts[4];
		    } else {
        		$importData['firstname'] = $customerData->name;
        		$importData['lastname']  = ' ';
		    }
		} else {
    		$importData['firstname'] = $customerData->name;
    		$importData['lastname']  = ' ';
		}

		$convertAdapter = Mage::getModel('customer/convert_adapter_customer');
		/* @var $convertAdapter Mage_Customer_Model_Convert_Adapter_Customer */
		if(($customerGroups = $convertAdapter->getCustomerGroups()) && !empty($customerGroups) && is_array($customerGroups)) {
			if(array_key_exists('General', $customerGroups)) {
				$importData['group']	= 'General';
				$importData['group_id']	= 'General';
			} else {
				$group_keys = array_keys($customerGroups);
				$importData['group']	= $group_keys[0];
				$importData['group_id'] = $group_keys[0];
			}

		} else {
			$importData['group']	= 'General';
			$importData['group_id']	= 'General';
		}

		try {
		    $convertAdapter->saveRow($importData);
		} catch(Exception $e) {
			Mage::getSingleton('customer/session')->addException($e, $this->__('Cannot create the customer.'));
		}

		$this->load($convertAdapter->getCustomerModel()->getId());

		if($this->getConfirmation()) {
            $this->setConfirmation(null)->save();
            $this->setIsJustConfirmed(true);
		}

		Mage::getModel('twitterconnect/synch')
		    ->setData('customer_id',            $this->getId())
		    ->setData('twitter_id',             $customerData->id)
		    ->setData('oauth_token',            $this->getConnection()->getOauthToken())
		    ->setData('oauth_token_secret',     $this->getConnection()->getOauthTokenSecret())
		    ->setData('twitterconnect_cookie',  $this->getConnection()->getCookieValue())
		    ->save();

		return $this;
	}

	public function login()
	{
		$customer_session = Mage::getSingleton('customer/session');
		/* @var $customer_session Mage_Customer_Model_Session */
		$customer_session->setCustomerAsLoggedIn($this);
	}

	public function getUserInfo()
	{
		$userData = $this->_getData('user_info');
		if(is_null($userData)) {
			$userData = $this->getConnection()->getAccountCredentials();
			$this->setData('user_info', $userData);
		}

		return $userData;
	}
}