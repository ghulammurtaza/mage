<?php
/**
 * Mageplace Twitter Connector
 *
 * @category	Mageplace_Twitter
 * @package		Mageplace_Twitter_Connect
 * @copyright	Copyright (c) 2011 Mageplace. (http://www.mageplace.com)
 * @license		http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Twitter_Connect_Helper_Data extends Mage_Core_Helper_Abstract
{
	const TWITTERCONNECT		= 'twitterconnect';

	const TAB_GENERAL			= 'general';
	const TAB_CONTENT			= 'content';

	const VAR_ENABLE_EXTENSION	= 'enable_extension';
	const VAR_CALLBACK_URL		= 'callback_url';
	const VAR_CONSUMER_KEY		= 'consumer_key';
	const VAR_CONSUMER_SECRET	= 'consumer_secret';

	const VAR_SHOW_IN_LOGIN		= 'show_in_login';
	const VAR_SHOW_IN_CHECKOUT	= 'show_in_checkout';
	const VAR_SHOW_CUSTOM		= 'show_custom';
	const VAR_SHOW_WIDGET		= 'show_widget';
	const VAR_ORDERSTATUS		= 'orderstatus';


	public function isEnabled()
	{
		static $is_enabled = null;

		if(is_null($is_enabled)) {
			$is_enabled = $this->getCfg(self::VAR_ENABLE_EXTENSION, false);
		}
		
		return $is_enabled;
	}
	
	public function isLoginPageButtonEnabled()
	{
		static $is_enabled = null;

		if(is_null($is_enabled)) {
			$is_enabled = $this->isEnabled() && $this->getCfg(self::VAR_SHOW_IN_LOGIN, false);
		}
		
		return $is_enabled;
	}
	
	public function isCheckoutPageButtonEnabled()
	{
		static $is_enabled = null;

		if(is_null($is_enabled)) {
			$is_enabled = $this->isEnabled() && $this->getCfg(self::VAR_SHOW_IN_CHECKOUT, false);
		}
		
		return $is_enabled;
	}
	
	public function isCustomButtonEnabled()
	{
		static $is_enabled = null;

		if(is_null($is_enabled)) {
			$is_enabled = $this->isEnabled() && $this->getCfg(self::VAR_SHOW_CUSTOM, false);
		}
		
		return $is_enabled;
	}
	
	public function isWidgetButtonEnabled()
	{
		static $is_enabled = null;

		if(is_null($is_enabled)) {
			$is_enabled = $this->isEnabled() && $this->getCfg(self::VAR_SHOW_WIDGET, false);
		}
		
		return $is_enabled;
	}

	public function isPostEnabled($order = null)
	{
		if (!Mage::app()->getStore()->isAdmin()) {
			return (Mage::getSingleton('customer/session')->getCustomer()->getData('twitter_post') ? true : false);
		} else {
			if (!($customerId = $order->getCustomerId())
					|| !($customer = Mage::getModel('customer/customer')->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->load($customerId))
					|| !($customer instanceof Mage_Customer_Model_Customer)) {
				return false;
			}
			
			return ($customer->getData('twitter_post') ? true : false);
		}
		
		
		
	}

	public function getPostOrderStatus()
	{
		if (!$orderstatus = $this->getCfg(self::VAR_ORDERSTATUS)) {
			return Mage_Sales_Model_Order::STATE_COMPLETE;
		}

		return $orderstatus;
	}

	public function showEmailPage()
	{
		return (Mage::getStoreConfig(self::TWITTERCONNECT.'/'.self::TAB_GENERAL.'/'.self::VAR_ENABLE_EXTENSION) ? true : false);
	}

	public function getCfg($config, $default=null, $tab=null)
	{
		switch($config) {
			case (self::VAR_SHOW_IN_LOGIN):
			case (self::VAR_SHOW_IN_CHECKOUT):
			case (self::VAR_SHOW_CUSTOM):
			case (self::VAR_SHOW_WIDGET):
				$tab = self::TAB_CONTENT;
			break;

			default:
				$tab = self::TAB_GENERAL;
		}

		$value = Mage::getStoreConfig(self::TWITTERCONNECT.'/'.$tab.'/'.$config, Mage::app()->getStore());
		switch($config) {
			case (self::VAR_ENABLE_EXTENSION):
			case (self::VAR_SHOW_IN_LOGIN):
			case (self::VAR_SHOW_IN_CHECKOUT):
			case (self::VAR_SHOW_CUSTOM):
			case (self::VAR_SHOW_WIDGET):
				$value = (bool)$value;
			break;

			case (self::VAR_CALLBACK_URL):
				if($value) {
					$value = (strtolower(substr($value,0,4)) === 'http') ? $value : Mage::getUrl($value);
				}
			break;
		}

		if(!$value && !is_null($default)) {
			$value = $default;
		}

		if(is_null($value)) {
			$value = '';
		}

		return $value;
	}

	public function getTwitterConnection()
	{
		$connection = Mage::getSingleton('twitterconnect/session');
		/* @var $connection Mageplace_Twitter_Connect_Model_Session*/

		if(!$connection->isInited()) {
			$connection->setConsumerKey($this->getCfg(self::VAR_CONSUMER_KEY))
				->setConsumerSecret($this->getCfg(self::VAR_CONSUMER_SECRET))
				->setCallbackUrl($this->getCfg(self::VAR_CALLBACK_URL));
		}

		return $connection;
	}
}
