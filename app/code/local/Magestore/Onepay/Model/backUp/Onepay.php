<?php

class Magestore_Onepay_Model_Onepay extends Mage_Payment_Model_Method_Abstract
{
	protected $_code  			= 'onepay';
    protected $_formBlockType 	= 'onepay/form';
    protected $_infoBlockType 	= 'onepay/info';
	
	
	/**
     * All payment information map
     *
     * @var array
     */
    protected $_paymentMap = array('card_type', 'transaction_no', 'command', 'message', 'locale');
	
	protected $_paymentMapFull = array();
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('onepay/onepay');
    }
	
	public function getOnepayUrlPayment() {
		return $this->getConfigData('onepay_url_payment');
	}
	
	public function getActive() {
		return $this->getConfigData('active');
	}
	
	public function getMerchantId() {
		
		return $this->getConfigData('onepay_merchant_id');
	}
	
	public function getTitle() {
		return $this->getConfigData('title');
	}
	
	public function getAccessCode() {
		
		return $this->getConfigData('onepay_merchant_access_code');
	}
	
	public function getPaymentServer() {
		return $this->getConfigData('payment_server');
	}
	
	
	public function getOnepayDescription() {
		return $this->getConfigData('onepay_description');
	}
	
	public function getCustomerIpAddress() {
		return Mage::helper('core/http')->getRemoteAddr();
	}
	
	
	
	public function getOrderPlaceRedirectUrl()
    {
          return Mage::getUrl('onepay/index/redirect', array('_secure' => true));
    }
	
	
	
	
	public function getOnepayFormElements($order, $shippingAddress) {
		$streets 	= $shippingAddress->getStreet();
		$street 	= '';
		if(is_array($streets)) {
			$street = $streets[0];
			if(isset($streets[1]) && $streets[1]) {
				$street .= " - ".$streets[1];
			}
		}
		
		$email = '';
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		
		if($customer->getId()) {
			$email = $customer->getEmail();
		} else {
			$email = $order->getCustomerEmail();
		}
		
        $currency = Mage::app()->getStore()-> getCurrentCurrencyCode();
        if($currency != 'EGP'){
            $currencyrate = Mage::app()->getStore()->getCurrentCurrencyRate();
            $amount = round(($order->getGrandTotal()*100)*(1/$currencyrate));
        }else{
            $amount = round($order->getGrandTotal()*100);
        }
		
		$elements = array(
			'AgainLink'					=> $_SERVER['HTTP_REFERER'],
			'Title'						=> 'PHP VPC 3-Party',
			'vpc_Merchant' 				=> $this->getMerchantId(),
			'vpc_AccessCode' 			=> $this->getAccessCode(),
			'vpc_MerchTxnRef'			=> $order->getId(),
			'vpc_OrderInfo'				=> $email,
			//'vpc_Amount'				=> round($order->getGrandTotal()*100),
            'vpc_Amount'                => $amount,
			'vpc_ReturnURL'				=> Mage::helper('onepay')->getOnepayUrlReturn(),
			'vpc_Version'				=> 1,
			'vpc_Command'				=> 'pay',
			'vpc_Locale'				=> $this->getPaymentServer(),
			'vpc_TicketNo'				=> $this->getCustomerIpAddress(),
			'vpc_SHIP_Street01'			=> $street,
			'vpc_SHIP_Provice'			=> $shippingAddress->getRegionId(),
			'vpc_SHIP_City'				=> $shippingAddress->getCity(),
			'vpc_SHIP_Country'			=> $shippingAddress->getCountry(),
			'vpc_Customer_Phone'		=> $shippingAddress->getTelephone(),
			'vpc_Customer_Email'		=> $email,
			'vpc_Customer_Id'			=> $email,
		);
		
		ksort($elements);
		
		return $elements;
	}
	
	public function isAvailable($quote = null) {
		if($this->getActive()) {
			return true;
		}
		return false;
	}
	
	public function getNotifyCustomer() {
		return $this->getConfigData('notify_customer');
	}
	
	
	public function getResponseDescription($responseCode) {

		switch ($responseCode) {
			case "0" : $result = Mage::helper('onepay')->__("Transaction Successful"); break;
			case "?" : $result = Mage::helper('onepay')->__("Transaction status is unknown"); break;
			case "1" : $result = Mage::helper('onepay')->__("Bank system reject"); break;
			case "2" : $result = Mage::helper('onepay')->__("Bank Declined Transaction"); break;
			case "3" : $result = Mage::helper('onepay')->__("No Reply from Bank"); break;
			case "4" : $result = Mage::helper('onepay')->__("Expired Card"); break;
			case "5" : $result = Mage::helper('onepay')->__("Insufficient funds"); break;
			case "6" : $result = Mage::helper('onepay')->__("Error Communicating with Bank"); break;
			case "7" : $result = Mage::helper('onepay')->__("Payment Server System Error"); break;
			case "8" : $result = Mage::helper('onepay')->__("Transaction Type Not Supported"); break;
			case "9" : $result = Mage::helper('onepay')->__("Bank declined transaction (Do not contact Bank)"); break;
			case "A" : $result = Mage::helper('onepay')->__("Transaction Aborted"); break;
			case "C" : $result = Mage::helper('onepay')->__("Transaction Cancelled"); break;
			case "D" : $result = Mage::helper('onepay')->__("Deferred transaction has been received and is awaiting processing"); break;
			case "E" : $result = Mage::helper('onepay')->__("Referred"); break;
			case "F" : $result = Mage::helper('onepay')->__("3D Secure Authentication failed"); break;
			case "I" : $result = Mage::helper('onepay')->__("Card Security Code verification failed"); break;
			case "L" : $result = Mage::helper('onepay')->__("Shopping Transaction Locked (Please try the transaction again later)"); break;
			case "N" : $result = Mage::helper('onepay')->__("Cardholder is not enrolled in Authentication scheme"); break;
			case "P" : $result = Mage::helper('onepay')->__("Transaction has been received by the Payment Adaptor and is being processed"); break;
			case "R" : $result = Mage::helper('onepay')->__("Transaction was not processed - Reached limit of retry attempts allowed"); break;
			case "S" : $result = Mage::helper('onepay')->__("Duplicate SessionID (OrderInfo)"); break;
			case "T" : $result = Mage::helper('onepay')->__("Address Verification Failed"); break;
			case "U" : $result = Mage::helper('onepay')->__("Card Security Code Failed"); break;
			case "V" : $result = Mage::helper('onepay')->__("Address Verification and Card Security Code Failed"); break;
			default  : $result = Mage::helper('onepay')->__("Unable to be determined"); 
		}
		return $result;
	}
	
	
}