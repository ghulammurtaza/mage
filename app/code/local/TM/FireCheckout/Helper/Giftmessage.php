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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_GiftMessage
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Gift Message helper
 *
 * @category   Mage
 * @package    Mage_GiftMessage
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class TM_FireCheckout_Helper_GiftMessage extends Mage_GiftMessage_Helper_Message
{
    /**
     * Retrive inline giftmessage edit form for specified entity
     *
     * @param string $type
     * @param Varien_Object $entity
     * @param boolean $dontDisplayContainer
     * @return string
     */
    public function getInline($type, Varien_Object $entity, $dontDisplayContainer=false)
    {
        if (!in_array($type, array('onepage_checkout','multishipping_adress')) && !$this->isMessagesAvailable($type, $entity)) {
            return '';
        }

        return Mage::getSingleton('core/layout')->createBlock('giftmessage/message_inline')
            ->setTemplate('firecheckout/giftmessage/inline.phtml') // bugfix with $(item).hide()
            ->setId('giftmessage_form_' . $this->_nextId++)
            ->setDontDisplayContainer($dontDisplayContainer)
            ->setEntity($entity)
            ->setType($type)->toHtml();
    }
}
