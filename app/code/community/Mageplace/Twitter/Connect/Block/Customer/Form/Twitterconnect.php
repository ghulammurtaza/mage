<?php
/**
 * Mageplace Twitter Connector
 *
 * @category	Mageplace_Twitter
 * @package		Mageplace_Twitter_Connect
 * @copyright	Copyright (c) 2011 Mageplace. (http://www.mageplace.com)
 * @license		http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Twitter_Connect_Block_Customer_Form_Twitterconnect extends Mageplace_Twitter_Connect_Block_Abstract // Mage_Core_Block_Template
{

	public function __construct()
	{
		parent::__construct();
	}

	public function getIsPostToTwitter()
	{
		return Mage::helper('twitterconnect')->isPostEnabled();
	}

	public function getAction()
	{
		return $this->getUrl('*/*/save');
	}

}
