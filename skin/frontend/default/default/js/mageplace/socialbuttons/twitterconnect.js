/**
 * Mageplace Twitter Connector
 *
 * @category	Mageplace_Twitter
 * @package		Mageplace_Twitter_Connect
 * @copyright	Copyright (c) 2011 Mageplace. (http://www.mageplace.com)
 * @license		http://www.mageplace.com/disclaimer.html
 */

var twitterWin = null;
var redirectUrl = '';

function openTwitterWin(elHref) {
	twitterWin = window.open(elHref,'TwitterConnectorPopup','width=700,height=600,left=100,top=100,location=no,status=yes,scrollbars=yes,resizable=yes');
	twitterWin.focus();
	var watchClose = setInterval(function() {
		if (twitterWin && twitterWin.closed) {
			clearTimeout(watchClose);
			if(redirectUrl) {
				window.location.href = redirectUrl;
			} else {
				window.location.reload();
			}
		}
	}, 200);
}

Event.observe(window, 'load', function() {
	$$('span.twitterconnect\-button > a').each(function(el) {
		el.setAttribute('onclick', "openTwitterWin('"+el.href+"')");
		el.onclick = Function("openTwitterWin('"+el.href+"')");
		el.href = 'javascript:void(0);';
	});
});
