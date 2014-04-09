<?php
class Magestore_Onepay_IndexController extends Mage_Core_Controller_Front_Action
{
	/**
     * Order instance
     */
    protected $_order;

    /**
     *  Get order
     *
     *  @return	  Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if ($this->_order == null) {
        }
        return $this->_order;
    }

    /**
     * Send expire header to ajax response
     *
     */
    protected function _expireAjax()
    {
        if (!Mage::getSingleton('checkout/session')->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    }
	
	
	/**
     * Get singleton with Onepay order transaction information
     *
     * @return Magestore_Onepay_Model_Onepay
     */
    public function getOnepay()
    {
        return Mage::getSingleton('onepay/onepay');
    }
	
	
	/**
     * When a customer chooses Onepay on Checkout/Payment page
     *
     */
    public function redirectAction()
    {
		// $ip = Mage::helper('core/http')->getRemoteAddr();
		// var_dump($ip);die();
    	$session = Mage::getSingleton('checkout/session');
        $session->setOnepayQuoteId($session->getQuoteId());
        $this->getResponse()->setBody($this->getLayout()->createBlock('onepay/redirect')->toHtml());
        $session->unsQuoteId();
		
    }
	
	
	
	
	
	/**
     * when onepay returns
     * The order information at this point is in POST
     * variables.  However, you don't want to "process" the order until you
     * get validation from the IPN.
     */
    public function  returnAction()
    {
	
		$requests = $this->getRequest()->getParams();
		// var_dump($requests);die();

		// This is secret for encoding the MD5 hash
		// This secret will vary from merchant to merchant
		// To not create a secure hash, let SECURE_SECRET be an empty string - ""
		// $SECURE_SECRET = "secure-hash-secret";
		//$SECURE_SECRET 			= "E49780B4C8FDB4E38222ADE7F3B97CCA";
        $SECURE_SECRET             = "E49780B4C8FDB4E38222ADE7F3B97CCA";
		
		
		// If there has been a merchant secret set then sort and loop through all the
		// data in the Virtual Payment Client response. While we have the data, we can
		// append all the fields that contain values (except the secure hash) so that
		// we can create a hash and validate it against the secure hash in the Virtual
		// Payment Client response.

		// NOTE: If the vpc_TxnResponseCode in not a single character then
		// there was a Virtual Payment Client error and we cannot accurately validate
		// the incoming data from the secure hash. */

		// get and remove the vpc_TxnResponseCode code from the response fields as we
		// do not want to include this field in the hash calculation
		$vpc_Txn_Secure_Hash 	= $this->null2unknown($requests['vpc_SecureHash']);
		$orderInfo          	= $this->null2unknown($this->getRequest()->getParam('vpc_OrderInfo'));
		$vpc_AcqResponseCode	= $this->null2unknown($this->getRequest()->getParam('vpc_AcqResponseCode'));
		$txnResponseCode		= $this->null2unknown($this->getRequest()->getParam('vpc_TxnResponseCode'));
		unset($requests['vpc_SecureHash']);
		
			
		
		$amount          		= $this->null2unknown($this->getRequest()->getParam('vpc_Amount'));
		$locale          		= $this->null2unknown($this->getRequest()->getParam('vpc_Locale'));
		$message          		= $this->null2unknown($this->getRequest()->getParam('vpc_Message'));
		$MerchTxnRef			= $this->null2unknown($requests['vpc_MerchTxnRef']);
		$merchantID          	= $this->null2unknown($this->getRequest()->getParam('vpc_Merchant'));
		$version         		= $this->null2unknown($this->getRequest()->getParam('vpc_Version'));
		$command         		= $this->null2unknown($this->getRequest()->getParam('vpc_Command'));
		$cardType        		= $this->null2unknown($this->getRequest()->getParam('vpc_Card'));
		$transactionNo   		= $this->null2unknown($this->getRequest()->getParam('vpc_TransactionNo'));
		
		$paymentInfo 					= array();
		$paymentInfo['card_type']		= array('label'=>Mage::helper('onepay')->__('Card type'), 'value'=>$cardType);
		$paymentInfo['transaction_no']	= array('label'=>Mage::helper('onepay')->__('Transaction Number'), 'value'=>$transactionNo);
		$paymentInfo['command']			= array('label'=>Mage::helper('onepay')->__('Command type'), 'value'=>$command);
		$paymentInfo['message']			= array('label'=>Mage::helper('onepay')->__('Message'), 'value'=>$message);
		$paymentInfo['locale']			= array('label'=>Mage::helper('onepay')->__('Locale'), 'value'=>$locale);
		
		
		
		
		// Mage::getSingleton('adminhtml/session')->setPaymentInfo($paymentInfo);
		// Mage::getModel('payment/info')->setAdditionalInformation('card_type', $cardType );
		// Mage::getModel('payment/info')->setAdditionalInformation('transaction_no', $transactionNo );
		// Mage::getModel('payment/info')->setAdditionalInformation('command', $command );
		// Mage::getModel('payment/info')->setAdditionalInformation('message', $message );
		// Mage::getModel('payment/info')->setAdditionalInformation('locale', $locale );
		
		// unset($this->getRequest()->getParam('vpc_SecureHash'));
		
		// set a flag to indicate if hash has been validated
		$errorExists = false;
		
		
		
		if (strlen($SECURE_SECRET) > 0 && $txnResponseCode != "7" && $txnResponseCode != "No Value Returned") {

			ksort($requests);
			$md5HashData = $SECURE_SECRET;	
			
			// sort all the incoming vpc response fields and leave out any with no value
			foreach($requests as $key => $value) {
				if ($key != "vpc_SecureHash" or strlen($value) > 0) {
					$md5HashData .= $value;
				}
			}
			
			// var_dump($md5HashData); die();
			// Validate the Secure Hash (remember MD5 hashes are not case sensitive)
			// This is just one way of displaying the result of checking the hash.
			// In production, you would work out your own way of presenting the result.
			// The hash check is all about detecting if the data has changed in transit.
			// var_dump(strtoupper($vpc_Txn_Secure_Hash) ." ==> ". strtoupper(md5($md5HashData))); die('asdflajdflajdsfl');
			if (strtoupper($vpc_Txn_Secure_Hash) == strtoupper(md5($md5HashData))) {
				// Secure Hash validation succeeded, add a data field to be displayed
				// later.
				$hashValidated = "CORRECT";
			} else {
				// Secure Hash validation failed, add a data field to be displayed
				// later.
				$hashValidated = "INVALID HASH";
			}
		} else {
			
			// Secure Hash was not validated, add a data field to be displayed later.
			$hashValidated = "INVALID HASH";
		}
		
		if($MerchTxnRef) {
		
			$order = Mage::getModel('sales/order')->load($MerchTxnRef);
			
			$language = Mage::helper('onepay')->getLocaleOption();
			
			$orderPayment = $order->getPayment();
			$orderPayment->setAdditionalInformation('Card type', $cardType);
			$orderPayment->setAdditionalInformation('Transaction ID', $transactionNo);
			$orderPayment->setAdditionalInformation('Command', $command);
			$orderPayment->setAdditionalInformation('Message', $message);
			$orderPayment->setAdditionalInformation('Locale', $language[$locale]);
			
			
			
			$status = '';
			$transStatus = "";
			if($hashValidated=="CORRECT" && $txnResponseCode=="0"){
			
				$transStatus = "Success";
				if($order->getShippingAddress()) {
					$status = 'processing';
				} else {
					$status = 'complete';
				}
				
				
			}elseif ($hashValidated=="CORRECT" && $txnResponseCode!="0"){
				$transStatus = "False";
				$status = 'pending';
				
			}elseif ($hashValidated=="INVALID HASH"){
				$transStatus = "Pendding";
				$status = 'pending_onepay';
			}
			// var_dump($txnResponseCode);die();
			$comment = '';
			$comment .= Mage::getModel('onepay/onepay')->getResponseDescription($txnResponseCode);
			$notify	= Mage::getModel('onepay/onepay')->getNotifyCustomer();
			
			try {
			
				$order->setStatus($status);
				$order->addStatusToHistory($status, $comment, $notify);
				
				$order->save();
			
				$order->sendOrderUpdateEmail($notify, $comment);
				
				if($status == 'pending') {
					$this->_redirect('checkout/onepage/failure', array('_secure'=>true));
					return;
				}
				// Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
				$this->_redirect('checkout/onepage/success', array('_secure'=>true));
				return;
				
			} catch(Exception $e) {
				// Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
				$this->_redirect('checkout/onepage/failure', array('_secure'=>true));
			}
		}

    }
	
	
	// If input is null, returns string "No Value Returned", else returns input
	protected function null2unknown($data) {
		if ($data == "") {
			return "No Value Returned";
		} else {
			return $data;
		}
	} 
}