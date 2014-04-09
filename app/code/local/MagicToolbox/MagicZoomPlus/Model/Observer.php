<?php

class MagicToolbox_MagicZoomPlus_Model_Observer {

    public function __construct() {

    }

    public function checkForMagic360Product($observer) {
        $helper = Mage::helper('magiczoomplus/settings');
        if($helper->isModuleOutputEnabled()) {
            $event = $observer->getEvent();
            $product = $event->getProduct();
            $id = $product->getId();
            $gallery = $product->getMediaGalleryImages();
            $imagesCount = $gallery->getSize();

            //NOTE: for old Magento ver. 1.3.x
            if(is_null($imagesCount)) {
                $imagesCount = count($gallery->getItems());
            }

            $tool = $helper->loadTool('product');
            if($tool->isEnabled($imagesCount, $id)) {
                Mage::register('magic360ClassName', 'magiczoomplus');
            } else {
                Mage::register('magic360ClassName', false);
            }
        }
    }

    /*public function controller_action_predispatch($observer) {

    }*/

    /*public function beforeLoadLayout($observer) {

    }*/

    public function fixLayoutUpdates($observer) {
        //NOTE: to prevent an override of our templates with other modules
        $layout_update = new Mage_Core_Model_Config_Element('<magiczoomplus_layout_update module="MagicToolbox_MagicZoomPlus"><file>magiczoomplus.xml</file></magiczoomplus_layout_update>');
        Mage::app()->getConfig()->getNode('frontend/layout/updates')->appendChild($layout_update);
    }

    /*public function controller_action_postdispatch($observer) {

    }*/

}

?>