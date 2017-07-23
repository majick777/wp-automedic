<?php

	// ========================================
	// AutoMedic Reloader and Cycler Javascript
	// ========================================

	if (!function_exists('add_action')) {exit;}

	// Script Header
	// -------------

	header("Content-type: text/javascript; charset: UTF-8");


	// Get Options
	// -----------
	global $vautomedic;

	$vcontext = $_GET['context'];
	if ( ($vcontext != 'admin') && ($vcontext != 'frontend') ) {$vcontext = 'frontend';}

	$vimages = automedic_get_option('images');
	$vstyles = automedic_get_option('styles');
	$vscripts = automedic_get_option('scripts');
	$viframes = automedic_get_option('iframes');
	// $vembeds = automedic_get_option('embeds');


	// Start Javascript
	// ----------------

	// note to javascript gurus: help is very welcome! I have mostly used javascript
	// for user interfaces, not for heavy lifting like this, that said, it works. ;-)
	// but need as many feedbacks, failbacks and fallbacks as we can get, lol.

	global $vautomedicversion;

	echo "/* WP AutoMedic ".$vautomedicversion." */".PHP_EOL;
	echo "/* http://pluginreview.net/wordpress-plugins/wp-automedic/ */".PHP_EOL.PHP_EOL;

	// TODO: use a Javascript Namespace?

	// Output Config Variables
	// -----------------------
	if ($vimages['reload'] == 'both') {$vdoimagereload = '1';}
	if ( ($vcontext == 'admin') && ($vimages['reload'] == 'admin') ) {$vdoimagereload = '1';}
	if ( ($vcontext == 'frontend') && ($vimages['reload'] == 'frontend') ) {$vdoimagereload = '1';}

	if ( ($vimages['cycle'] != '') && ($vimages['cycle'] != '0')
	  && ($vimages['attempts'] != '') && ($vimages['attempts'] != '0') ) {$vdoimagecycle = 'yes';}
														  else {$vdoimagecycle = 'no';}

	if ($vdoimagereload == '1') {
		echo "	var amDoImageCycle = '".$vdoimagecycle."';".PHP_EOL;
		echo "	var amImageCycle = '".$vimages['cycle']."';".PHP_EOL;
		echo "	var amImageDelay = '".$vimages['delay']."';".PHP_EOL;
		echo "	var amImageAttempts = '".$vimages['attempts']."';".PHP_EOL;
		echo "  var amImageExternal = '".$vimages['external']."';".PHP_EOL;
		echo "  var amImageCache = '".$vimages['cache']."';".PHP_EOL;
	}

	if ($vstyles['reload'] == 'both') {$vdostylereload = '1';}
	if ( ($vcontext == 'admin') && ($vstyles['reload'] == 'admin') ) {$vdostylereload = '1';}
	if ( ($vcontext == 'frontend') && ($vstyles['reload'] == 'frontend') ) {$vdostylereload = '1';}

	if ( ($vstyles['cycle'] != '') && ($vstyles['cycle'] != '0')
	  && ($vstyles['attempts'] != '') && ($vstyles['attempts'] != '0') ) {$vdostylecycle = 'yes';}

	if ($vdostylereload == '1') {
		echo "	var amDoStyleCycle = '".$vdostylecycle."';".PHP_EOL;
		echo "	var amStyleCycle = '".$vstyles['cycle']."';".PHP_EOL;
		echo "	var amStyleDelay = '".$vstyles['delay']."';".PHP_EOL;
		echo "	var amStyleAttempts = '".$vstyles['attempts']."';".PHP_EOL;
		echo "  var amStyleExternal = '".$vstyles['external']."';".PHP_EOL;
		echo "  var amStyleCache = '".$vstyles['cache']."';".PHP_EOL;
	}

	if ($vscripts['reload'] == 'both') {$vdoscriptreload = '1';}
	if ( ($vcontext == 'admin') && ($vscripts['reload'] == 'admin') ) {$vdoscriptreload = '1';}
	if ( ($vcontext == 'frontend') && ($vscripts['reload'] == 'frontend') ) {$vdoscriptreload = '1';}

	if ( ($vscripts['cycle'] != '') && ($vscripts['cycle'] != '0')
	  && ($vscripts['attempts'] != '') && ($vscripts['attempts'] != '0') ) {$vdoscriptcycle = 'yes';}

	if ($vdoscriptreload == '1') {
		echo "	var amDoScriptCycle = '".$vdoscriptcycle."';".PHP_EOL;
		echo "	var amScriptCycle = '".$vscripts['cycle']."';".PHP_EOL;
		echo "	var amScriptDelay = '".$vscripts['delay']."';".PHP_EOL;
		echo "	var amScriptAttempts = '".$vscripts['attempts']."';".PHP_EOL;
		echo "  var amScriptExternal = '".$vscripts['external']."';".PHP_EOL;
		echo "  var amScriptCache = '".$vscripts['cache']."';".PHP_EOL;
	}

	if ($viframes['reload'] == 'both') {$vdoiframereload = '1';}
	if ( ($vcontext == 'admin') && ($viframes['reload'] == 'admin') ) {$vdoiframereload = '1';}
	if ( ($vcontext == 'frontend') && ($viframes['reload'] == 'frontend') ) {$vdoiframereload = '1';}

	if ( ($viframes['cycle'] != '') && ($viframes['cycle'] != '0')
	  && ($viframes['attempts'] != '') && ($viframes['attempts'] != '0') ) {$vdoiframecycle = 'yes';}

	if ($vdoiframereload == '1') {
		echo "	var amDoIframeCycle = '".$vdoiframecycle."';".PHP_EOL;
		echo "	var amIframeCycle = '".$viframes['cycle']."';".PHP_EOL;
		echo "	var amIframeDelay = '".$viframes['delay']."';".PHP_EOL;
		echo "	var amIframeAttempts = '".$viframes['attempts']."';".PHP_EOL;
	}

	// TODO: set embed variables?

	// Set Console Logging Debug Switches
	// ----------------------------------
	// allows for overrides passed from loading page
	echo "	";
	if (isset($_GET['imagedebug'])) {if ($_GET['imagedebug'] == '1') {$vimages['debug'] = '1';} }
	if ($vimages['debug'] == '1') {echo "var amImageDebug = true; ";} else {echo "var amImageDebug = false; ";}
	if (isset($_GET['styledebug'])) {if ($_GET['styledebug'] == '1') {$vstyles['debug'] = '1';} }
	if ($vstyles['debug'] == '1') {echo "var amStyleDebug = true; ";} else {echo "var amStyleDebug = false; ";}
	if (isset($_GET['scriptdebug'])) {if ($_GET['scriptdebug'] == '1') {$vscripts['debug'] = '1';} }
	if ($vscripts['debug'] == '1') {echo "var amScriptDebug = true; ";} else {echo "var amScriptDebug = false; ";}
	if (isset($_GET['iframedebug'])) {if ($_GET['iframedebug'] == '1') {$viframes['debug'] = '1';} }
	if ($viframes['debug'] == '1') {echo "var amIframeDebug = true; ";} else {echo "var amIframeDebug = false; ";}
	// if (isset($_GET['embeddebug'])) {if ($_GET['embeddebug'] == '1') {$vembeds['debug'] = '1';} }
	// if ($vembeds['debug'] == '1') {echo "var amEmbedDebug = true; ";} else {echo "var amEmbedDebug = false; ";}

	// Set Site URL for external checks
	// --------------------------------
	$vsitehost = $_SERVER['HTTP_HOST'];
	// $vsiteurl = site_url();
	// $vsiteurl = str_ireplace('http://','',$vsiteurl);
	// $vsiteurl = str_ireplace('https://','',$vsiteurl);
	echo PHP_EOL."	var amSiteHost = '".$vsitehost."';".PHP_EOL;

	// Set Admin AJAX URL
	// ------------------
	$vadminajaxurl = admin_url('admin-ajax.php');
	echo PHP_EOL."  var adminajaxurl = '".$vadminajaxurl."';".PHP_EOL;

	// External Resource Check
	// -----------------------
	echo " /* External Resource Checker */
	function amIsExternal(url) {
		if (url.indexOf(amSiteHost) > -1) {return false;}
		if (url.indexOf('http') == 0) {return true;}
		if (url.indexOf('HTTP') == 0) {return true;}
		return false;
	}".PHP_EOL.PHP_EOL;

	// Cache Buster Function
	// ---------------------
	// without this the browser may just load the cached version of the
	// resource, which we do not want as it may be empty or failed to load
	// 1.4.0: fix to indexOf testing syntax
	echo " /* Cache Buster Function */
	function amCacheBust(resourceurl) {
		var thedate = new Date(); var thetime = thedate.getTime();
		if (typeof resourceurl == 'undefined') {return 'javascript:void(0);';}
		if (resourceurl.indexOf('rldtime=') > -1) {
			if (resourceurl.indexOf('?rldtime=') > -1) {
				urlparts = resourceurl.split('?rldtime=');
				newurl = urlparts[0]+'?rldtime='+thetime;
				/* if (typeof urlparts[1] != 'undefined') {newurl += urlparts[1];} */
				return newurl;
			}
			if (resourceurl.indexOf('&rldtime=') > -1) {
				urlparts = resourceurl.split('&rldtime=');
				newurl = urlparts[0]+'&rldtime='+thetime;
				/* if (typeof urlparts[1] != 'undefined') {newurl += urlparts[1];} */
				return newurl;
			}
		}
		else {
			if (resourceurl.indexOf('?') > -1) {
				newurl = resourceurl+'&rldtime='+thetime; return newurl;
			} else {newurl = resourceurl+'?rldtime='+thetime; return newurl;}
		}
	}";

	echo PHP_EOL.PHP_EOL;

	// Note: "everything loaded" function
	// possible useful but unknown just how reliable it would be
	// cross-browser as it relies on document.readyState...
	// Ref: http://callmenick.com/post/check-if-everything-loaded-with-javascript
	// var everythingLoaded = setInterval(function() {
  	//  if (/loaded|complete/.test(document.readyState)) {
    //   clearInterval(everythingLoaded);
    //   finalinit(); // this is the function that gets called when everything is loaded
	//  }
	// }, 10);

	// jQuery document ready version
	// echo PHP_EOL.PHP_EOL;
	// echo "	jQuery(document).ready(function($) {".PHP_EOL.PHP_EOL;

	// Javascript only version... using docready.js
	// Ref: https://github.com/jfriend00/docReady
	// note: changed docReady to documentReady in docready.js to prevent conflicts
	$vdocready = file_get_contents(dirname(__FILE__).'/docready.js');
	echo $vdocready;

	echo PHP_EOL.PHP_EOL;
	echo "
	window.documentReady(wpAutoMedic);

	function wpAutoMedic() {";

	// Image Reloader
	// --------------
	// Ref: http://stackoverflow.com/questions/8968576/how-to-detect-image-load-failure-and-if-fail-attempt-reload-until-success
	// Ref: http://stackoverflow.com/questions/92720/jquery-javascript-to-replace-broken-images

	// TODO: add an option to reload external images or not
	// TODO: activate image fallback cache one it is secure

	// Test Notes:
	// all tested fine as naturalWidth == 0 for broken images
	// only Internet Explorer comes back complete as false
	// nothing comes back readystate as uninitialized?
	// but some older browsers might so may as well keep it

	if ($vdoimagereload == '1') {

		echo "
		/* Image Reloader */

		var imagetries = new Array(); var theimage = new Array();  var theimagesrc =  new Array();
		var imagereload = new Array(); var imagereloaddelay = new Array(); var imgnum = 0;
		var amCachedImages = new Array();

		/* Image Reload Functions */

		function amTestImageLoad(i) {
			if ( (!i.complete) || (typeof i.naturalWidth == 'undefined')
			  || (i.naturalWidth == 0) || (i.readystate == 'uninitialized') )
			{return false;} else {return true;}
		}

		function amLoadCachedImage(imgnum) {
			/* TODO: maybe implement when image cache is secure */
			/* check for and test a cached local copy */
			/* 	cachedimage = amCachedImages[imgnum];
			 	testcachedimage = amTestImageLoad(cachedimage);} */
			/* if found replace the original image src */
			/* if (testcachedimage) {
				theimage[imgnum].src = 'javascript:void(0);';
				theimage[imgnum].src = cachedimage.src; return true;
			   } */
			return false;
		}

		function amDoImageReload(imgnum) {
			thisimage = theimage[imgnum];
			testimage = amTestImageLoad(thisimage);
			if (!testimage) {
			  	if (amImageDebug) {console.log('Image '+imgnum+' Reloading');}
				imagesrc = thisimage.src;
				/* external image check */
				externalimage = amIsExternal(imagesrc);
				if (externalimage) {
					if (amImageCache) {
						loadcached = amLoadCachedImage(imgnum);
						if (loadcached) {return;}
					}
				}
				/* attempt a simple image reload */
				theimage[imgnum].src = 'javascript:void(0);';
				theimage[imgnum].src = amCacheBust(theimagesrc[imgnum]);
				console.log('Reloaded Image '+imgnum+': '+theimage[imgnum].src);
				/* start the reload cycle */
				/* if (amDoImageCycle == 'yes') {amImageReloadCycle(imgnum);} */
			}
		}

		function amImageReloadCycle(imgnum) {
			if (amImageDebug) {console.log('Start Image '+imgnum+' Reload Cycle');}
			var amDoImageReloadCycle = function(imgnum) {
				imagereload[imgnum] = setInterval(function() {
					thisimage = theimage[imgnum];
					testimage = amTestImageLoad(thisimage);
					if (!testimage) {
						if (amImageDebug) {console.log('Image '+imgnum+' Reload Cycling');}
						/* external image check */
						externalimage = amIsExternal(imagesrc);

						/* check image cache */
						if (externalimage) {
							if (amImageCache) {
								/* try to load a cached image */
								loadcached = amLoadCachedImage(imgnum);
								if (loadcached) {
									clearInterval(imagereload[imgnum]);
									if (amImageDebug) {console.log('Image '+imgnum+' Reload Cycle Cleared');}
									return;
								}
							}
						}

						/* check attempts */
						if (imagetries[imgnum] > amImageAttempts) {
							clearInterval(imagereload[imgnum]);
							if (amImageDebug) {console.log('Image '+imgnum+' Reload Cycle Finished');}
							return;
						}

						/* attempt a reload */
						theimage[imgnum].src = 'javascript:void(0);';
						theimage[imgnum].src = amCacheBust(theimagesrc[imgnum]);
						console.log('Reloaded Image '+imgnum+': '+theimage[imgnum].src);
						imagetries[imgnum] = imagetries[imgnum] + 1;
						return;
					}
					/* clear the reload cycle */
					clearInterval(imagereload[imgnum]);
					if (amImageDebug) {console.log('Image '+imgnum+' Reload Cycle Cleared');}

			 	}, (amImageCycle * 1000) );

			 	amDoImageReloadCycle(imgnum);
			}
		}

		/* Loop Images */

";
		/* jquery loop version */
		/* $('img').each(function() { */
		/*  var thisimage = this; */
		/* 	var imagesrc = this.src; */

		/* javascript only version... */

echo "
		function amLoopImages() {

			amAllImages = document.getElementsByTagName('img');

			for (var j = 0; j < (amAllImages.length); j++) {

				var thisimage = amAllImages[j];
				var imagesrc = thisimage.src;

				if ( (imagesrc != 'javascript:void(0);')
				  && (imagesrc != 'javascript:void();') ) {

					testimage = amTestImageLoad(thisimage);

					if (!testimage) {

						if (amImageDebug) {console.log('Image '+imgnum+' Source: '+imagesrc);}

						/* external image check */
						externalimage = false;
						externalimage = amIsExternal(imagesrc);
						if (externalimage) {
							if ( (amImageExternal) && (amImageCache) ) {
								/* an external image, check/cache a local copy */
								var getimage = new Image();
								getimage.src = adminajaxurl+'?action=am_get_image&src'+encodeURIComponent(imagesrc);
								amCachedImages[imgnum] = getimage;
							}
						}

						if ( (!externalimage) || (externalimage && amImageExternal) ) {

							/* note: no need to check amImageExternal in reload cycle */

							/* image broken so store reference */
							imagetries[imgnum] = 0;
							theimage[imgnum] = thisimage;
							theimagesrc[imgnum] = imagesrc;

							/* Set initial delayed check with setTimeout convolution */
							/* imagereloaddelay[imgnum] = setTimeout(function() {amDoImageReload(imgnum);}, (amImageDelay * 1000) ); */
							var imagereloadtimer = function(num) {
								setTimeout(function() {amDoImageReload(num);}, (amImageDelay * 1000) );
							}
							imagereloadtimer(imgnum);
						}
					}
				}

				imgnum++;
			}
		}";

		// });"; // for jquery loop version "

		echo "
		amLoopImages();
		";

		echo PHP_EOL.PHP_EOL;
	}


	// -------------------
	// Stylesheet Reloader
	// -------------------

	// Note: stylesheet.rules check still comes back as undefined in Firefox,
	// but we are checking both .rules and .cssRules anyway so does not matter

	// TODO: add a local stylesheet fallback cache

	// TODO: add an option to reload external stylesheets or not
	// Note: Reloads external stylesheets *once* in even if they did actually load fine
	// the first time. This is because the cssRules stylesheet check comes back as a
	// security violation for cross-domain stylesheets...
	// ...but reloaded version is @imported so it succeeds the cross domain check. :-)
	// Ref: http://www.phpied.com/when-is-a-stylesheet-really-loaded/
	// +(Firefox supported inserted @import style solution also there)

	// Ref: http://stackoverflow.com/questions/4724606/how-to-use-javascript-to-check-and-load-css-if-not-loaded
	// Ref: http://javascript.nwbox.com/CSSReady/cssready.html
	// Ref: http://stackoverflow.com/questions/3211536/accessing-cross-domain-style-sheet-with-cssrules (for XML CORS)
	// Ref: http://stackoverflow.com/questions/28527244/how-to-check-if-a-link-rel-stylesheet-href-file-css-succeeded

	if ($vdostylereload == '1') {

		echo "
		/* Stylesheet Reloader */

		var stylesheettries = new Array(); var stylesheetsrc =  new Array(); var stylelinks = new Array();
		var stylesheetreload = new Array(); var stylesheetreloaddelay = new Array(); var stylenum = 0;

		/* Stylesheet Reload Functions */

		function amReloadStyle(thislink,stylesheet,newurl,oldurl) {

			if (amStyleDebug) {console.log('Reloading Stylesheet...');}

			/* TODO: is there a *truly* reliable way of testing for Firefox? */
			/* so maybe we can do one OR the other of these methods not both */

			/* Most Browsers */
			/* attempt link href reset */
			thislink.href = 'javascript:void(0)';
			thislink.href = newurl;
			// FIXME: using title won't work for alternate stylesheets
			thislink.title = oldurl;
			if (amStyleDebug) {console.log('Reset Stylesheet URL: '+newurl);}

			/* Firefox */
			/* using <style>@import(url)</style> */
			newstyle = document.createElement('style');
			newstyle.textContent = '@import(\"'+newurl+'\");';
			// FIXME: using title won't work with alternate stylesheets
			newstyle.title = oldurl;
			document.getElementsByTagName('head')[0].appendChild(newstyle);
			thisstyle = document.styleSheets[document.styleSheets.length-1];
			thisstyle.title = encodeURIComponent(oldurl);
			if (amStyleDebug) {console.log('Added Style Import: '+newurl);}

			/* Alternative Method */
			/* add a new link element */
			/*
				newstylesheet = document.createElement('link');
				newstylesheet.href = newurl;
				newstylesheet.title = oldurl;
				newstylesheet.rel = 'stylesheet';
				document.getElementsByTagName('head')[0].appendChild(newstylesheet);
				thisstylesheet = document.styleSheets[document.styleSheets.length-1];
				thisstylesheet.title = encodeURIComponent(oldurl);
				if (amStyleDebug) {console.log('Added Link Stylesheet: '+newurl);}
			*/
		}

		function amLoadCachedStyle(stylenum) {
			/* TODO: maybe implement when cache is secure */
			return false;
		}

		/* Initial Delayed Style Check */

		function amStyleReload(stylenum) {
			stylesheet = ''; nocssrules = ''; norules = ''; zerorules = '';
			thislink = stylelinks[stylenum];
			stylesheethref = stylesheetsrc[stylenum];
			if (amStyleDebug) {console.log('Stylesheet '+stylenum+' URL: '+stylesheethref);}

			if (document.styleSheets) {allstylesheets = document.styleSheets;}
			else {allstylesheets = document.sheet;}

			nocssrules = 'yes'; norules = 'yes';
			for (var i = 0, max = allstylesheets.length; i < max; i++) {
				matched = '';
				if (allstylesheets[i].href) {if (allstylesheets[i].href.indexOf(stylesheethref) > -1) {matched = 'yes';} }
				if ( (matched != 'yes') && (allstylesheets[i].title) ) {
					if (allstylesheets[i].title.indexOf(stylesheethref) > -1) {matched = 'yes';} }
				if (matched == 'yes') {
					if (amStyleDebug) {console.log('Link '+stylenum+' Matched Stylesheet '+i);}
					stylesheet = allstylesheets[i]; thisi = i;
					if (stylesheet.cssRules) {if (stylesheet.cssRules.length !== 0) {nocssrules = '';} }
					if (stylesheet.rules) {if (stylesheet.rules.length !== 0) {norules = '';} }
					break;
				}
			}

			if ( (nocssrules == 'yes') && (norules == 'yes') ) {

				if (amStyleDebug) {console.log('Stylesheet '+thisi+' has no rules.');}

				if (amStyleDebug) {console.log('Style '+stylenum+' Reloading: '+stylesheetsrc[stylenum]);}
				newurl = amCacheBust(stylesheetsrc[stylenum]);
				/* reload the stylesheet */
				amReloadStyle(thislink,stylesheet,newurl,stylesheetsrc[stylenum]);
				/* if (amDoStyleCycle == 'yes') {amStyleReloadCycle(stylenum);} */
			}
		}

		/* Style Reload Cycling */

		function amStyleReloadCycle(stylenum) {

			if (amStyleDebug) {console.log('Style '+stylenum+' Reload Cycle: '+stylesheetsrc[stylenum]);}

			var amDoStyleReloadCycle = function(stylenum) {

				stylesheetreload[stylenum] = setInterval(function() {

					stylesheet = ''; nocssrules = ''; norules = ''; zerorules = '';
					stylesheethref = stylesheetsrc[stylenum];
					thislink = stylelinks[stylenum];
					console.log('Stylesheet '+stylenum+' URL: '+stylesheethref);

					/* Check Stylesheets */

					if (document.styleSheets) {allstylesheets = document.styleSheets;}
					else {allstylesheets = document.sheet;}
					/* ? not sure what browsers actually support document.sheet ? */
					/* maybe for old IE/FF? cannot find much reference to it */

					nocssrules = 'yes'; norules = 'yes';
					for (var i = 0, max = allstylesheets.length; i < max; i++) {
						matched = '';
						if (allstylesheets[i].href) {if (allstylesheets[i].href.indexOf(stylesheethref) > -1) {matched = 'yes';} }

						/* TODO: use an attribute other than title to store oldurl */
						/* or else will not work for alternate stylesheets */

						if ( (matched != 'yes') && (allstylesheets[i].title) ) {
							if (allstylesheets[i].title.indexOf(stylesheethref) > -1) {matched = 'yes';} }
						if (matched == 'yes') {
							/* if (amStyleDebug) {console.log('Link '+stylenum+' Matched Stylesheet '+i);} */
							stylesheet = allstylesheets[i]; thisi = i;
							if (stylesheet.cssRules) {if (stylesheet.cssRules.length !== 0) {nocssrules = '';} }
							if (stylesheet.rules) {if (stylesheet.rules.length !== 0) {norules = '';} }
						}
					}

					if ( (nocssrules == 'yes') && (norules == 'yes') ) {

						if (stylesheettries[stylenum] > amStyleAttempts) {
							dostylesheetreload = stylesheetreload[stylenum];
							if (amStyleDebug) {console.log('Stylesheet '+stylenum+' Cycle Finished');}
							clearInterval(dostylesheetreload); return;
						}
						var newurl = amCacheBust(stylesheetsrc[stylenum]);
						/* reload the stylesheet */
						amReloadStyle(thislink,stylesheet,newurl,stylesheetsrc[stylenum]);
						stylesheettries[stylenum] = stylesheettries[stylenum] + 1;

						return;
					}

					clearInterval(stylesheetreload[stylenum]);
					if (amStyleDebug) {console.log('Stylesheet '+stylenum+' Reload Cycle Cleared');}

					/* TODO: ? Maybe Trigger PrefixFree reprocessing ? */

			 }, (amStyleCycle * 1000) );}

			 amDoStyleReloadCycle(stylenum);
		}

		/* Loop Stylesheets */

";
		/* jquery loop version */
		/* $('link').each(function() { */
		/* 	thislink = this; */

		/* javascript only loop version */

echo "
		function amLoopStyles() {

			var amAllLinks = document.getElementsByTagName('link');

			for (var j = 0; j < (amAllLinks.length); j++) {

				thislink = amAllLinks[j];

				if (thislink.rel) {

					/* FIXME: use case insensitive match to catch rare STYLESHEET? */

					if (thislink.rel == 'stylesheet') {

						/* TODO: could handle 'alternate stylesheet' also */
						/* using thislink.rel.indexOf('stylesheet') ? */
						/* but only when oldurl storage bug is fixed */

						if (!thislink.href) {
							/* ? not sure anymore if this does anything ? */
							if (thislink.url) {stylesheethref = thislink.url;}
						}
						else {stylesheethref = thislink.href;}

						if (stylesheethref) {

							stylelinks[stylenum] = thislink;

							if ( (stylesheethref != 'javascript:void(0);')
								&& (stylesheethref != 'javascript:void();') ) {

								stylesheetsrc[stylenum] = stylesheethref;
								if (stylesheethref.indexOf('?rldtime=') > -1) {
									hrefparts = stylesheethref.split('?rldtime=');
									stylesheetsrc[stylenum] = hrefparts[0];
								}

								if (amStyleDebug) {console.log('Stylesheet '+stylenum+' URL: '+stylesheethref);}

								/* loop through all stylesheets and get the matching one */
								if (document.styleSheets) {allstylesheets = document.styleSheets;}
								else {allstylesheets = document.sheet;}

								stylesheet = ''; nocssrules = ''; norules = ''; zerorules = '';
								for (var i = 0; i < (allstylesheets.length); i++) {
									if ( (allstylesheets[i].href == stylesheethref)
									  || (allstylesheets[i].title == stylesheethref) ) {
										/* if (amStyleDebug) {console.log(stylesheethref+'-'+allstylesheets[i].href+'-'+allstylesheets[i].url);} */
										stylesheet = allstylesheets[i]; thisi = i;
										stylesheet[stylenum] = allstylesheets[i];
										break;
									}
								}

								if (amStyleDebug) {console.log('Stylesheet '+stylenum+' matched Stylesheet '+thisi);}

								/* external stylesheet URL check */
								externalstyle = amIsExternal(stylesheethref);

								if (!externalstyle) {
									if (stylesheet.cssRules) {if (stylesheet.cssRules.length === 0) {nocssrules = 'yes';} }
									if (stylesheet.rules) {if (stylesheet.rules.length === 0) {norules = 'yes';} }
									if ( (nocssrules == 'yes') && (norules == 'yes') ) {zerorules = 'yes';}
									if ( (!stylesheet.cssRules) && (!stylesheet.rules) ) {zerorules = 'yes';}
								} else {

									/* do not test for rules to avoid security failure */
									/* external stylesheet, just consider it as failed */
									/* as it will reload from browser cache anyway */
									/* and it can then be checked after first reload */
									/* TEST: a try/catch block here as partial workaround? */

									zerorules = 'yes';

									/* cache a local copy of external stylesheet */
									if ( (amStyleExternal) && (amStyleCache) ) {
										/* an external style, check/cache a local copy */
										/* TODO: check this as may need XML request */
										/* var getstyle = document.createElement('style'); */
										/* getstyle.src = adminajaxurl+'?action=am_get_style&src'+encodeURIComponent(stylesrc); */
										/* cachedstyles[stylenum] = getstyle; */
									}
								}

								if (zerorules == 'yes') {

									if (!externalstyle || (externalstyle && amStyleExternal) ) {

										if (amStyleDebug) {console.log('Link '+stylenum+' (Stylesheet '+thisi+') has no rules yet.');}

										/* Stylesheet rules not loaded */
										stylesheettries[stylenum] = 0;

										/* Set initial delayed check using setTimeout convolution */
										/* stylesheetreloaddelay[stylenum] = setTimeout(amStyleReload(stylenum), (amStyleDelay * 1000) ); */
										var stylesheetreloadtimer = function(num) {
											if (amStyleDebug) {console.log('Stylesheet '+num+' reloading in '+amStyleDelay+' seconds.');}
											setTimeout(function() {amStyleReload(num);}, (amStyleDelay * 1000) );
										}
										stylesheetreloadtimer(stylenum);
									}
								}
							}
						}
					}
				}

				stylenum++;
			}
		}"; // javascript only version
		// });"; // jquery loop version "

		echo "
		amLoopStyles();
		";

		echo PHP_EOL.PHP_EOL;
	}

	// ---------------
	// Script Reloader
	// ---------------
	// Ref: http://stackoverflow.com/questions/9521298/verify-external-script-is-loaded
	// Ref: http://www.ejeliot.com/blog/109
	// Ref: https://remysharp.com/2007/04/12/how-to-detect-when-an-external-library-has-loaded

	// Ref: http://stackoverflow.com/questions/2954790/javascript-get-the-current-executing-script-node


	// TODO: update cycler to check for LOADED/FAILED in amScripts
	// note: ? no onerror or onreadystatechange events for scripts in IE8 ?

	// test script, nothing shows script contents for src :-(
	// if ($vjueryscriptcheck) {
	//	echo "
	//	var $ = jQuery.noConflict();
	//	/* jQuery(document).function($) { */
	//		$('script').each(function() {
	//		 	thisscript = this;
	//	 		console.log('*A*:'+thisscript.src+ ' : '+thisscript.text);
	//	 		console.log('*B*:'+thisscript.innerHTML+ ' : '+thisscript.content);
	//	 		console.log('*C*:'+thisscript.contentDocument+ ' : '+thisscript.length);
	//		});
	//	/* }); */
	//	";
	// }

	// TODO: dev flag off
	if ($vdoscriptreload == '1') {

		echo "

		/* Script Reloader */

		var scriptnum = 0;
		var scripttries = new Array(); var thescript = new Array(); var thescriptsrc =  new Array();
		var scriptreload = new Array(); var scriptreloaddelay = new Array();


		function amCheckScriptLoad(scriptnum) {

			scriptloaded = false;
			thisscript = thescript[scriptnum];

			/* TODO: script event callback checking */
			/* value should be LOADED or ERROR */


			/* temp: until events are checked properly */
			scriptloaded = true;

			return scriptloaded;
		}


		/* Script Reload Functions */

		function amScriptReload(scriptnum) {

			thisscript = thescript[scriptnum];

			/* check script load event callback */
			scriptloaded = amCheckScriptLoad(thisscript,scriptnum);

			if (!scriptloaded) {
				if (amScriptDebug) {console.log('Script '+scriptnum+' Reloading...');}
				scriptsrc = thescriptsrc[scriptnum];
				thescript[scriptnum].src = 'javascript:void(0);';
				thescript[scriptnum].src = amCacheBust(scriptsrc);
				console.log('Reloaded Script: '+thescript[scriptnum].src);
			}
			if (amDoScriptCycle == 'yes') {amScriptReloadCycle(scriptnum);}
		}

		function amLoadCachedScript(scriptnum) {
			/* TODO: maybe implement when cache is secure */
			return false;
		}

		function amScriptReloadCycle(scriptnum) {
			if (amScriptDebug) {console.log('Script '+imgnum+' Reload Cycle');}
			var amDoScriptReloadCycle = function(scriptnum) {
				scriptreload[scriptnum] = setInterval(function() {

				/* check script load event callbacks */
				scriptloaded = amCheckScriptLoad(scriptnum);

				if (!scriptloaded) {

					if (amScriptDebug) {console.log('Script '+scriptnum+' Reload Cycling');}
					if (scripttries[imgnum] > amScriptAttempts) {
						clearInterval(scriptreload[scriptnum]);
						if (amImageDebug) {console.log('Script '+scriptnum+' Reload Cycle Finished');}
						return;
					}

					scriptsrc = thescriptsrc[scriptnum];
					thescript[scriptnum].src = 'javascript:void(0);';
					thescript[scriptnum].src = amCacheBust(scriptsrc);
					console.log('Reloaded Script: '+thescript[scriptnum].src);
					scripttries[scriptnum] = scripttries[scriptnum] + 1;
					return;

				}

				clearInterval(scriptreload[scriptnum]);
				if (amScriptDebug) {console.log('Script '+scriptnum+' Reload Cycle Cleared');}

			 }, (amScriptCycle * 1000) );}

			 amDoScriptReloadCycle(scriptnum);
		}

		/* Loop Scripts */
";

		/* jQuery loop version */
		/* $('script').each(function() { */
		/* 	thisscript = this; */

		/* javascript only version */

echo "
		function amLoopScripts() {

			amAllScripts = document.getElementsByTagName('script');

			for (var i = 0, max = amAllScripts.length; i < max; i++) {

				thisscript = amAllScripts[i];
				scriptsrc = thisscript.src;

				if ( (scriptsrc != 'javascript:void(0);')
					&& (scriptsrc != 'javascript:void();') ) {

					allscripts = document.scripts;
					scriptfound = false;
					for (var i = 0; i < (allscripts.length); i++) {
						if (allscripts[i].src == scriptsrc) {
							thisscript = allscripts[i]; thisi = i;
							/* ? multiple instances with same src ? */
							/* this will only find the first instance */
							scriptfound = true; break;
						}
					}

					/* check script load event callbacks */
					scriptloaded = amCheckScriptLoad(scriptnum);

					if (!scriptloaded) {

						/* external script URL check */
						externalscript = amIsExternal(scriptsrc);
						if (externalscript) {
							if (amScriptExternal && amScriptCache) {
								/* TODO: cache the script */
								/* var getscript = document.createElement('script');
								getscript.src = adminajaxurl+'?action=am_get_script&src'+encodeURIComponent(scriptsrc);
								cachedscripts[scriptnum] = getscript; */
							}
						}

						if ( (!externalscript) || (externalscript && amScriptExternal) ) {

							/* start checking cycle for this script */

							thescript[scriptnum] = thisscript;
							thescriptsrc[scriptnum] = scriptsrc;
							scripttries[scriptnum] = 0;
							console.log('Script '+scriptnum+': '+scriptsrc);

							/* Set initial delayed check with setTimeout convolution */
							/* scriptreloaddelay[scriptnum] = setTimeout(amScriptReload(scriptnum), (amScriptDelay * 1000) ); */
							var scriptreloadtimer = function(num) {
								setTimeout(function() {amScriptReload(num);}, (amScriptDelay * 1000) );
							}
							scriptreloadtimer(stylenum);
						}
					}
				}

				scriptnum++;
			}
		};";
	// });"; // jQuery loop version "

		echo "
		amLoopScripts();
		";

		echo PHP_EOL.PHP_EOL;

	}


	// Iframe Reloader
	// ---------------
	// same domain only

	// Ref: http://stackoverflow.com/questions/9249680/how-to-check-if-iframe-is-loaded-or-it-has-a-content
	// note: contencontentWindow (IE) vs. contentDocument (FF)
	// i = document.getElementById('testiframe');
	// var iframeDoc = i.contentDocument || i.contentWindow.document;
    // if (iframeDoc.readyState  == 'complete' ) {}
	// check the readyState of the element?

 	// older browsers to get content
 	// i.contentWindow.document.body.innerHTML;
	// Ref: https://roneiv.wordpress.com/2008/01/18/get-the-content-of-an-iframe-in-javascript-crossbrowser-solution-for-both-ie-and-firefox/

	// good test summary
	// Ref: http://www.nczonline.net/blog/2009/09/15/iframes-onload-and-documentdomain/

	// good explanation of document.domain effect on this
	// Ref: http://mechanics.flite.com/blog/2013/04/29/javascripts-document-domain-property-and-how-to-detect-when-it-changes/

	// postMessage option
	// http://stackoverflow.com/questions/8917755/how-to-detect-if-an-iframe-is-accessible-without-triggering-an-error

    // jQuery for same domain only
 	// if ($('iframe').contents().find('body').children().length > 0) {} // <


	if ($viframes['reload'] == 'both') {$vdoiframereload = '1';}
	if ( ($vcontext == 'admin') && ($viframes['reload'] == 'admin') ) {$vdoiframereload = '1';}
	if ( ($vcontext == 'frontend') && ($viframes['reload'] == 'frontend') ) {$vdoiframereload = '1';}

	// TODO: update cycler to check for LOADED/FAILED in amIframes
	// note: no onload or onreadystatechange events for iframes in IE8

	// flag off until ready
	if ($vdoiframereload == "NOPE") {

	echo "
		/* Iframe Reloader */

		var iframetries = new Array(); var theiframe = new Array(); var theiframesrc =  new Array();
		var iframereload = new Array(); var iframereloaddelay = new Array(); var iframenum = 0;

		/* Iframe Load Event Checker */
		function amCheckIframeLoad(iframenum) {

			iframeloaded = false;
			thisiframe = theiframe[iframenum];

			/* TODO: iframe event callback checking */
			/* value should be LOADED */
			/* no ERROR callback is fired :-( */
			/* so this only tells us the iframe is loaded */
			/* not whether the contents are loaded */
			/* a browser error dialogue is considered 'loaded' */

			/* temp: until events are checked properly */
			iframeloaded = true;

			return iframeloaded;
		}

		/* Check Iframe Contents */
		/* for same domain only */
		function amCheckIframeContent(iframeelement,iframenum) {

			iframedocument = false;
			try {iframedocument = thisiframe.contentDocument || thisiframe.contentWindow.document;}
			catch (e) {if (amIframeDebug) {console.log('Iframe Check Failed: '+e);}
			if (iframedocument)
				if (amIframeDebug) {console.log('Iframe '+iframenum+' readyState is *'+iframedocument.readyState+'*');}
				/* if (iframedocument.readyState == 'complete') { } */
				if (iframedocument.body) {
					/* some further checking here? */
					bodylength = iframedocument.body.length;
					contentlength = iframedocument.body.innerHTML.length;
					if (amIframeDebug) {console.log('Iframe '+iframenum+' Document Body Found, Length:'+bodylength+', Content Length: '+contentlength);}
					if (iframedocument.body.length > 0) {return true;}
				}
			}
			return false;
		}


		/* Iframe Reload Functions */

		function amDoIframeReload(iframenum) {

			thisiframe = theiframe[iframenum];

			/* check iframe load event callback */
			/* if (amIframeEvents) { */
			iframeloaded = amCheckIframeLoad(iframenum);
			if (!iframeloaded) {
				/* start the check cycler without reloading */
				/* as there is nothing to check right now */
				if (amDoIframeCycle == 'yes') {iframeereloadcycle(iframenum);} */
				return;
			}
			/* } */

			/* external iframe check */
			externaliframe = amIsExternal(iframesrc);
			if (externaliframe) {
				/* can anything be done for external iframes? */
				/* aint got nothin yet */
				if (amDoIframeCycle == 'yes') {iframeereloadcycle(iframenum);}
				return;
			}

			/* check for content if not external iframe */
			iframecontentloaded = false;
			if (!externaliframe) {
				iframecontentloaded = amCheckIframeContent(thisiframe,iframenum);
			}

			if (!externaliframe && !iframecontentloaded) {
				if (amIframeDebug) {console.log('Iframe '+iframenum+' Reloading');}
				iframesrc = theiframesrc[iframenum];
				theiframe[iframenum].src = 'javascript:void(0);';
				theiframe[iframenum].src = amCacheBust(iframesrc);
				console.log('Reloaded Iframe '+iframenum+': '+theiframe[iframenum].src);
				if (amDoIframeCycle == 'yes') {amIframeReloadCycle(iframenum);}
			}
		}

		function amIframeReloadCycle(iframenum) {

			if (amIframeDebug) {console.log('Iframe '+iframenum+' Reload Cycle');}

			var amDoIframeReloadCycle = function(iframenum) {
				iframereload[iframenum] = setInterval(function() {

				  	thisiframe = theiframe[iframenum];

					/* check iframe load event callback */
					/* if (amIframeEvents) { */
					iframeloaded = amCheckIframeLoad(iframenum);
					/* } */

					/* external iframe check */
					externaliframe = amIsExternal(iframesrc);
					if (externaliframe) {
						/* can anything be done for external iframes? */
						/* still nothing here my friend */
						clearInterval(iframereload[iframenum]);
						return;
					}

					/* check for content if not external iframe */
					iframecontentloaded = false;
					if (!externaliframe && iframeloaded) {
						iframecontentloaded = amCheckIframeContent(thisiframe,iframenum);
					}

					/* reload iframe if not external */
					if (!externaliframe && !iframecontentloaded) {
						if (amIframeDebug) {console.log('Iframe '+iframenum+' Reload Cycling');}
						if (iframetries[iframenum] > amIframeAttempts) {
							clearInterval(iframereload[iframenum]);
							if (amIframeDebug) {console.log('Iframe '+iframenum+' Reload Cycle Finished');}
							return;
						}
						theiframe[iframenum].src = 'javascript:void(0);';
						theiframe[iframenum].src = amCacheBust(theiframesrc[iframenum]);
						console.log('Reloaded Image '+iframenum+': '+theiframe[iframenum].src);
						iframetries[iframenum] = iframetries[iframenum] + 1;'
						return;
					}

					clearInterval(iframereload[iframenum]);
					if (amIframeDebug) {console.log('Iframe '+iframenum+' Reload Cycle Cleared');}

			 }, (amIframeCycle * 1000) );}

			 amDoIframeReloadCycle(iframenum);
		}

		/* Loop Iframes */

		function amLoopIframes() {

			var amAllIframes = document.getElementsByTagName('iframe');

			for (var j = 0; j < (amAllIframes.length); j++) {

				var thisiframe = amAllIframes[j];
				var iframesrc = thisiframe.src;

				if ( (iframesrc != 'javascript:void(0);')
				  && (iframesrc != 'javascript:void();') ) {

					/* check iframe load event callback */
					/* if (amIframeEvents) { */
					iframeloaded = amCheckIframeLoad(iframenum);
					/* } */

					/* external iframe check */
					externaliframe = amIsExternal(iframesrc);
					if (externaliframe) {
						/* can anything be done for external iframes? */
						/* Bueller? Bueller? Bueller? ...... Bueller? */

					}

					/* check for content if not external iframe */
					iframecontentloaded = false;
					if (!externaliframe && iframeloaded) {
						iframecontentloaded = amCheckIframeContent(thisiframe,iframenum);
					}

					/* start loop anyway for anything but fully loaded iframes */
					if (externaliframe || !iframecontentloaded) {

						iframetries[iframenum] = 0;
						theiframe[iframenum] = thisiframe;
						theiframesrc[iframenum] = iframesrc;

						if (amIframeDebug) {console.log('Iframe '+iframenum+' Source: '+this.src);}

						/* Set initial delayed check with setTimeout convolution */
						/* iframereloaddelay[iframenum] = setTimeout(function() {amDoIframeReload(iframenum);}, (amIframeDelay * 1000) ); */
						var iframereloadtimer = function(num) {
							setTimeout(function() {amDoIframeReload(num);}, (amIframeDelay * 1000) );
						}
						iframereloadtimer(iframenum);
					}
				}

				iframenum++;
			}
		}";
		// });"; // jquery loop version "

		echo "
		amLoopIframes();
		";

		echo PHP_EOL.PHP_EOL;

	}


	// TODO: Embed Reloader?
	// ---------------------
	// if ($vembeds['reload'] == 'both') {$vdoembedreload = '1';}
	// if ( ($vcontext == 'admin') && ($vembeds['reload'] == 'admin') ) {$vdoembedreload = '1';}
	// if ( ($vcontext == 'frontend') && ($vembeds['reload'] == 'frontend') ) {$vdoembedreload = '1';}

	// if ($vdoembedreload) {

		// TODO: Embed Src Reloader

		// Javascript Code

	//	echo PHP_EOL.PHP_EOL;

	// }


	// javascript end window load function
	echo PHP_EOL."}".PHP_EOL;

	// jQuery end window load function
	// echo PHP_EOL."});".PHP_EOL;

?>