<?php
/**
 * Mageplace Twitter Core
 *
 * @category    Mageplace_Twitter
 * @package     Mageplace_Twitter_Core
 * @copyright   Copyright (c) 2011 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Twitter_Core_Model_Twitter extends Varien_Object
{
	const IFRAME_URL		= 'http://platform.twitter.com/widgets/';
	const TWITTER_URL		= 'http://twitter.com/';
	const WIDGETS_JS_URL	= 'http://platform.twitter.com/widgets.js';

	protected $_twitter	= null;

	public function getTwitterHtml()
	{
		$html = '';
		if (!Mage::registry('mageplace_twitter_html')) {
			ob_start();
			?>
	<script type="text/javascript">
			(function(){
			  var twitterWidgets = document.createElement('script');
			  twitterWidgets.type = 'text/javascript';
			  twitterWidgets.async = true;
			  twitterWidgets.src = "<?php echo self::WIDGETS_JS_URL; ?>";
			  document.getElementsByTagName('head')[0].appendChild(twitterWidgets);
			})();
	</script>
			<?php
			$html = ob_get_clean();
			Mage::register('mageplace_twitter_html', true);
		}

		return $html;
	}
}