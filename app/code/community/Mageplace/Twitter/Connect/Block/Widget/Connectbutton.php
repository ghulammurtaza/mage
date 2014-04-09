<?php
/**
 * Mageplace Twitter Connector
 *
 * @category	Mageplace_Twitter
 * @package		Mageplace_Twitter_Connect
 * @copyright	Copyright (c) 2011 Mageplace. (http://www.mageplace.com)
 * @license		http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Twitter_Connect_Block_Widget_Connectbutton
	extends Mageplace_Twitter_Connect_Block_Customer_Connectbutton
	implements Mage_Widget_Block_Interface
{
	public function getTitle()
	{
		$title = $this->getData('title');
		
		return $title ? $title : parent::getTitle();
	}
	
	protected function _toHtml()
	{
		if(Mage::helper('twitterconnect')->isWidgetButtonEnabled()) {
			return parent::_toHtml();
		} else {
			return '';
		}
    }
}