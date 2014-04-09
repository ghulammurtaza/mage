<?php
/**
 * Mageplace Twitter Connector
 *
 * @category	Mageplace_Twitter
 * @package		Mageplace_Twitter_Connect
 * @copyright	Copyright (c) 2011 Mageplace. (http://www.mageplace.com)
 * @license		http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Twitter_Connect_Block_Abstract extends Mage_Core_Block_Template
{
	public function getLinkUrl()
	{
		return Mage::getUrl('twitterconnect/login', array('_secure' => Mage::getModel('core/url')->getSecure()));
	}

	public function getTitle()
	{
		$title = $this->getAttribute('title');

		return $title ? $title : Mage::helper('twitterconnect')->__('Sign in with Twitter');
	}
	
	public function getAttribute($name)
	{
		$value = Mage::helper('twitterconnect')->getCfg($name);

		return $value;
	}
}