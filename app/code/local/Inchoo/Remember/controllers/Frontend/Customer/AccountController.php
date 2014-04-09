<?php
require_once Mage::getModuleDir('controllers', 'Mage_Customer').DS.'AccountController.php';

class Inchoo_Remember_Frontend_Customer_AccountController extends Mage_Customer_AccountController
	{
		public function loginPostAction()
		{
			//Do whatever original method does
			parent::loginPostAction();
			//Set or remove cookie depending on checkbox
			$login = $this->getRequest()->getPost('login');
			if ($this->_getSession()->isLoggedIn() && isset($login['remember']))
				{
					//create cookies with user information, and salted password
					$user = $this->_getSession()->getCustomer()->getName();
					//At the moment Created At timestamp could be a good idea to salt the password
					$salt = $this->_getSession()->getCustomer()->getCreatedAtTimestamp();
					$pass = $this->_getSession()->getCustomer()->getPasswordHash();
					$safe_pass = sha1(md5($pass).md5($salt));
					//Set the cookie with prepared data
					setcookie('info',$safe_pass,time()+60*60*24*30,'/');
				}
			else
				{
					//Remove cookie if not checked
					if (isset($_COOKIE['info']))
						setcookie('info',$safe_pass,time()-60*60*24*30,'/');
				}
		}

		public function logoutAction()
		{
			//Remove the cookie if someone clicked logout
			if (isset($_COOKIE['info']))
				setcookie('info','',time()-60*60*24*30,'/');

			//Do whatever original method does
			parent::logoutAction();
		}
	}
