
/* AutoMedic Reloader */

/* note to javascript gurus: help is very welcome! I have mostly used javascript
// for user interfaces, not for heavy lifting like this, that said, it works. ;-)
// but need as many feedbacks, failbacks and fallbacks as we can get, lol. */

/* TODO: allow for querystring overrides?
/* TODO: add a local stylesheet fallback cache? */
/* TODO: add an option to reload external images or not? */
/* TODO: activate image fallback cache once it is secure */


/* External Resource Checker */
function amIsExternal(url) {
	if (url.indexOf(amSiteHost) > -1) {return false;}
	if (url.indexOf('http') == 0) {return true;}
	if (url.indexOf('HTTP') == 0) {return true;}
	return false;
}

/* Cache Buster Function */
/* without this the browser may just load the (browser) cached version of the
// resource, which we do not want as it may be empty or failed to load */
function amCacheBust(resourceurl) {
	var thedate = new Date(); var thetime = thedate.getTime();
	if (typeof resourceurl == 'undefined') {return 'javascript:void(0);';}
	if (resourceurl.indexOf('reloadtime=') > -1) {
		if (resourceurl.indexOf('?reloadtime=') > -1) {
			urlparts = resourceurl.split('?reloadtime=');
			newurl = urlparts[0]+'?reloadtime='+thetime;
		}
		if (resourceurl.indexOf('&reloadtime=') > -1) {
			urlparts = resourceurl.split('&reloadtime=');
			newurl = urlparts[0]+'&reloadtime='+thetime;
		}
	} else {
		if (resourceurl.indexOf('?') > -1) {newurl = resourceurl+'&reloadtime='+thetime;}
		else {newurl = resourceurl+'?reloadtime='+thetime;}
	}
	return newurl;
}


/* AutoMedic */
function AutoMedic() {

	/* Image Reloader
	// --------------
	// Ref: http://stackoverflow.com/questions/8968576/how-to-detect-image-load-failure-and-if-fail-attempt-reload-until-success
	// Ref: http://stackoverflow.com/questions/92720/jquery-javascript-to-replace-broken-images

	// Test Notes:
	// all tested fine as naturalWidth == 0 for broken images (only Internet Explorer comes back complete as false)
	// nothing comes back readystate as uninitialized? but some older browsers might so may as well keep it */

	if (amImageReload == '1') {

		var amImageTries = new Array(); var amImages = new Array();  var amImageSrc =  new Array();
		var amImageReloads = new Array(); var amImageReloadDelays = new Array(); var amImageNum = 0;
		var amCachedImages = new Array();

		/* Test an Image Load */
		function amTestImageLoad(i) {
			if ( (!i.complete) || (typeof i.naturalWidth == 'undefined')
			  || (i.naturalWidth == 0) || (i.readystate == 'uninitialized') )
			{return false;} else {return true;}
		}

		/* Load a Cached Image */
		function amLoadCachedImage(amImageNum) {
			/* TODO: maybe implement when image cache is secure */
			/* check for and test a cached local copy */
			/* 	cachedimage = amCachedImages[amImageNum];
			 	testcachedimage = amTestImageLoad(cachedimage);} */
			/* if found replace the original image src */
			/* if (testcachedimage) {
				amImages[amImageNum].src = 'javascript:void(0);';
				amImages[amImageNum].src = cachedimage.src; return true;
			   } */
			return false;
		}

		/* Initial Reload Check */
		function amDoImageReload(amImageNum) {
			thisimage = amImages[amImageNum];
			testimage = amTestImageLoad(thisimage);
			if (!testimage) {
			  	if (amImageDebug) {console.log('Image '+amImageNum+' Reloading');}
				imagesrc = thisimage.src;

				/* external image check */
				externalimage = amIsExternal(imagesrc);
				if (externalimage) {
					if (amImageCache) {
						if (typeof amLoadCachedImage == 'function') {
							loadcached = amLoadCachedImage(amImageNum);
							if (loadcached) {return;}
						}
					}
				}

				/* attempt a simple image reload */
				amImages[amImageNum].src = 'javascript:void(0);';
				amImages[amImageNum].src = amCacheBust(amImageSrc[amImageNum]);
				console.log('Reloaded Image '+amImageNum+': '+amImages[amImageNum].src);

				/* start the reload cycle */
				if (amImageCycling == '1') {amImageReloadCycle(amImageNum);}
			}
		}

		/* Image Reload Cycle */
		function amImageReloadCycle(amImageNum) {
			if (amImageDebug) {console.log('Start Image '+amImageNum+' Reload Cycle');}
			var amDoImageReloadCycle = function(amImageNum) {
				amImageReloads[amImageNum] = setInterval(function() {
					thisimage = amImages[amImageNum];
					testimage = amTestImageLoad(thisimage);
					if (!testimage) {
						if (amImageDebug) {console.log('Image '+amImageNum+' Reload Cycling');}

						/* external image check */
						externalimage = amIsExternal(imagesrc);

						/* check image cache */
						if (externalimage) {
							if (amImageCache) {
								if (typeof amLoadCachedImage == 'function') {
									/* try to load a cached image */
									loadcached = amLoadCachedImage(amImageNum);
									if (loadcached) {
										clearInterval(amImageReloads[amImageNum]);
										if (amImageDebug) {console.log('Image '+amImageNum+' Reload Cycle Cleared');}
										return;
									}
								}
							}
						}

						/* check attempts */
						if (amImageTries[amImageNum] > amImageAttempts) {
							clearInterval(amImageReloads[amImageNum]);
							if (amImageDebug) {console.log('Image '+amImageNum+' Reload Cycle Finished');}
							return;
						}

						/* attempt a reload */
						amImages[amImageNum].src = 'javascript:void(0);';
						amImages[amImageNum].src = amCacheBust(amImageSrc[amImageNum]);
						console.log('Reloaded Image '+amImageNum+': '+amImages[amImageNum].src);
						amImageTries[amImageNum] = amImageTries[amImageNum] + 1;
						return;
					}
					/* clear the reload cycle */
					clearInterval(amImageReloads[amImageNum]);
					if (amImageDebug) {console.log('Image '+amImageNum+' Reload Cycle Cleared');}

			 	}, (amImageCycle * 1000) );

			 	amDoImageReloadCycle(amImageNum);
			}
		}

		/* Loop All Images */
		function amLoopImages() {

			amAllImages = document.getElementsByTagName('img');

			for (var j = 0; j < (amAllImages.length); j++) {

				var thisimage = amAllImages[j];
				var imagesrc = thisimage.src;

				if (imagesrc.indexOf('javascript:void') == -1) {

					testimage = amTestImageLoad(thisimage);

					if (!testimage) {

						if (amImageDebug) {console.log('Image '+amImageNum+' Source: '+imagesrc);}

						/* external image check */
						externalimage = false;
						externalimage = amIsExternal(imagesrc);
						if (externalimage) {
							if ( (amImageExternal) && (amImageCache) ) {
								/* an external image, check/cache a local copy */
								var getimage = new Image();
								getimage.src = amAjaxUrl+'?action=am_get_image&src='+encodeURIComponent(imagesrc);
								amCachedImages[amImageNum] = getimage;
							}
						}

						if ( (!externalimage) || (externalimage && amImageExternal) ) {

							/* note: no need to check amImageExternal in reload cycle */

							/* image is broken so store reference */
							amImageTries[amImageNum] = 0;
							amImages[amImageNum] = thisimage;
							amImageSrc[amImageNum] = imagesrc;

							/* Set initial delayed check with setTimeout convolution */
							var amImageReloadstimer = function(num) {
								setTimeout(function() {amDoImageReload(num);}, (amImageDelay * 1000) );
							}
							amImageReloadstimer(amImageNum);
						}
					}
				}

				amImageNum++;
			}
		}

		amLoopImages();

	}


	// -------------------
	// Stylesheet Reloader
	// -------------------

	// Note: stylesheet.rules check still comes back as undefined in Firefox,
	// but we are checking both .rules and .cssRules anyway so does not matter


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

	/* Stylesheet Reloader */
	if (amStyleReload == '1') {

		var amStyleTries = new Array(); var amStyleSrc =  new Array(); var amStyleLinks = new Array();
		var amStyleReloads = new Array(); var amStyleReloadDelays = new Array(); var amStyleNum = 0;

		/* Reload a Stylesheet */
		function amReloadStyle(thislink,stylesheet,newurl,oldurl) {

			if (amStyleDebug) {console.log('Reloading Stylesheet...');}

			/* TODO: is there a *truly* reliable way of testing for Firefox? */
			/* if so maybe we can do one OR the other of these methods not both */

			/* Most Browsers: attempt link href reset */
			thislink.href = 'javascript:void(0)';
			thislink.href = newurl;
			// FIXME: using title attribute here can break alternate stylesheets
			thislink.title = oldurl;
			if (amStyleDebug) {console.log('Reset Stylesheet URL: '+newurl);}

			/* Firefox: use <style>@import(url)</style> */
			newstyle = document.createElement('style');
			newstyle.textContent = '@import(\"'+newurl+'\");';
			// FIXME: using title attribute here can break alternate stylesheets
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

		function amLoadCachedStyle(amStyleNum) {
			/* TODO: maybe implement when cache is secure */
			return false;
		}

		/* Initial Delayed Stylesheet Check */
		function amStyleReload(amStyleNum) {
			stylesheet = ''; nocssrules = ''; norules = ''; zerorules = '';
			thislink = amStyleLinks[amStyleNum];
			stylesheethref = amStyleSrc[amStyleNum];
			if (amStyleDebug) {console.log('Stylesheet '+amStyleNum+' URL: '+stylesheethref);}

			if (document.styleSheets) {allstylesheets = document.styleSheets;}
			else {allstylesheets = document.sheet;}

			nocssrules = 'yes'; norules = 'yes';
			for (var i = 0, max = allstylesheets.length; i < max; i++) {
				matched = '';
				if (allstylesheets[i].href) {if (allstylesheets[i].href.indexOf(stylesheethref) > -1) {matched = 'yes';} }
				if ( (matched != 'yes') && (allstylesheets[i].title) ) {
					if (allstylesheets[i].title.indexOf(stylesheethref) > -1) {matched = 'yes';} }
				if (matched == 'yes') {
					if (amStyleDebug) {console.log('Link '+amStyleNum+' Matched Stylesheet '+i);}
					stylesheet = allstylesheets[i]; thisi = i;
					if (stylesheet.cssRules) {if (stylesheet.cssRules.length !== 0) {nocssrules = '';} }
					if (stylesheet.rules) {if (stylesheet.rules.length !== 0) {norules = '';} }
					break;
				}
			}

			if ( (nocssrules == 'yes') && (norules == 'yes') ) {

				if (amStyleDebug) {console.log('Stylesheet '+thisi+' has no rules.');}

				if (amStyleDebug) {console.log('Style '+amStyleNum+' Reloading: '+amStyleSrc[amStyleNum]);}
				newurl = amCacheBust(amStyleSrc[amStyleNum]);
				/* reload the stylesheet */
				amReloadStyle(thislink,stylesheet,newurl,amStyleSrc[amStyleNum]);
				if (amDoStyleCycling == '1') {amStyleReloadCycle(amStyleNum);}
			}
		}

		/* Style Reload Cycling */
		function amStyleReloadCycle(amStyleNum) {

			if (amStyleDebug) {console.log('Style '+amStyleNum+' Reload Cycle: '+amStyleSrc[amStyleNum]);}

			var amDoStyleReloadCycle = function(amStyleNum) {

				amStyleReloads[amStyleNum] = setInterval(function() {

					stylesheet = ''; nocssrules = ''; norules = ''; zerorules = '';
					stylesheethref = amStyleSrc[amStyleNum];
					thislink = amStyleLinks[amStyleNum];
					console.log('Stylesheet '+amStyleNum+' URL: '+stylesheethref);

					/* Check Stylesheets */

					if (document.styleSheets) {allstylesheets = document.styleSheets;}
					else {allstylesheets = document.sheet;}
					/* ? not sure what browsers actually support document.sheet ? */
					/* maybe for old IE/FF? - cannot find much reference to it */

					nocssrules = 'yes'; norules = 'yes';
					for (var i = 0, max = allstylesheets.length; i < max; i++) {
						matched = '';
						if (allstylesheets[i].href) {if (allstylesheets[i].href.indexOf(stylesheethref) > -1) {matched = 'yes';} }

						/* TODO: use an attribute other than title to store oldurl */
						/* or else can break alternate stylesheet usage */
						if ( (matched != 'yes') && (allstylesheets[i].title) ) {
							if (allstylesheets[i].title.indexOf(stylesheethref) > -1) {matched = 'yes';} }
						if (matched == 'yes') {
							/* if (amStyleDebug) {console.log('Link '+amStyleNum+' Matched Stylesheet '+i);} */
							stylesheet = allstylesheets[i]; thisi = i;
							if (stylesheet.cssRules) {if (stylesheet.cssRules.length !== 0) {nocssrules = '';} }
							if (stylesheet.rules) {if (stylesheet.rules.length !== 0) {norules = '';} }
						}
					}

					if ( (nocssrules == 'yes') && (norules == 'yes') ) {

						if (amStyleTries[amStyleNum] > amStyleAttempts) {
							doamStyleReloads = amStyleReloads[amStyleNum];
							if (amStyleDebug) {console.log('Stylesheet '+amStyleNum+' Cycle Finished');}
							clearInterval(doamStyleReloads); return;
						}
						var newurl = amCacheBust(amStyleSrc[amStyleNum]);
						/* reload the stylesheet */
						amReloadStyle(thislink,stylesheet,newurl,amStyleSrc[amStyleNum]);
						amStyleTries[amStyleNum] = amStyleTries[amStyleNum] + 1;

						return;
					}

					clearInterval(amStyleReloads[amStyleNum]);
					if (amStyleDebug) {console.log('Stylesheet '+amStyleNum+' Reload Cycle Cleared');}

					/* TODO: ? Maybe Trigger PrefixFree style reprocessing ? */

			 }, (amStyleCycle * 1000) );}

			 amDoStyleReloadCycle(amStyleNum);
		}

		/* Loop All Stylesheets */
		function amLoopStyles() {

			var amAllLinks = document.getElementsByTagName('link');

			for (var j = 0; j < (amAllLinks.length); j++) {

				thislink = amAllLinks[j];

				if (thislink.rel) {

					if ( (thislink.rel == 'stylesheet') || (thislink.rel == 'STYLESHEET') ) {

						/* TODO: could handle 'alternate stylesheet' also */
						/* using thislink.rel.indexOf('stylesheet') ? */
						/* but only when oldurl storage bug is fixed */

						if (!thislink.href) {
							/* ? not sure anymore if this does anything ? */
							if (thislink.url) {stylesheethref = thislink.url;}
						} else {stylesheethref = thislink.href;}

						if (stylesheethref) {

							amStyleLinks[amStyleNum] = thislink;

							if (stylesheethref.indexOf('javascript:void') != -1) {

								amStyleSrc[amStyleNum] = stylesheethref;
								if (stylesheethref.indexOf('?reloadtime=') > -1) {
									hrefparts = stylesheethref.split('?reloadtime=');
									amStyleSrc[amStyleNum] = hrefparts[0];
								}

								if (amStyleDebug) {console.log('Stylesheet '+amStyleNum+' URL: '+stylesheethref);}

								/* loop through all stylesheets and get the matching one */
								if (document.styleSheets) {allstylesheets = document.styleSheets;}
								else {allstylesheets = document.sheet;}

								stylesheet = ''; nocssrules = ''; norules = ''; zerorules = '';
								for (var i = 0; i < (allstylesheets.length); i++) {
									if ( (allstylesheets[i].href == stylesheethref)
									  || (allstylesheets[i].title == stylesheethref) ) {
										/* if (amStyleDebug) {console.log(stylesheethref+'-'+allstylesheets[i].href+'-'+allstylesheets[i].url);} */
										stylesheet = allstylesheets[i]; thisi = i;
										stylesheet[amStyleNum] = allstylesheets[i];
										break;
									}
								}

								if (amStyleDebug) {console.log('Stylesheet '+amStyleNum+' matched Stylesheet '+thisi);}

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
										/* TODO: check this as may need an XML request */
										/* var getstyle = document.createElement('style'); */
										/* getstyle.src = amAjaxUrl+'?action=am_get_style&src='+encodeURIComponent(stylesrc); */
										/* cachedstyles[amStyleNum] = getstyle; */
									}
								}

								if (zerorules == 'yes') {

									if (!externalstyle || (externalstyle && amStyleExternal) ) {

										if (amStyleDebug) {console.log('Link '+amStyleNum+' (Stylesheet '+thisi+') has no rules yet.');}

										/* Stylesheet rules not loaded */
										amStyleTries[amStyleNum] = 0;

										/* Set initial delayed check using setTimeout convolution */
										var amStyleReloadstimer = function(num) {
											if (amStyleDebug) {console.log('Stylesheet '+num+' reloading in '+amStyleDelay+' seconds.');}
											setTimeout(function() {amStyleReload(num);}, (amStyleDelay * 1000) );
										}
										amStyleReloadstimer(amStyleNum);
									}
								}
							}
						}
					}
				}

				amStyleNum++;
			}
		}

		amLoopStyles();
	}

/* end AutoMedic */
}

/* DocReady - Javacript-only cross-browser document ready function
// (this is a substitute for jQuery .ready() function, also minified)
// note: changed docReady to documentReady to prevent possible conflicts
// https://github.com/jfriend00/docReady */

(function(funcName, baseObj) {
    "use strict"; funcName = funcName || "docReady"; baseObj = baseObj || window;
    var readyList = []; var readyFired = false; var readyEventHandlersInstalled = false;
    function ready() {
        if (!readyFired) {
            readyFired = true;
            for (var i = 0; i < readyList.length; i++) {
                readyList[i].fn.call(window, readyList[i].ctx);
            }
            readyList = [];
        }
    }
    function readyStateChange() {if (document.readyState === "complete") {ready();} }

    baseObj[funcName] = function(callback, context) {
        if (readyFired) {setTimeout(function() {callback(context);}, 1); return;}
		else {readyList.push({fn: callback, ctx: context});}
        if (document.readyState === "complete" || (!document.attachEvent && document.readyState === "interactive")) {
            setTimeout(ready, 1);
        } else if (!readyEventHandlersInstalled) {
            if (document.addEventListener) {
                document.addEventListener("DOMContentLoaded", ready, false);
                window.addEventListener("load", ready, false);
            } else {
                document.attachEvent("onreadystatechange", readyStateChange);
                window.attachEvent("onload", ready);
            }
            readyEventHandlersInstalled = true;
        }
    }
})("documentReady", window);

/* Launch AutoMedic on Pageload! */
window.documentReady(AutoMedic);
