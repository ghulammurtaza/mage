<?php
class Magestore_Onepay_Block_Redirect extends Mage_Core_Block_Abstract
{
	protected function _toHtml()
    {
        $checkout			= Mage::getSingleton('checkout/session');
		$order 				= Mage::getModel('sales/order')->loadByIncrementId($checkout->getLastRealOrderId());
		$shippingAddress	= $order->getBillingAddress();
		//echo '123'.Mage::app()->getStore()-> getCurrentCurrencyCode(); 
        //$currencyrate = Mage::app()->getStore()->getCurrentCurrencyRate();
        //echo '<br>'.$currencyrate;
        //exit;
		$onepay = Mage::getModel('onepay/onepay');

        $form = new Varien_Data_Form();
		$url = $this->getUrlAction($order, $shippingAddress);
        $form->setAction($url)
            ->setId('onepay_checkout')
            ->setName('onepay_checkout')
            ->setMethod('post')
            ->setUseContainer(true);
		
        // foreach($onepay->getOnepayFormElements($order, $shippingAddress) as $field=>$value) {
		
			// $form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));
			
        // }
		
        // $html = '<html><body>';
        // $html.= $this->__('You will be redirected to Onepay in a few seconds.');
        // $html.= $form->toHtml();
        // $html.= '<script type="text/javascript">document.getElementById("onepay_checkout").submit();</script>';
        // $html.= '</body></html>';

        $html = '<html><body>';
        $html.= $this->__('You will be redirected to the Arab African International Bank secure gateway in a few seconds.');
        $html.= '<script type="text/javascript">window.location="'.$url.'"</script>';
        $html.= '</body></html>';
		
        return $html;
    }
	
	protected function getUrlAction($order, $shippingAddress) {
	
		$onepay = Mage::getModel('onepay/onepay');
		
		// $SECURE_SECRET = "secure-hash-secret";
		//$SECURE_SECRET = "01C86EEE96BEF5BF560F29B885E0EAF5";
        $SECURE_SECRET = "E49780B4C8FDB4E38222ADE7F3B97CCA";
		
		// add the start of the vpcURL querystring parameters
		$vpcURL = $onepay->getOnepayUrlPayment() . "?";
		
		// Create the request to the Virtual Payment Client which is a URL encoded GET
		// request. Since we are looping through all the data we may as well sort it in
		// case we want to create a secure hash and add it to the VPC data if the
		// merchant secret has been provided.
		$md5HashData = $SECURE_SECRET;
		
		// echo $_SERVER['HTTP_REFERER'];echo "<br><br>";
		
		
		$elements = $onepay->getOnepayFormElements($order, $shippingAddress);
		
		// set a parameter to show the first pair in the URL
		$appendAmp = 0;
		
		foreach($elements as $key=>$value) {
			if (strlen($value) > 0) {
				// this ensures the first paramter of the URL is preceded by the '?' char
				if ($appendAmp == 0) {
					$vpcURL .= urlencode($key) . '=' . urlencode($value);
					$appendAmp = 1;
				} else {
					$vpcURL .= '&' . urlencode($key) . "=" . urlencode($value);
				}
				$md5HashData .= $value;
			}
		}

		if (strlen($SECURE_SECRET) > 0) {
			$vpcURL .= "&vpc_SecureHash=" . strtoupper(md5($md5HashData));
		}
		
		return $vpcURL;
	}
}