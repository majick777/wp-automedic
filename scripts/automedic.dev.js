/* ------------------ */
/* AutoMedic Reloader */
/* ------------------ */

/* note to javascript gurus: help is very welcome! I have mostly used javascript
// for user interfaces, not for heavy lifting like this, that said, it works. ;-)
// but need as many feedbacks, failbacks and fallbacks as we can get, lol. */

/* Development TODO List
/* - allow for querystring overrides ? */
/* - add a local stylesheet fallback cache ? */
/* - activate image fallback cache if/when it is secure */

if (typeof am == 'undefined') {
	var am = {}; am.imagedata = {}; am.styledata = {}; am.sitehost = location.hostname;
	am.images = {'reload':'frontend', 'delay':5, 'cycle':30, 'attempts':2, 'external':1, 'cache':1, 'import':0, 'debug':0};
	am.styles = {'reload':'frontend', 'delay':2, 'cycle':20, 'attempts':3, 'external':1, 'cache':1, 'import':0, 'debug':0};
}

/* ========= */
/* AutoMedic */
/* ========= */
function AutoMedic() {

	/* External Resource Checker */
	function AIsExternal(url) {
		if (url.indexOf(am.sitehost) > -1) {return false;}
		if (url.indexOf('http') == 0) {return true;}
		if (url.indexOf('HTTP') == 0) {return true;}
		return false;
	}

	/* Cache Buster Function */
	/* without this the browser may just load the (browser) cached version of the
	// resource, which we do not want as it may be empty or failed to load */
	function ACacheBust(url) {
		thedate = new Date(); thetime = thedate.getTime();
		if (typeof url == 'undefined') {return 'javascript:void(0);';}
		if (url.indexOf('reloadtime=') > -1) {
			if (url.indexOf('?reloadtime=') > -1) {
				urlparts = url.split('?reloadtime=');
				newurl = urlparts[0]+'?reloadtime='+thetime;
			}
			if (url.indexOf('&reloadtime=') > -1) {
				urlparts = url.split('&reloadtime=');
				newurl = urlparts[0]+'&reloadtime='+thetime;
			}
		} else {
			if (url.indexOf('?') > -1) {newurl = url+'&reloadtime='+thetime;}
			else {newurl = url+'?reloadtime='+thetime;}
		}
		return newurl;
	}

	/* Image Reloader
	// --------------
	// Ref: http://stackoverflow.com/questions/8968576/how-to-detect-image-load-failure-and-if-fail-attempt-reload-until-success
	// Ref: http://stackoverflow.com/questions/92720/jquery-javascript-to-replace-broken-images

	// Test Notes:
	// all tested fine as naturalWidth == 0 for broken images (only Internet Explorer comes back complete as false)
	// nothing comes back readystate as uninitialized? but some older browsers might so may as well keep it */

	if (am.images.reload == '1') {

		/* Set Empty Image Data Arrays */
		am.imagedata = {'tries':[], 'images':[], 'sources':[], 'reloads':[], 'cached':[], 'count':0};

		/* Test an Image Load */
		function ATestImageLoad(el) {
			if ( (!el.complete) || (typeof el.naturalWidth == 'undefined')
			  || (el.naturalWidth == 0) || (el.readystate == 'uninitialized') ) {return false;}
			return true;
		}

		/* Load a Cached Image */
		function ALoadCachedImage(count) {
			/* TODO: maybe implement when image cache is secure */
			/* check for and test a cached local copy */
			/* 	cachedimage = am.imagedata.cached[count];
			 	testcachedimage = ATestImageLoad(cachedimage);} */
			/* if found replace the original image src */
			/* if (testcachedimage) {
				am.imagedata.image[count].src = 'javascript:void(0);';
				am.imagedate.images[count].src = cachedimage.src; return true;
			  } */
			return false;
		}

		/* Initial Reload Check */
		function ADoImageReload(count) {
			thisimage = am.imagedata.images[count];
			testimage = ATestImageLoad(thisimage);
			if (!testimage) {
			  	if (am.images.debug) {console.log('Image '+count+' Reloading');}
				imagesrc = thisimage.src;

				/* external image check */
				external = AIsExternal(imagesrc);
				if (external && am.images.cache) {
					if (typeof ALoadCachedImage == 'function') {
						loadcached = ALoadCachedImage(count);
						if (loadcached) {return;}
					}
				}

				/* attempt a simple image reload */
				am.imagedata.images[count].src = 'javascript:void(0);';
				am.imagedata.images[count].src = ACacheBust(am.imagedata.sources[count]);
				if (am.images.debug) {console.log('Reloaded Image '+count+': '+am.imagedata.sources[count]);}

				/* start the reload cycle */
				if (am.images.cycling == '1') {AImageReloadCycle(count);}
			}
		}

		/* Image Reload Cycle */
		function AImageReloadCycle(count) {
			if (am.images.debug) {console.log('Start Image '+count+' Reload Cycle');}
			var ADoImageReloadCycle = function(count) {
				am.imagedata.reloads[count] = setInterval(function() {

					/* abort if above attempt value */
					if (am.imagedata.tries[count] > am.images.attempts) {
						if (am.images.debug) {console.log('Image '+count+' Reload Cycle Finished');}
						clearInterval(am.imagedata.reloads[count]);	return;
					}

					/* retest the image */
					thisimage = am.imagedata.images[count];
					testimage = ATestImageLoad(thisimage);
					if (!testimage) {
						if (am.images.debug) {console.log('Image '+count+' Reload Cycling');}

						/* check image cache */
						external = AIsExternal(imagesrc);
						if (external && am.images.cache) {
							if (typeof ALoadCachedImage == 'function') {
								/* try to load a cached image */
								loadcached = ALoadCachedImage(count);
								if (loadcached) {
									if (am.images.debug) {console.log('Image '+count+' Reload Cycle Cleared');}
									clearInterval(am.imagedata.reloads[count]); return;
								}
							}
						}

						/* attempt a reload */
						am.imagedata.images[count].src = 'javascript:void(0);';
						am.imagedata.images[count].src = ACacheBust(am.imagedata.sources[count]);
						if (am.images.debug) {console.log('Reloaded Image '+count+': '+am.imagedata.sources[count]);}
						am.imagedata.tries[count] = am.imagedata.tries[count] + 1;
						return;
					}
					/* clear the reload cycle */
					clearInterval(am.imagedata.reloads[count]);
					if (am.images.debug) {console.log('Image '+count+' Reload Cycle Cleared');}

			 	}, (am.images.cycle * 1000) );

				/* keep cycling */
			 	ADoImageReloadCycle(count);
			}
		}

		/* Loop All Images */
		function ALoopImages() {

			AAllImages = document.getElementsByTagName('img');

			for (var j = 0; j < AAllImages.length; j++) {

				thisimage = AAllImages[j];
				imagesrc = thisimage.src;
				count = am.imagedata.count;

				if (imagesrc.indexOf('javascript:void') < 0) {

					/* test the image */
					testimage = ATestImageLoad(thisimage);

					if (!testimage) {

						if (am.images.debug) {console.log('Image '+count+' Source: '+imagesrc);}

						/* external image check */
						external = AIsExternal(imagesrc);
						if (external) {
							/* for an external image, check/cache a local copy
							if ( (am.images.external) && (am.images.cache) ) {
								ALoadCachedImages(count);
							} */
						}

						if (!external || (external && am.images.external)) {

							/* note: no need to check am.images.external in reload cycle */

							/* image is broken so store reference */
							am.imagedata.tries[count] = 0;
							am.imagedata.images[count] = thisimage;
							am.imagedata.sources[count] = imagesrc;

							/* Set initial delayed check with setTimeout convolution */
							var AImageReloadTimer = function(c) {
								if (am.images.debug) {console.log('Image '+c+' reloading in '+am.images.delay+' seconds.');}
								setTimeout(function() {ADoImageReload(c);}, (am.images.delay * 1000) );
							}
							AImageReloadTimer(count);

							/* increment image count */
							am.imagedata.count++;
						}
					}
				}
			}
		}
		/* start the image check loop */
		ALoopImages();
	}


	/* -------------------
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
	/*

	/* Stylesheet Reloader */
	if (am.styles.reload == '1') {

		/* Set Empty Stylesheet Data Arrays */
		am.styledata = {'tries':[], 'links':[], 'sources':[], 'reloads':[], 'cached':[], 'count':0};

		/* Stylesheet Load Test */
		/* note: may fail on local development as treated as external sheets?! */
		function ATestStylesheetLoad(stylesheet) {
			nocssrules = false; norules = false;
			if ('cssRules' in stylesheet) {
				try {if (!stylesheet.cssRules || (stylesheet.cssRules.length === 0)) {nocssrules = true;} }
				catch(e) {nocssrules = true;}
			}
			if ('rules' in stylesheet) {
				try {if (!stylesheet.rules || (stylesheet.rules.length === 0)) {norules = true;} }
				catch(e) {norules = true;}
			}
			if (nocssrules && norules) {return false;} else {return true;}
		}

		/* Reload a Stylesheet */
		function AReloadStyle(thislink,stylesheet,newurl,oldurl) {

			if (am.styles.debug) {console.log('Reloading Stylesheet...');}

			/* TODO: is there a *truly* reliable way of testing for Firefox? */
			/* if so maybe we can do one OR the other of these methods not both */

			/* Most Browsers: attempt link href reset */
			/* thislink.href = 'javascript:void(0)';
			thislink.href = newurl;
			thislink.setAttribute('data-href',oldurl);
			if (am.styles.debug) {console.log('Reset Stylesheet URL: '+newurl);} */

			/* Firefox: use <style>@import(url)</style> */
			newstyle = document.createElement('style');
			if ('id' in thislink) {newstyle.setAttribute('id',thislink.id);}
			if ('media' in thislink) {newstyle.setAttribute('media',thislink.media);}
			newstyle.textContent = '@import(\"'+newurl+'\");';
			newstyle.setAttribute('data-href',oldurl);
			document.getElementsByTagName('head')[0].appendChild(newstyle);
			if (am.styles.debug) {console.log('Added Style Import: '+newurl);}

			/* Alternative Method */
			/* (adding a new link element) */
			/*
				newstylesheet = document.createElement('link');
				newstylesheet.href = newurl;
				newstylesheet.title = oldurl;
				newstylesheet.rel = 'stylesheet';
				document.getElementsByTagName('head')[0].appendChild(newstylesheet);
				thisstylesheet = document.styleSheets[document.styleSheets.length-1];
				thisstylesheet.title = encodeURIComponent(oldurl);
				if (am.styles.debug) {console.log('Added Link Stylesheet: '+newurl);}
			*/
		}

		function AReloadExternalStyle(thislink,stylesheet,newurl,oldurl) {

			if (am.styles.debug) {console.log('Reloading External Stylesheet...');}

			/* use <style>@import(url)</style> */
			newstyle = document.createElement('style');
			newstyle.textContent = '@import(\"'+newurl+'\");';
			newstyle.setAttribute('data-href',oldurl);
			document.getElementsByTagName('head')[0].appendChild(newstyle);
		}

		function ALoadCachedStyle(count) {
			/* TODO: maybe implement when cache is secure */
			return false;
		}

		/* Initial Delayed Stylesheet Check */
		function AStyleReload(count) {

			thislink = am.styledata.links[count];
			href = am.styledata.sources[count]; datahref = '';
			if ('datahref' in thislink) {datahref = thislink.getAttribute('data-href');}
			if (am.styles.debug) {console.log('Stylesheet '+count+' URL: '+href);}

			if (document.styleSheets) {allstylesheets = document.styleSheets;}
			else {allstylesheets = document.sheet;}

			stylesheet = false; zerorules = false; matched = false;
			for (var i = 0; i < allstylesheets.length; i++) {
				if (!matched) {
					if (allstylesheets[i].href) {
						if ( (allstylesheets[i].href.indexOf(href) > -1) || (allstylesheets[i].href.indexOf(datahref) > -1) ) {
						  if (am.styles.debug) {console.log('Link '+count+' Matched Stylesheet '+i);}
						  stylesheet = allstylesheets[i]; thisi = i; matched = true;
						}
					}
				}
			}

			if (stylesheet) {

				external = AIsExternal(href);
				if (!external || (external && am.styles.external)) {
					zerorules = ATestStylesheetLoad(stylesheet);
				}

				if (zerorules) {

					if (am.styles.debug) {
						console.log('Stylesheet '+thisi+' has no rules.');
						console.log('Style '+count+' Reloading: '+am.styledata.sources[count]);
					}

					/* reload the stylesheet */
					newurl = ACacheBust(am.styledata.sources[count]);
					AReloadStyle(thislink,stylesheet,newurl,am.styledata.sources[count]);

					/* start the reload cycle */
					if (am.styles.cycling == '1') {AStyleReloadCycle(count);}
				}
			}
		}

		/* Style Reload Cycling */
		function AStyleReloadCycle(count) {

			if (am.styles.debug) {console.log('Style '+count+' Reload Cycle: '+am.styledata.sources[count]);}

			var ADoStyleReloadCycle = function(count) {
				am.styledata.reloads[count] = setInterval(function() {

					/* abort if above attempt value */
					if (am.styledata.tries[count] > am.styles.attempts) {
						/* am.styledata.reloads = am.styledata.reloads[count]; */
						if (am.styles.debug) {console.log('Stylesheet '+count+' Cycle Finished');}
						clearInterval(am.styledata.reloads[count]); return;
					}

					datahref = '';
					href = am.styledata.sources[count];
					thislink = am.styledata.links[count];
					if ('datahref' in thislink) {datahref = thislink.getAttribute('data-href');}
					if (am.styles.debug) {console.log('Stylesheet '+count+' URL: '+href);}

					/* Check Stylesheets */
					if (document.styleSheets) {allstylesheets = document.styleSheets;}
					else {allstylesheets = document.sheet;}

					stylesheet = false; zerorules = false; matched = false;
					for (var i = 0; i < allstylesheets.length; i++) {
						if (!matched) {
							if (allstylesheets[i].href) {
								if ( (allstylesheets[i].href.indexOf(href) > -1) || (allstylesheets[i].href.indexOf(datahref) > -1) ) {
									if (am.styles.debug) {console.log('Link '+count+' Matched Stylesheet '+i);}
									stylesheet = allstylesheets[i]; thisi = i; matched = true;
								}
							}
						}
					}

					if (stylesheet) {

						external = AIsExternal(href);
						if (!external || (external && am.styles.external)) {
							zerorules = ATestStylesheetLoad(stylesheet);
						}

						if (zerorules) {

							/* reload the stylesheet */
							var newurl = ACacheBust(am.styledata.sources[count]);
							AReloadStyle(thislink,stylesheet,newurl,am.styledata.sources[count]);
							am.styledata.tries[count] = am.styledata.tries[count] + 1;
							return;
						}
					}

					clearInterval(am.styledata.reloads[count]);
					if (am.styles.debug) {console.log('Stylesheet '+count+' Reload Cycle Cleared');}

					/* TODO: ? Maybe Trigger PrefixFree style reprocessing ? */

			 }, (am.styles.cycle * 1000) );}

			 ADoStyleReloadCycle(count);
		}

		/* Loop All Stylesheets */
		function ALoopStyles() {

			AAllLinks = document.getElementsByTagName('link');

			for (var j = 0; j < AAllLinks.length; j++) {

				thislink = AAllLinks[j];
				count = am.styledata.count;
				datahref = false;
				if ('datahref' in thislink) {datahref = thislink.getAttribute('data-href');}

				/* TODO: could handle 'alternate stylesheet' also */
				/* using thislink.rel.indexOf('stylesheet') ? */
				if (thislink.rel && (thislink.rel.toLowerCase() == 'stylesheet')) {

					if (!thislink.href) {
						/* ? not sure anymore if this does anything ? */
						if (thislink.url) {href = thislink.url;}
					} else {href = thislink.href;}

					if (href && (href.indexOf('javascript:void') < 0)) {

						am.styledata.links[count] = thislink;
						am.styledata.sources[count] = href;
						if (am.styles.debug) {console.log('Stylesheet '+count+' URL: '+href);}

						/* loop through all stylesheets and get the matching one */
						if (document.styleSheets) {allstylesheets = document.styleSheets;}
						else {allstylesheets = document.sheet;}

						stylesheet = false; zerorules = false; matched = false;
						for (var i = 0; i < allstylesheets.length; i++) {
							if (!matched) {
								if (allstylesheets[i].href) {
									if ( (allstylesheets[i].href.indexOf(href) > -1) || (allstylesheets[i].href.indexOf(datahref) > -1) ) {
										if (am.styles.debug) {console.log('Stylesheet '+count+' matched Stylesheet '+i);}
										stylesheet = allstylesheets[i]; thisi = i; matched = true;
									}
								}
							}
						}

						/* external stylesheet URL check */
						if (stylesheet) {
							external = AIsExternal(href);
							if (!external) {
								zerorules = ATestStylesheetLoad(stylesheet);
							} else {

								/* do not test for rules to avoid security failure */
								/* on external stylesheet, just consider it as failed */
								/* - as it will reload from browser cache anyway */
								/* and it can then be checked after first reload */
								/* TEST: a try/catch block here as partial workaround? */

								zerorules = true;

								/* TODO: cache a local copy of external stylesheet? */
								/* if ( (am.styles.external) && (am.styles.cache) ) { */
									/* an external style, check/cache a local copy */
									/* TODO: check this as may need an XML request */
									/* var getstyle = document.createElement('style'); */
									/* getstyle.src = am.ajaxurl+'?action=am_get_style&src='+encodeURIComponent(stylesrc); */
									/* cachedstyles[count] = getstyle; */
								/* } */
							}

							if (zerorules) {
								if (!external || (external && am.styles.external) ) {

									if (am.styles.debug) {console.log('Link '+count+' (Stylesheet '+thisi+') has no rules yet.');}

									/* Stylesheet rules not loaded */
									am.styledata.tries[count] = 0;

									/* Set initial delayed check using setTimeout convolution */
									var AStyleReloadTimer = function(c) {
										if (am.styles.debug) {console.log('Stylesheet '+c+' reloading in '+am.styles.delay+' seconds.');}
										setTimeout(function() {AStyleReload(c);}, (am.styles.delay * 1000) );
									}
									AStyleReloadTimer(count);

									/* increment stylesheet count */
									am.styledata.count++;
								}
							}
						}
					}
				}
			}
		}
		/* start the style check loop */
		ALoopStyles();
	}
}
/* end AutoMedic */

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
