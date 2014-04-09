<?php

class Inchoo_Remember_Model_Observer
	{
		public function checkRememberMe($observer)
		{
			$session = $observer->getEvent()->getCustomerSession();
			if(!$session->isLoggedIn() and isset($_COOKIE['info']))
				{
					$collection = Mage::getModel('customer/customer')->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->getCollection()->load();
					if ($collection->getSize() > 0)
						{
						foreach ($collection as $user)
							{
								$user->loadByEmail($user->getEmail());
								$salt = $user->getCreatedAtTimestamp();
								$pass = $user->getPasswordHash();
								$safe_pass = sha1(md5($pass).md5($salt));
								if($safe_pass == $_COOKIE['info'])
									{
									$observer->getEvent()->getCustomerSession()->setCustomerAsLoggedIn($user);
									header("Location: ".Mage::helper('core/url')->getCurrentUrl());
									exit;
									}
							}
						}
				}
			return;
		}
	}