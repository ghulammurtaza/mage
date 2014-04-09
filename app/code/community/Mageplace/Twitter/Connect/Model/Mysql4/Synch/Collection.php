<?php
/**
 * Mageplace Twitter Connector
 *
 * @category	Mageplace_Twitter
 * @package		Mageplace_Twitter_Connect
 * @copyright	Copyright (c) 2011 Mageplace. (http://www.mageplace.com)
 * @license		http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Twitter_Connect_Model_Mysql4_Synch_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	protected function _construct()
	{
		$this->_init('twitterconnect/synch');
	}

	public function getSynchByCookie($cookie)
	{
        static $return = array();

        if(!array_key_exists($cookie, $return)) {
            $items = $this->addFilter('twitterconnect_cookie', $cookie)
                ->setOrder($this->getResource()->getIdFieldName(), 'DESC')
                ->getItems();

            $return[$cookie] = (empty($items) ? null : array_shift($items));
        }

        return $return[$cookie];
	}

	public function getSynchByTwitterId($id)
	{
        static $return = array();

        if(!array_key_exists($id, $return)) {
            $items = $this->addFilter('twitter_id', $id)
                ->setOrder($this->getResource()->getIdFieldName(), 'DESC')
                ->getItems();

            $return[$id] = (empty($items) ? null : array_shift($items));
        }

        return $return[$id];
	}
}