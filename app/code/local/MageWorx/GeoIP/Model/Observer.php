<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageworx.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 * or send an email to sales@mageworx.com
 *
 * @category   MageWorx
 * @package    MageWorx_GeoIP
 * @copyright  Copyright (c) 2009 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * GeoIP extension
 *
 * @category   MageWorx
 * @package    MageWorx_GeoIP
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

class MageWorx_GeoIP_Model_Observer
{
	public function currencyAutoswitcher()
	{
		$mageStore      = Mage::app()->getStore();
		$currencyCookie = $this->_getCurrencyCookie();
		if ($mageStore->getCurrentCurrencyCode() != $currencyCookie) {
			$currency = null;

			if ($currencyCookie) {
				$currency = $currencyCookie;
			} else {
				$session  = new Varien_Object(Mage::getSingleton('core/session')->getValidatorData());
				$geoip    = Mage::getSingleton('geoip/geoip')->getGeoIP($session->getRemoteAddr());
				$currency = Mage::helper('geoip')->getCurrency($geoip->getCountry());
			}
			if ($currency && ($mageStore->getCurrentCurrencyCode() != $currency)) {
	            $mageStore->setCurrentCurrencyCode($currency);

	            $this->_setCurrencyCookie($currency);

				if (Mage::getSingleton('checkout/session')->getQuote()) {
		            Mage::getSingleton('checkout/session')->getQuote()
		                ->collectTotals()
		                ->save();
		        }
	        } else {
	        	$this->_setCurrencyCookie($mageStore->getCurrentCurrencyCode());
	        }
		}
	}

	private function _setCurrencyCookie($currency)
	{
		$version = Mage::getVersion();
        $cookie = Mage::getSingleton('core/cookie');
        if (version_compare($version, '1.2.1', '<')) {
            $cookie->set('currency_code', base64_encode($currency));
        } else {
            $cookie->set('currency_code', base64_encode($currency), true, '/', null, null, true);
        }
	}

	private function _getCurrencyCookie()
	{
		$cookie = Mage::getSingleton('core/cookie');
		if ($cookie->get('currency_code')) {
			return base64_decode($cookie->get('currency_code'));
		} else {
			return false;
		}
	}

	public function setCurrency()
	{
		$filter   = new Zend_Filter_StripTags();
		$currency = $filter->filter(Mage::app()->getFrontController()->getRequest()->getParam('currency'));
		$this->_setCurrencyCookie($currency);
	}
}