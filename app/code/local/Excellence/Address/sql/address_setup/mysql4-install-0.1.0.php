<?php

$installer = $this;

$installer->startSetup();

$this->addAttribute('customer_address', 'appartment_number', array(
	'type' => 'varchar',
	'input' => 'text',
	'label' => 'Appartment number',
	'global' => 1,
	'visible' => 1,
	'required' => 0,
	'user_defined' => 1,
	'visible_on_front' => 1
));


if (version_compare(Mage::getVersion(), '1.6.0', '<='))
{
	$customer = Mage::getModel('customer/address');
	$attrSetId = $customer->getResource()->getEntityType()->getDefaultAttributeSetId();
	$this->addAttributeToSet('customer_address', $attrSetId, 'General', 'appartment_number');
}

if (version_compare(Mage::getVersion(), '1.4.2', '>='))
{
	Mage::getSingleton('eav/config')
	->getAttribute('customer_address', 'appartment_number')
	->setData('used_in_forms', array('customer_register_address','customer_address_edit','adminhtml_customer_address'))
	->save();
}

$tablequote = $this->getTable('sales/quote_address');
$installer->run("
ALTER TABLE  $tablequote ADD  `appartment_number` varchar(255) NOT NULL
");

$tablequote = $this->getTable('sales/order_address');
$installer->run("
ALTER TABLE  $tablequote ADD  `appartment_number` varchar(255) NOT NULL
");
  

//-------------------------------- 2nd-----------


$this->addAttribute('customer_address', 'floor_number', array(
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'Floor number',
    'global' => 1,
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
    'visible_on_front' => 1
));


if (version_compare(Mage::getVersion(), '1.6.0', '<='))
{
    $customer = Mage::getModel('customer/address');
    $attrSetId = $customer->getResource()->getEntityType()->getDefaultAttributeSetId();
    $this->addAttributeToSet('customer_address', $attrSetId, 'General', 'floor_number');
}

if (version_compare(Mage::getVersion(), '1.4.2', '>='))
{
    Mage::getSingleton('eav/config')
    ->getAttribute('customer_address', 'floor_number')
    ->setData('used_in_forms', array('customer_register_address','customer_address_edit','adminhtml_customer_address'))
    ->save();
}

$tablequote = $this->getTable('sales/quote_address');
$installer->run("
ALTER TABLE  $tablequote ADD  `floor_number` varchar(255) NOT NULL
");

$tablequote = $this->getTable('sales/order_address');
$installer->run("
ALTER TABLE  $tablequote ADD  `floor_number` varchar(255) NOT NULL
");

//---------------------- 3rd ----------------------------------------------------


$this->addAttribute('customer_address', 'building_number', array(
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'Buiding number',
    'global' => 1,
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
    'visible_on_front' => 1
));


if (version_compare(Mage::getVersion(), '1.6.0', '<='))
{
    $customer = Mage::getModel('customer/address');
    $attrSetId = $customer->getResource()->getEntityType()->getDefaultAttributeSetId();
    $this->addAttributeToSet('customer_address', $attrSetId, 'General', 'building_number');
}

if (version_compare(Mage::getVersion(), '1.4.2', '>='))
{
    Mage::getSingleton('eav/config')
    ->getAttribute('customer_address', 'building_number')
    ->setData('used_in_forms', array('customer_register_address','customer_address_edit','adminhtml_customer_address'))
    ->save();
}

$tablequote = $this->getTable('sales/quote_address');
$installer->run("
ALTER TABLE  $tablequote ADD  `building_number` varchar(255) NOT NULL
");

$tablequote = $this->getTable('sales/order_address');
$installer->run("
ALTER TABLE  $tablequote ADD  `building_number` varchar(255) NOT NULL
");


$installer->endSetup(); 