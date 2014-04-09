<?php

class Infortis_SuperSlideshow_Model_Source_Easing
{
    public function toOptionArray()
    {
        return array(
			//Ease in-out
			array('value' => 'easeInOutSine',	'label' => Mage::helper('superslideshow')->__('easeInOutSine')),
			array('value' => 'easeInOutQuad',	'label' => Mage::helper('superslideshow')->__('easeInOutQuad')),
			array('value' => 'easeInOutCubic',	'label' => Mage::helper('superslideshow')->__('easeInOutCubic')),
			array('value' => 'easeInOutQuart',	'label' => Mage::helper('superslideshow')->__('easeInOutQuart')),
			array('value' => 'easeInOutQuint',	'label' => Mage::helper('superslideshow')->__('easeInOutQuint')),
			array('value' => 'easeInOutExpo',	'label' => Mage::helper('superslideshow')->__('easeInOutExpo')),
			array('value' => 'easeInOutCirc',	'label' => Mage::helper('superslideshow')->__('easeInOutCirc')),
			array('value' => 'easeInOutElastic','label' => Mage::helper('superslideshow')->__('easeInOutElastic')),
			array('value' => 'easeInOutBack',	'label' => Mage::helper('superslideshow')->__('easeInOutBack')),
			array('value' => 'easeInOutBounce',	'label' => Mage::helper('superslideshow')->__('easeInOutBounce')),
			//Ease out
			array('value' => 'easeOutSine',		'label' => Mage::helper('superslideshow')->__('easeOutSine')),
			array('value' => 'easeOutQuad',		'label' => Mage::helper('superslideshow')->__('easeOutQuad')),
			array('value' => 'easeOutCubic',	'label' => Mage::helper('superslideshow')->__('easeOutCubic')),
			array('value' => 'easeOutQuart',	'label' => Mage::helper('superslideshow')->__('easeOutQuart')),
			array('value' => 'easeOutQuint',	'label' => Mage::helper('superslideshow')->__('easeOutQuint')),
			array('value' => 'easeOutExpo',		'label' => Mage::helper('superslideshow')->__('easeOutExpo')),
			array('value' => 'easeOutCirc',		'label' => Mage::helper('superslideshow')->__('easeOutCirc')),
			array('value' => 'easeOutElastic',	'label' => Mage::helper('superslideshow')->__('easeOutElastic')),
			array('value' => 'easeOutBack',		'label' => Mage::helper('superslideshow')->__('easeOutBack')),
			array('value' => 'easeOutBounce',	'label' => Mage::helper('superslideshow')->__('easeOutBounce')),
			//Ease in
			array('value' => 'easeInSine',		'label' => Mage::helper('superslideshow')->__('easeInSine')),
			array('value' => 'easeInQuad',		'label' => Mage::helper('superslideshow')->__('easeInQuad')),
			array('value' => 'easeInCubic',		'label' => Mage::helper('superslideshow')->__('easeInCubic')),
			array('value' => 'easeInQuart',		'label' => Mage::helper('superslideshow')->__('easeInQuart')),
			array('value' => 'easeInQuint',		'label' => Mage::helper('superslideshow')->__('easeInQuint')),
			array('value' => 'easeInExpo',		'label' => Mage::helper('superslideshow')->__('easeInExpo')),
			array('value' => 'easeInCirc',		'label' => Mage::helper('superslideshow')->__('easeInCirc')),
			array('value' => 'easeInElastic',	'label' => Mage::helper('superslideshow')->__('easeInElastic')),
			array('value' => 'easeInBack',		'label' => Mage::helper('superslideshow')->__('easeInBack')),
			array('value' => 'easeInBounce',	'label' => Mage::helper('superslideshow')->__('easeInBounce')),
			//No easing
			array('value' => 'null',			'label' => Mage::helper('superslideshow')->__('Disabled'))
        );
    }
}
