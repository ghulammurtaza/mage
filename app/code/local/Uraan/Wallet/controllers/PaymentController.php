<?php
/*
Mygateway Payment Controller
By: Junaid Bhura
www.junaidbhura.com
*/

class Uraan_Wallet_PaymentController extends Mage_Core_Controller_Front_Action {
    // The redirect action is triggered when someone places an order
    public function redirectAction() {
        $this->loadLayout();
        $block = $this->getLayout()->createBlock('Mage_Core_Block_Template','wallet',array('template' => 'wallet/redirect.phtml'));
        $this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
    }

    // The response action is triggered when your gateway sends back a response after processing the customer's payment
    public function responseAction() {
        $this->loadLayout();
        if($this->getRequest()->isGet()) {

            /*
            /* Your gateway's code to make sure the reponse you
            /* just got is from the gatway and not from some weirdo.
            /* This generally has some checksum or other checks,
            /* and is provided by the gateway.
            /* For now, we assume that the gateway's response is valid
            */
            $request = $this->getRequest();
            $parameters = $request->getParams();
            $response = $parameters['response'];
            $orderId = $parameters['merchinvno'];


            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($orderId);
            //echo "<pre>"; print_r($parameters); exit;
            if($response == 0){
                $validated = true;
                // Payment was successful, so update the order's state, send order email and move to the success page
                $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'Gateway has authorized the payment.'); 
                $order->sendNewOrderEmail();
                $order->setEmailSent(true);
                $comment = 'Transaction Successful';
                $orderPayment = $order->getPayment(); 
                $orderPayment->setAdditionalInformation('Transaction ID', $response);
                $orderPayment->setAdditionalInformation('Command', 'Wallet');
                $orderPayment->setAdditionalInformation('Message', $comment);
                $order->save();

                Mage::getSingleton('checkout/session')->unsQuoteId();

            }
            elseif($response == 1){
                $validated = false; 
                $status = 'pending';
                $comment = 'Bank Declined Transaction';
                $notify = 1; 
                $orderPayment = $order->getPayment(); 
                $orderPayment->setAdditionalInformation('Transaction ID', 0);
                $orderPayment->setAdditionalInformation('Command', 'Wallet');
                $orderPayment->setAdditionalInformation('Message', $comment);
                $order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, true, $comment); 
                $order->addStatusToHistory($status, $comment, $notify);
                $order->save();
            }
            elseif($response == 2){
                $validated = false;
                $this->cancelAction(); 
            }
            elseif($response == 3){
                $validated = false;
                $status = 'pending';
                $comment = 'Error Communicating with Bank';
                $notify = 1; 
                $orderPayment = $order->getPayment(); 
                $orderPayment->setAdditionalInformation('Transaction ID', 0);
                $orderPayment->setAdditionalInformation('Command', 'Wallet');
                $orderPayment->setAdditionalInformation('Message', $comment);
                $order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, true, $comment); 
                $order->addStatusToHistory($status, $comment, $notify);
                $order->save();
            }
            elseif($response == 4){
                $validated = false;
                $this->cancelAction();
            }
            elseif($response == 5){
                $validated = false;
                $this->cancelAction();
            }
            elseif($response == 6){
                $validated = false;
                $status = 'pending';
                $comment = 'Unable to be determined';
                $notify = 1; 
                $orderPayment = $order->getPayment(); 
                $orderPayment->setAdditionalInformation('Transaction ID', 0);
                $orderPayment->setAdditionalInformation('Command', 'Wallet');
                $orderPayment->setAdditionalInformation('Message', $comment);
                $order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, true, $comment); 
                $order->addStatusToHistory($status, $comment, $notify);
                $order->save();
            }
            elseif($response == 7){
                $validated = false;
                $status = 'pending';
                $comment = 'Transaction Aborted';
                $notify = 1; 
                $orderPayment = $order->getPayment(); 
                $orderPayment->setAdditionalInformation('Transaction ID', 0);
                $orderPayment->setAdditionalInformation('Command', 'Wallet');
                $orderPayment->setAdditionalInformation('Message', $comment);
                $order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, true, $comment); 
                $order->addStatusToHistory($status, $comment, $notify);
                $order->save();
            }
            //echo $validated." comes post <pre>"; 
            //print_r($parameters);
            //exit;
            if($validated) {
                Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/success', array('_secure'=>true));
                return;
            }
            else {
                // There is a problem in the response we got
                Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array('_secure'=>true));
                return;
            }
        }
        ///$this->renderLayout();
        //Mage_Core_Controller_Varien_Action::_redirect('');
    }

    // The cancel action is triggered when an order is to be cancelled
    public function cancelAction() {
        if (Mage::getSingleton('checkout/session')->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());
            if($order->getId()) {
                // Flag the order as 'cancelled' and save it
                $order->cancel()->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, 'Gateway has declined the payment.')->save();
            }
        }
    }
}