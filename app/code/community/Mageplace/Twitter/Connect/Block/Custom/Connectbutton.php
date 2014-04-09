<?php
/**
 * Mageplace Twitter Connector
 *
 * @category	Mageplace_Twitter
 * @package		Mageplace_Twitter_Connect
 * @copyright	Copyright (c) 2011 Mageplace. (http://www.mageplace.com)
 * @license		http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Twitter_Connect_Block_Custom_Connectbutton extends Mageplace_Twitter_Connect_Block_Customer_Connectbutton
{
	protected function _toHtml()
	{
		if(Mage::helper('twitterconnect')->isCustomButtonEnabled()) {
			return parent::_toHtml();
		} else {
			return '';
		}
    }
}