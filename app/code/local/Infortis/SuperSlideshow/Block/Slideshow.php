<?php
class Infortis_SuperSlideshow_Block_Slideshow extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
	public function getImgUrls()
	{
		$helper = Mage::helper('superslideshow');
		
		//Get slides path (relative to 'media'), trim slashes. If path specified: append to 'media' and append slash.
		$slidesUrl = $mediaUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
		$dir = trim($helper->getCfg('general/directory'), "/");
		if ($dir != '')
			$slidesUrl .= $dir . "/";
		
		//Get filenames separated with commas
		$fileNames = explode(",", $helper->getCfg('general/files'));
		$fileUrls = array();
		foreach ($fileNames as $filename)
		{
			if ( ($trimmedFilename = trim($filename)) != '' )
				$fileUrls[] = $slidesUrl . $trimmedFilename;
		}

		return $fileUrls;
	}
	
	public function getStaticBlockIds()
	{
		$helper = Mage::helper('superslideshow');
		
		$blockIdsString = $helper->getCfg('general/blocks');
		$blockIds = explode(",", str_replace(" ", "", $blockIdsString));
		return $blockIds;
	}
	
	public function getSlideshowConfig()
	{
		$helper = Mage::helper('superslideshow');
		
		$cfg = array(
			array('label' => 'fx',			'value' => "'" . $helper->getCfg('general/fx') . "'"),
            array('label' => 'easing',		'value' => "'" . $helper->getCfg('general/easing') . "'"),
			array('label' => 'timeout',		'value' => $helper->getCfg('general/timeout')),
            array('label' => 'speedOut',	'value' => $helper->getCfg('general/speed_out')),
			array('label' => 'speedIn',		'value' => $helper->getCfg('general/speed_in')),
			array('label' => 'sync',		'value' => $helper->getCfg('general/sync')),
			array('label' => 'pause',		'value' => $helper->getCfg('general/pause')),
			array('label' => 'fit',			'value' => $helper->getCfg('general/fit'))
        );
		
		//Set height only if it's greater than 0
		if ($helper->getCfg('general/height') > 0)
			$cfg[] = array('label' => 'height', 'value' => $helper->getCfg('general/height'));
		
		return $cfg;
	}
}