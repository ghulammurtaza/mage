<?php
/**
 * Mageplace Twitter Connector
 *
 * @category	Mageplace_Twitter
 * @package		Mageplace_Twitter_Connect
 * @copyright	Copyright (c) 2011 Mageplace. (http://www.mageplace.com)
 * @license		http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Twitter_Connect_Model_Observer
{
    public function processCoreBlockAbstractToHtmlAfter($observer)
	{
		static $connect_block_displayed = false;
		
		if($connect_block_displayed || !$this->_isEnabled()) {
			return;
		}
		
		$nameInLayout = $observer->getBlock()->getNameInLayout();

		if(($nameInLayout == 'customer_form_login') && $this->_isLoginPageButtonEnabled()) {
			$html = $this->_getButtonHtml($observer);
			$observer->getTransport()->setHtml($html);
			$connect_block_displayed = true;
			return;
		}

		if(($nameInLayout == 'checkout.onepage.login') && $this->_isCheckoutPageButtonEnabled()) {
			$html = $this->_getButtonHtml($observer);
			$observer->getTransport()->setHtml($html);
			$connect_block_displayed = true;
			return;
		}
	}
	
	protected function _isEnabled()
	{
		static $is_enabled = null;

		if(is_null($is_enabled)) {
			$is_enabled = Mage::helper('twitterconnect')->isEnabled();
		}
		
		return $is_enabled;
	}
	
	protected function _isLoginPageButtonEnabled()
	{
		static $login_page_enabled = null;

		if(is_null($login_page_enabled)) {
			$login_page_enabled = Mage::helper('twitterconnect')->isLoginPageButtonEnabled();
		}
		
		return $login_page_enabled;
	}
	
	public function _isCheckoutPageButtonEnabled()
	{
		static $checkout_page_enabled = null;

		if(is_null($checkout_page_enabled)) {
			$checkout_page_enabled = Mage::helper('twitterconnect')->isCheckoutPageButtonEnabled();
		}
		
		return $checkout_page_enabled;
	}

	private function _getButtonHtml($observer)
	{
		$html = '';
		if($block = $observer->getBlock()->getLayout()->getBlock('customer_form_login_twitterconnect'))	{
			$html = $observer->getTransport()->getHtml() . $block->toHtml();
		}

		return $html;
	}

	function processSalesOrderSaveAfter($observer)
	{
		$order = $observer->getEvent()->getOrder();
		
		if(Mage::registry('twitter_post_order')
		    || !Mage::helper('twitterconnect')->isEnabled()
			|| !Mage::helper('twitterconnect')->isPostEnabled($order)
			|| !($connection = Mage::helper('twitterconnect')->getTwitterConnection())
			|| !$connection->isConnected())
		{
			return $this;
		}
		
		if ($order instanceof Mage_Sales_Model_Order) {
		
			$order_state = $order->getStatus() ? $order->getStatus() : $order->getState();
					
			if (!$order_state) {
				$order_state = (string) $order->getConfig()->getStateDefaultStatus($order->getState());
			}

			if ($order_state != Mage::helper('twitterconnect')->getPostOrderStatus()) {
				return $this;
			}
			
			$isProcessed = Mage::getModel('twitterconnect/order')->setConnection($connection)->post($order);
			if($isProcessed) {
			    Mage::register('twitter_post_order', true);
			}
		}

		return $this;
	}
}