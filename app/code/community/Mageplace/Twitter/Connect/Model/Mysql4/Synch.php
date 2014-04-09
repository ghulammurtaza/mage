<?php
/**
 * Mageplace Twitter Connector
 *
 * @category	Mageplace_Twitter
 * @package		Mageplace_Twitter_Connect
 * @copyright	Copyright (c) 2011 Mageplace. (http://www.mageplace.com)
 * @license		http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Twitter_Connect_Model_Mysql4_Synch extends Mage_Core_Model_Mysql4_Abstract
{
	protected function _construct()
	{
		$this->_init('twitterconnect/synch', 'synch_id');
	}
}