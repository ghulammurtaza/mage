<?php
class Magestore_Onepay_Block_Form extends Mage_Payment_Block_Form
{
	/**
     * Payment method code
     * @var string
     */
    protected $_methodCode = 'onepay';
	
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('onepay/form.phtml')
			->setRedirectMessage(
                Mage::helper('onepay')->__('You will be redirected to OnePay website when you place an order.')
            );
    }
	
	/**
     * Payment method code getter
     * @return string
     */
    public function getMethodCode()
    {
        return $this->_methodCode;
    }
	
}