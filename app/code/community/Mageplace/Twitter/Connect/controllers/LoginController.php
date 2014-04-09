<?php

/**
 * Mageplace Twitter Connector
 *
 * @category	Mageplace_Twitter
 * @package		Mageplace_Twitter_Connect
 * @copyright	Copyright (c) 2011 Mageplace. (http://www.mageplace.com)
 * @license		http://www.mageplace.com/disclaimer.html
 */
class Mageplace_Twitter_Connect_LoginController extends Mage_Core_Controller_Front_Action
{

	public function indexAction()
	{
#Mage::log(__METHOD__.': START');

		if (!Mage::helper('twitterconnect')->isEnabled()) {
			$this->_forward('noRoute');
			return;
		}

		if (Mage::getSingleton('customer/session')->isLoggedIn()) {
			$this->_redirectUrl(Mage::helper('customer')->getDashboardUrl());
			return;
		}
		try {

			$connection = Mage::helper('twitterconnect')->getTwitterConnection();
			/* @var $connection Mageplace_Twitter_Connect_Model_Session */
			if (!$connection->isConnected()) {
#Mage::log(__METHOD__.': CONNECT');
				$url = $connection->connect();
				Mage::register('twitter_auth_url', $url);
				$this->loadLayout()->renderLayout();

				return $this;
			}

			$customer = Mage::getModel('twitterconnect/customer')
					->setConnection($connection)
					->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
					->loadByTwitterUserId();

			if (!$customer->getId()) {
#Mage::log(__METHOD__.': CREATE');
				$customer->create();
				Mage::getSingleton('customer/session')->addError(Mage::helper('twitterconnect')->__('Please insert you email, otherwhise system will remember your Twitter screenname only and you will not be able to enjoy all shop features.Remember to change your password.'));
				echo "<script>window.opener.redirectUrl = '" . Mage::helper('customer')->getEditUrl() . "';</script>";
			}

			if ($customer->getId()) {
#Mage::log(__METHOD__.': LOGIN');
				$customer->login();
			}

#Mage::log(__METHOD__.': END');

			echo "<script>window.close();</script>";
			exit();
		} catch (Exception $e) {
			echo "<script>window.close();</script>";
			Mage::logException($e);
			$message = Mage::helper('twitterconnect')->__('Cannot login via Twitter!');
			Mage::getSingleton('customer/session')->addError($message);
			$this->_redirectUrl(Mage::helper('customer')->getDashboardUrl());
		}
	}

	public function callbackAction()
	{
#Mage::log(__METHOD__.': CALLBACK');
		if (Mage::helper('twitterconnect')->getTwitterConnection()->callback()) {
#Mage::log(__METHOD__.': FORWARD TO INDEX');
			$this->_forward('index');
		}
	}

	public function clearAction()
	{
		Mage::helper('twitterconnect')->getTwitterConnection()->clearConnection();
		$this->_forward('index');
	}

}
