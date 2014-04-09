<?php
/**
 * Mageplace Twitter Connector
 *
 * @category	Mageplace_Twitter
 * @package		Mageplace_Twitter_Connect
 * @copyright	Copyright (c) 2011 Mageplace. (http://www.mageplace.com)
 * @license		http://www.mageplace.com/disclaimer.html
 */

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->addAttribute(
	'customer',
	'twitter_post',
	array(
		'type'		=> 'int',
		'label'		=> 'Post order details to Twitter',
		'input'		=> 'boolean',
		'default'   => '1',
		'visible'	=> true,
		'required'  => false,
	)
);

$eavConfig = Mage::getSingleton('eav/config');
$attribute = $eavConfig->getAttribute('customer', 'twitter_post');
$attribute->setData('used_in_forms', array('customer_account_edit','customer_account_create','adminhtml_customer'));
$attribute->save();

$query = "
CREATE TABLE IF NOT EXISTS `{$this->getTable('twitterconnect/synch')}` (
	`synch_id`				int(10) unsigned NOT NULL AUTO_INCREMENT,
	`customer_id`			int(10) unsigned NULL,
	`twitter_id`			int(12) unsigned NULL,
	`oauth_token`			varchar(50) NOT NULL,
	`oauth_token_secret`	varchar(50) NOT NULL,
	`twitterconnect_cookie`	varchar(20) NOT NULL,
	PRIMARY KEY (`synch_id`),
	CONSTRAINT `FK_TWITTERCONNECT_SYNCH_CUSTOMER_ID` FOREIGN KEY (`customer_id`) REFERENCES `{$this->getTable('customer/entity')}` (`entity_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=UTF8 COMMENT='Twitter Connect Synch Table';
";

$installer = $this;
$installer->startSetup();
$installer->run($query);
$installer->endSetup();
