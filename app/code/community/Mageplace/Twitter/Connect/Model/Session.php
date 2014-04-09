<?php
/**
 * Mageplace Twitter Connector
 *
 * @category	Mageplace_Twitter
 * @package		Mageplace_Twitter_Connect
 * @copyright	Copyright (c) 2011 Mageplace. (http://www.mageplace.com)
 * @license		http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Twitter_Connect_Model_Session extends Mage_Core_Model_Session_Abstract
{
	const TWITTER_MESSAGE_LENGTH     = 140;

    const ACCESS_TOKEN               = 'access_token';
	const OAUTH_TOKEN                = 'oauth_token';
	const OAUTH_TOKEN_SECRET         = 'oauth_token_secret';
	const OAUTH_CALLBACK_CONFIRMED   = 'oauth_callback_confirmed';
	const OAUTH_VERIFIER             = 'oauth_verifier';
	const ACCOUNT_VERIFY_CREDENTIALS = 'account/verify_credentials';
	const ACCOUNT_CREDENTIALS        = 'account_credentials';
	const OAUTH_COOKIE_NAME          = 'twitterconnector';


    public function __construct()
	{
		$namespace = 'mageplace_twitter_connect_' . (Mage::app()->getStore()->getWebsite()->getCode());
		$this->init($namespace);
	}

	public function connect()
	{
	    $this->clearConnection();

	    if(!$this->isInited()) {
            return null;
        }

	    $connection = new TwitterOAuth(
	        $this->getConsumerKey(),
	        $this->getConsumerSecret()
	    );
        try {
            $request_token = $connection->getRequestToken($this->getCallbackUrl());
        } catch(Exception $e) {
            Mage::logException($e);
        }

        $this->addData($request_token);

        if(200 == $connection->http_code) {
            $url = $connection->getAuthorizeURL($this->getOauthToken());
        } else {
            $url = null;
        }

        return $url;
	}

	public function callback()
	{
        $request = Mage::app()->getFrontController()->getRequest();
	    $requestOauthToken = $request->getParam(self::OAUTH_TOKEN);

	    if(!$requestOauthToken || ($this->getOauthToken() !== $requestOauthToken)) {
	        $this->connect();
            return false;
        }

        /* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
        $connection = new TwitterOAuth(
            $this->getConsumerKey(),
            $this->getConsumerSecret(),
            $this->getOauthToken(),
            $this->getOauthTokenSecret()
        );

        /* Request access tokens from twitter */
        $access_token = $connection->getAccessToken($request->getParam(self::OAUTH_VERIFIER));

		/* Save the access tokens. Normally these would be saved in a database for future use. */
        $this->setAccessToken($access_token);

        $this->unsetData(self::OAUTH_TOKEN);
	    $this->unsetData(self::OAUTH_TOKEN_SECRET);

#	    Mage::log(__METHOD__.': CALLBACK'); Mage::log($connection); Mage::log($this);
	    /* If HTTP response is 200 continue otherwise send to connect page to retry */
        if (200 == $connection->http_code) {
            $this->setCookie();
            $this->setTwitterConnection($connection);
            return true;
        } else {
            $this->connect();
            return false;
        }
	}

	public function isInited()
	{
	    return ($this->getConsumerKey() && $this->getConsumerSecret());
	}

	public function isConnected()
	{
	    return ($this->isInited() && $this->getTwitterConnection() && $this->getAccessToken() && $this->getOauthToken() && $this->getOauthTokenSecret());
	}

	public function getOauthToken()
	{
	    $accessToken = $this->getAccessToken();

	    return (empty($accessToken[self::OAUTH_TOKEN]) ? $this->_getData(self::OAUTH_TOKEN) : $accessToken[self::OAUTH_TOKEN]);
	}

	public function getOauthTokenSecret()
	{
	    $accessToken = $this->getAccessToken();

	    return (empty($accessToken[self::OAUTH_TOKEN_SECRET]) ? $this->_getData(self::OAUTH_TOKEN_SECRET) : $accessToken[self::OAUTH_TOKEN_SECRET]);
	}

	public function getAccountCredentials()
	{
	    if($credentials = $this->_getData(self::ACCOUNT_CREDENTIALS)) {
            return $credentials;
	    } else {
	        if($connection = $this->getTwitterConnection()) {
                $credentials = $connection->get(self::ACCOUNT_VERIFY_CREDENTIALS);
                $this->setData(self::ACCOUNT_CREDENTIALS, $credentials);
                return $credentials;
    	    }
	    }

	    return null;
	}

 	public function setCookie($cookie_value=null)
 	{
        if(is_null($cookie_value)) {
     	    $customer = Mage::getModel('customer/customer');
    		/* @var $customer Mage_Customer_Model_Customer */
            $cookie_value = $customer->generatePassword(20);
 	    }
        Mage::getModel('core/cookie')->set(self::OAUTH_COOKIE_NAME, $cookie_value, time()+60*60*24*30*12*10);
        $this->setCookieValue($cookie_value);
 	}

 	public function getCookieValue()
 	{
 	    if($cookie = $this->_getData('cookie_value')) {
            return $cookie;
 	    } else if(array_key_exists(self::OAUTH_COOKIE_NAME, $_COOKIE)) {
            return Mage::getModel('core/cookie')->get(self::OAUTH_COOKIE_NAME);
 	    }
 	}

 	public function getTwitterConnection()
 	{
 	    if($connection = $this->getData('twitter_connection')) {
 	        if(!Mage::getModel('core/cookie')->get(self::OAUTH_COOKIE_NAME)
 	            && ($account_credentials = $connection->get(self::ACCOUNT_VERIFY_CREDENTIALS))
 	            && (isset($account_credentials->id))
 	            && ($twitter_id = $account_credentials->id))
 	        {
 	            $synch = Mage::getResourceModel('twitterconnect/synch_collection')->getSynchByTwitterId($twitter_id);
                if($synch && ($cookie = $synch->getTwitterconnectCookie())) {
                    $this->setCookie($cookie);
                }
 	        }

 	        return $connection;

 	    } else if($connection = $this->getConnectionByCookie()) {
 	        return $connection;
 	    }

 	    return null;
 	}

 	public function getConnectionByCookie()
 	{
#        Mage::log(__METHOD__.': CHECK COOKIE START');

        if(!$cookie_value = $this->getCookieValue()) {
            return null;
        }

        $synch = Mage::getResourceModel('twitterconnect/synch_collection')->getSynchByCookie($cookie_value);
        if(!($synch instanceof Mageplace_Twitter_Connect_Model_Synch) || !($oauthToken=$synch->getOauthToken())
 	        || !($oauthTokenSecret=$synch->getOauthTokenSecret()) || !($twitterId=$synch->getTwitterId()))
 	    {
            Mage::getModel('core/cookie')->delete(self::OAUTH_COOKIE_NAME);
 	        return null;
 	    }


 	    $this->setCustomerId($synch->getCustomerId());

 	    $connection = new TwitterOAuth(
            $this->getConsumerKey(),
            $this->getConsumerSecret(),
            $oauthToken,
            $oauthTokenSecret
        );

        $credentials = $connection->get(self::ACCOUNT_VERIFY_CREDENTIALS);
        if(!$credentials || empty($credentials->id) || ($credentials->id != $twitterId)) {
            return null;
        }

        $this->setData(self::ACCOUNT_CREDENTIALS, $credentials);

        $accessToken = array();
        $accessToken[self::OAUTH_TOKEN] = $oauthToken;
        $accessToken[self::OAUTH_TOKEN_SECRET] = $oauthTokenSecret;
        $this->setAccessToken($accessToken);

        $this->setTwitterConnection($connection);

#        Mage::log(__METHOD__.': CHECK COOKIE END');
        return $connection;
 	}

 	public function twitterPost($order_details)
 	{
        $connection = $this->getTwitterConnection();
        $status = $connection->post(
        	'statuses/update',
            array(
            	'status' => Mage::helper('twitterconnect')->__('I\'ve just purchased %1$d item(s) at %2$s - %3$s.', $order_details->totalCount, $order_details->storeName, $order_details->storeUrl)
            )
        );

#        Mage::log($status);

        foreach($order_details->products as $product) {
            if(!$product['name'] || !$product['url']) {
                continue;
            }

            $message = Mage::helper('twitterconnect')->__('View %1$s for %2$s - %3$s.', $product['name'], strip_tags(@$product['price']), $product['url']);
            if(strlen($message) > self::TWITTER_MESSAGE_LENGTH) {
                $message = Mage::helper('twitterconnect')->__('View %s', $product['url']);
            }

            $status = $connection->post(
            	'statuses/update',
                array(
                	'status' => $message,
                )
            );
#            Mage::log($status);
        }

		return true;
 	}

	public function clearConnection()
	{
        $this->unsetData(self::ACCESS_TOKEN);
        $this->unsetData(self::OAUTH_TOKEN);
	    $this->unsetData(self::OAUTH_TOKEN_SECRET);
	    $this->unsetData(self::OAUTH_CALLBACK_CONFIRMED);
        $this->setTwitterConnection(null);
 	}
}
