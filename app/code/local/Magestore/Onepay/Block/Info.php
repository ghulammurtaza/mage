<?php
class Magestore_Onepay_Block_Info extends Mage_Payment_Block_Info_Cc
{
	
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('onepay/info.phtml');
    }
	
	public function getOrder() {
		return Mage::registry('current_order');
	}
	
	/**
     * Prepare PayPal-specific payment information
     *
     * @param Varien_Object|array $transport
     * return Varien_Object
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        $transport = parent::_prepareSpecificInformation($transport);
        $payment = $this->getInfo();
        $onepay = Mage::getModel('onepay/onepay');
        
		$info = $onepay->getPaymentInfo($payment);
        
        return $transport->addData($info);
    }
}