<?php
/**
 * Mageplace Twitter Connector
 *
 * @category	Mageplace_Twitter
 * @package		Mageplace_Twitter_Connect
 * @copyright	Copyright (c) 2011 Mageplace. (http://www.mageplace.com)
 * @license		http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Twitter_Connect_ManageController extends Mage_Core_Controller_Front_Action
{
	/**
	 * Action predispatch
	 *
	 * Check customer authentication for some actions
	 */
	public function preDispatch()
	{
		parent::preDispatch();
		if (!Mage::getSingleton('customer/session')->authenticate($this)) {
			$this->setFlag('', 'no-dispatch', true);
		}
	}

	public function indexAction()
	{
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->_initLayoutMessages('catalog/session');

		if ($block = $this->getLayout()->getBlock('customer_twitterconnect')) {
			$block->setRefererUrl($this->_getRefererUrl());
		}
		$this->getLayout()->getBlock('head')->setTitle($this->__('Twitter Connect'));
		$this->renderLayout();
	}

	public function saveAction()
	{
		if (!$this->_validateFormKey()) {
			return $this->_redirect('customer/account/');
		}
		try {
			$twitter_post = (boolean)$this->getRequest()->getParam('twitter_post', false);
			Mage::getSingleton('customer/session')->getCustomer()
			->setStoreId(Mage::app()->getStore()->getId())
			->setData('twitter_post', $twitter_post)
			->save();
			if ($twitter_post) {
				Mage::getSingleton('customer/session')->addSuccess($this->__('The parameter has been saved.'));
			} else {
				Mage::getSingleton('customer/session')->addSuccess($this->__('The parameter has been removed.'));
			}

		} catch (Exception $e) {
			Mage::getSingleton('customer/session')->addError($this->__('An error occurred while saving your parameter.'));
		}
		$this->_redirect('customer/account/');
	}
}
