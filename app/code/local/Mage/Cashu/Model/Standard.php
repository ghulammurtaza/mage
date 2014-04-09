<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Secureebs
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Secureebs Standard Model
 *
 * @category   Mage
 * @package    Mage_Secureebs
 * @name       Mage_Secureebs_Model_Standard
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Cashu_Model_Standard extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'cashu_standard';
    protected $_formBlockType = 'cashu/standard_form';

    protected $_isGateway               = false;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;

    protected $_order = null;


    /**
     * Get Config model
     *
     * @return object Mage_cashu_Model_Config
     */
    public function getConfig()
    {
        return Mage::getSingleton('cashu/config');
    }

    /**
     * Payment validation
     *
     * @param   none
     * @return  Mage_cashu_Model_Standard
     */
    public function validate()
    {
        parent::validate();
        $paymentInfo = $this->getInfoInstance();
        if ($paymentInfo instanceof Mage_Sales_Model_Order_Payment) {
            $currency_code = $paymentInfo->getOrder()->getBaseCurrencyCode();
        } else {
            $currency_code = $paymentInfo->getQuote()->getBaseCurrencyCode();
        }
       
        return $this;
    }

    /**
     * Capture payment
     *
     * @param   Varien_Object $orderPayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function capture (Varien_Object $payment, $amount)
    {
        $payment->setStatus(self::STATUS_APPROVED)
            ->setLastTransId($this->getTransactionId());

        return $this;
    }

    /**
     *  Returns Target URL
     *
     *  @return	  string Target URL
     */
    public function getCashuUrl ()
    {
        return 'https://www.cashu.com/cgi-bin/pcashu.cgi';
    }

    /**
     *  Return URL for cashu success response
     *
     *  @return	  string URL
     */
    protected function getSuccessURL ()
    {
        return Mage::getUrl('cashu/standard/success', array('_secure' => true));
    }

    /**
     *  Return URL for cashu notification
     *
     *  @return	  string Notification URL
     */
    protected function getNotificationURL ()
    {
        return Mage::getUrl('cashu/standard/notify', array('_secure' => true));
    }

    /**
     *  Return URL for cashu failure response
     *
     *  @return	  string URL
     */
    protected function getFailureURL ()
    {
        return Mage::getUrl('cashu/standard/failure', array('_secure' => true));
    }

    /**
     *  Form block description
     *
     *  @return	 object
     */
    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('cashu/form_standard', $name);
        $block->setMethod($this->_code);
        $block->setPayment($this->getPayment());

        return $block;
    }

    /**
     *  Return Order Place Redirect URL
     *
     *  @return	  string Order Redirect URL
     */
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('cashu/standard/redirect');
    }

    /**
     *  Return Standard Checkout Form Fields for request to cashu
     *
     *  @return	  array Array of hidden form fields
     */
    public function getStandardCheckoutFormFields ()
    {
        $order = $this->getOrder();
        if (!($order instanceof Mage_Sales_Model_Order)) {
            Mage::throwException($this->_getHelper()->__('Cannot retrieve order object'));
        }

        $billingAddress = $order->getBillingAddress();

        $streets = $billingAddress->getStreet();
        $street = isset($streets[0]) && $streets[0] != ''
                  ? $streets[0]
                  : (isset($streets[1]) && $streets[1] != '' ? $streets[1] : '');

        if ($this->getConfig()->getDescription()) {
            $transDescription = $this->getConfig()->getDescription();
        } else {
            $transDescription = Mage::helper('cashu')->__('Order #%s', $order->getRealOrderId());
        }

        if ($order->getCustomerEmail()) {
            $email = $order->getCustomerEmail();
        } elseif ($billingAddress->getEmail()) {
            $email = $billingAddress->getEmail();
        } else {
            $email = '';
        }

        $currency = Mage::app()->getStore()-> getCurrentCurrencyCode();
        if($currency != 'EGP'){
            $currencyrate = Mage::app()->getStore()->getCurrentCurrencyRate();
            $amount = round($order->getBaseGrandTotal()*(1/$currencyrate),2);
        }else{
            $amount = round($order->getBaseGrandTotal(),2);
        }
        
		// fields to send to cashu
        $fields = array(
						'merchant_id'	   => Mage::getSingleton('cashu/config')->getMerchantId(),
						//'currency'		   => Mage::app()->getStore()-> getCurrentCurrencyCode(), 
                        'currency'           => 'EGP', 
					//	'account_id'       => Mage::getSingleton('cashu/config')->getAccountId(),
                        'product_name'     => $transDescription,
                        'amount'    	   => $amount,
                        'language'         => $this->getConfig()->getLanguage(),
                        'f_name'           => $billingAddress->getFirstname(),
                        's_name'           => $billingAddress->getLastname(),
                        'street'           => $street,
                        'city'             => $billingAddress->getCity(),
                        'state'            => $billingAddress->getRegionModel()->getCode(),
                        'decline_url'      => $this->getFailureURL(),
                      	);

        if ($this->getConfig()->getDebug()) {
            $debug = Mage::getModel('cashu/api_debug')
                ->setRequestBody($this->getCashuUrl()."\n".print_r($fields,1))
                ->save();
            $fields['cs2'] = $debug->getId();
        }

        return $fields;
    }

}