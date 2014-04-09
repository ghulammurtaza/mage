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
 * @package    Mage_Cashu
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Cashu Standard Front Controller
 *
 * @category   Mage
 * @package    Mage_Cashu
 * @name       Mage_Cashu_StandardController
 * @author     Magento Core Team <core@magentocommerce.com>
*/

require('Rc43.php');

class Mage_Cashu_StandardController extends Mage_Core_Controller_Front_Action
{
    /**
     * Order instance
     */
    protected $_order;

    /**
     *  Return debug flag
     *
     *  @return  boolean
     */
    public function getDebug ()
    {
        return Mage::getSingleton('cashu/config')->getDebug();
    }

    /**
     *  Get order
     *
     *  @param    none
     *  @return	  Mage_Sales_Model_Order
     */
    public function getOrder ()
    {
        if ($this->_order == null) {
            $session = Mage::getSingleton('checkout/session');
            $this->_order = Mage::getModel('sales/order');
            $this->_order->loadByIncrementId($session->getLastRealOrderId());
        }
        return $this->_order;
    }

    /**
     * When a customer chooses cashu on Checkout/Payment page
     *
     */
    public function redirectAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setCashuStandardQuoteId($session->getQuoteId());
		
        $order = $this->getOrder();

        if (!$order->getId()) {
            $this->norouteAction();
            return;
        }

        $order->addStatusToHistory(
            $order->getStatus(),
            Mage::helper('cashu')->__('Customer was redirected to cashu')
        );
        $order->save();

        $this->getResponse()
            ->setBody($this->getLayout()
                ->createBlock('cashu/standard_redirect')
                ->setOrder($order)
                ->toHtml());

        $session->unsQuoteId();
    }

    /**
     *  Success response from Secure Ebs
     *
     *  @return	  void
     */
    public function  successAction()
    {
    	
     if(isset($_GET['DR'])) {
		 $DR = preg_replace("/\s/","+",$_GET['DR']);	 	 
	 
	     $secret_key = Mage::getSingleton('cashu/config')->getSecretKey(); // Your Secret Key

	 	 $rc4 = new Crypt_RC4($secret_key);
 	     $QueryString = base64_decode($DR);
	     $rc4->decrypt($QueryString);
	     $QueryString = split('&',$QueryString);

	 $response = array();
	 
 
	 foreach($QueryString as $param){
	 	$param = split('=',$param);
		$response[$param[0]] = urldecode($param[1]);
	 }
	}
       if($response['ResponseCode']== 0)
       {
       	$session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getCashuStandardQuoteId());
        $session->unsCashuStandardQuoteId();
		
        $order = $this->getOrder();
		
        if (!$order->getId()) {
            $this->norouteAction();
            return;
        }

        $order->addStatusToHistory(
            $order->getStatus(),
            Mage::helper('cashu')->__('Customer successfully returned from cashu')
        );
        
	    //$order->save();
		
		//$order->sendNewOrderEmail();
        //$this->_redirect('checkout/onepage/success');
       }
       else
       {
       	$this->_redirect('cashu/standard/failure');
       }
    }

   
    /**
     *  Notification Action from Secure Ebs
     *
     *  @param    none
     *  @return	  void
     */
    public function notifyAction ()
    {
        $postData = $this->getRequest()->getPost();

        if (!count($postData)) {
            $this->norouteAction();
            return;
        }

        if ($this->getDebug()) {
            $debug = Mage::getModel('cashu/api_debug');
            if (isset($postData['cs2']) && $postData['cs2'] > 0) {
                $debug->setId($postData['cs2']);
            }
            $debug->setResponseBody(print_r($postData,1))->save();
        }

        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId(Mage::helper('core')->decrypt($postData['cs1']));
        if ($order->getId()) {
            $result = $order->getPayment()->getMethodInstance()->setOrder($order)->validateResponse($postData);

            if ($result instanceof Exception) {
                if ($order->getId()) {
                    $order->addStatusToHistory(
                        $order->getStatus(),
                        $result->getMessage()
                    );
                    $order->cancel();
                }
                Mage::throwException($result->getMessage());
                return;
            }

            $order->sendNewOrderEmail();

            $order->getPayment()->getMethodInstance()->setTransactionId($postData['transaction_id']);

            if ($this->saveInvoice($order)) {
                $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
            }
            $order->save();
        }
    }

    /**
     *  Save invoice for order
     *
     *  @param    Mage_Sales_Model_Order $order
     *  @return	  boolean Can save invoice or not
     */
    protected function saveInvoice (Mage_Sales_Model_Order $order)
    {
        if ($order->canInvoice()) {
            $invoice = $order->prepareInvoice();
            $invoice->register()->capture();
            Mage::getModel('core/resource_transaction')
               ->addObject($invoice)
               ->addObject($invoice->getOrder())
               ->save();
            return true;
        }

        return false;
    }

    /**
     *  Failure response from cashu
     *
     *  @return	  void
     */
    public function failureAction ()
    {
        $errorMsg = Mage::helper('cashu')->__('There was an error occurred during paying process.');

        $order = $this->getOrder();

        if (!$order->getId()) {
            $this->norouteAction();
            return;
        }

        if ($order instanceof Mage_Sales_Model_Order && $order->getId()) {
            $order->addStatusToHistory($order->getStatus(), $errorMsg);
            $order->cancel();
            $order->save();
        }

        $this->loadLayout();
        $this->renderLayout();

        Mage::getSingleton('checkout/session')->unsLastRealOrderId();
    }

}

