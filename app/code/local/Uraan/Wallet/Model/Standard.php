<?php
class Uraan_Wallet_Model_Standard extends Mage_Payment_Model_Method_Abstract {
	protected $_code = 'wallet';
	
	protected $_isInitializeNeeded      = true;
	protected $_canUseInternal          = true;
	protected $_canUseForMultishipping  = false;
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('wallet/standard');
    }
    
    public function getGatewayId() {
        return $this->getConfigData('gateway_id');
    }
    
	public function getOrderPlaceRedirectUrl() {
		return Mage::getUrl('wallet/payment/redirect', array('_secure' => true));
	}
}
?>