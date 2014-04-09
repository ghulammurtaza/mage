<?php

class Infortis_SuperSlideshow_Model_Source_Fx
{
    public function toOptionArray()
    {
        return array(
			array('value' => 'fade',			'label' => Mage::helper('superslideshow')->__('Fade')),
			array('value' => 'zoom',			'label' => Mage::helper('superslideshow')->__('Zoom')),
			array('value' => 'fadeZoom',		'label' => Mage::helper('superslideshow')->__('Fade Zoom')),
			array('value' => 'curtainX',		'label' => Mage::helper('superslideshow')->__('Curtain X')),
			array('value' => 'curtainY',		'label' => Mage::helper('superslideshow')->__('Curtain Y')),
			array('value' => 'blindX',			'label' => Mage::helper('superslideshow')->__('Blind X')),
			array('value' => 'blindY',			'label' => Mage::helper('superslideshow')->__('Blind Y')),
			array('value' => 'blindZ',			'label' => Mage::helper('superslideshow')->__('Blind Z')),
			array('value' => 'growX',			'label' => Mage::helper('superslideshow')->__('Grow X')),
			array('value' => 'growY',			'label' => Mage::helper('superslideshow')->__('Grow Y')),
			array('value' => 'cover',			'label' => Mage::helper('superslideshow')->__('Cover')),
			array('value' => 'uncover',			'label' => Mage::helper('superslideshow')->__('Uncover')),
            array('value' => 'scrollHorz',		'label' => Mage::helper('superslideshow')->__('Scroll Horizontal')),
            array('value' => 'scrollVert',		'label' => Mage::helper('superslideshow')->__('Scroll Vertical')),
			array('value' => 'scrollUp',		'label' => Mage::helper('superslideshow')->__('Scroll Up')),
			array('value' => 'scrollDown',		'label' => Mage::helper('superslideshow')->__('Scroll Down')),
			array('value' => 'scrollLeft',		'label' => Mage::helper('superslideshow')->__('Scroll Left')),
			array('value' => 'scrollRight',		'label' => Mage::helper('superslideshow')->__('Scroll Right')),
			array('value' => 'slideX',			'label' => Mage::helper('superslideshow')->__('Slide X')),
			array('value' => 'slideY',			'label' => Mage::helper('superslideshow')->__('Slide Y')),
			array('value' => 'turnUp',			'label' => Mage::helper('superslideshow')->__('Turn Up')),
			array('value' => 'turnDown',		'label' => Mage::helper('superslideshow')->__('Turn Down')),
			array('value' => 'turnLeft',		'label' => Mage::helper('superslideshow')->__('Turn Left')),
			array('value' => 'turnRight',		'label' => Mage::helper('superslideshow')->__('Turn Right')),
			array('value' => 'wipe',			'label' => Mage::helper('superslideshow')->__('Wipe')),
			array('value' => 'toss',			'label' => Mage::helper('superslideshow')->__('Toss')),
			array('value' => 'shuffle',			'label' => Mage::helper('superslideshow')->__('Shuffle'))
        );
    }
}
