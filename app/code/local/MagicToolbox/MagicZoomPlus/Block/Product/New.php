<?php

class MagicToolbox_MagicZoomPlus_Block_Product_New extends Mage_Catalog_Block_Product_New {

    protected function _toHtml() {

        $_productCollection = $this->getProductCollection();
        if($_productCollection && $_productCollection->getSize()) {
            $magicToolboxHelper = Mage::helper('magiczoomplus/settings');
            $tool = $magicToolboxHelper->loadTool('newproductsblock');
            if(!$tool->params->checkValue('enable-effect', 'No')) {
                $contents = parent::_toHtml();
                require($magicToolboxHelper->getMagicToolboxListTemplateFilename());
                return $contents;
            }
        }
        return parent::_toHtml();

    }

}