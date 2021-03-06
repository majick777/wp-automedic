if (typeof am == undefined) {
	var am = {}; am.imagedata = {}; am.styledata = {}; am.sitehost = location.hostname;
	am.images = {'reload':'frontend', 'delay':5, 'cycle':30, 'attempts':2, 'external':1, 'cache':1, 'import':0, 'debug':0};
	am.styles = {'reload':'frontend', 'delay':2, 'cycle':20, 'attempts':3, 'external':1, 'cache':1, 'import':0, 'debug':0};
}
function AutoMedic() {
	function amIsExternal(url) {
		if (url.indexOf(am.sitehost) > -1) {return false;}
		if (url.indexOf('http') == 0) {return true;}
		if (url.indexOf('HTTP') == 0) {return true;}
		return false;
	}
	function amCacheBust(url) {
		d = new Date(); t = d.getTime();
		if (typeof url == 'undefined') {return 'javascript:void(0);';}
		if (url.indexOf('reloadtime=') > -1) {
			if (url.indexOf('?reloadtime=') > -1) {
				urlparts = url.split('?reloadtime=');
				newurl = urlparts[0]+'?reloadtime='+t;
			}
			if (url.indexOf('&reloadtime=') > -1) {
				urlparts = url.split('&reloadtime=');
				newurl = urlparts[0]+'&reloadtime='+t;
			}
		} else {
			if (url.indexOf('?') > -1) {newurl = url+'&reloadtime='+t;}
			else {newurl = url+'?reloadtime='+t;}
		}
		return newurl;
	}
	if (am.images.reload == '1') {
		am.imagedata = {'tries':[], 'images':[], 'sources':[], 'reloads':[], 'cached':[], 'count':0};
		function amTestImageLoad(el) {
			if ( (!el.complete) || (typeof el.naturalWidth == 'undefined')
			  || (el.naturalWidth == 0) || (el.readystate == 'uninitialized') ) {return false;}
			return true;
		}
		function amLoadCachedImage(count) {
			return false;
		}
		function amDoImageReload(count) {
			thisimage = am.imagedata.images[count];
			testimage = amTestImageLoad(thisimage);
			if (!testimage) {
			  	if (am.images.debug) {console.log('Image '+count+' Reloading');}
				imagesrc = thisimage.src;
				external = amIsExternal(imagesrc);
				if (external && am.images.cache) {
					if (typeof amLoadCachedImage == 'function') {
						loadcached = amLoadCachedImage(count);
						if (loadcached) {return;}
					}
				}
				am.imagedata.images[count].src = 'javascript:void(0);';
				am.imagedata.images[count].src = amCacheBust(am.imagedata.sources[count]);
				if (am.images.debug) {console.log('Reloaded Image '+count+': '+am.imagedata.sources[count]);}
				if (am.images.cycling == '1') {amImageReloadCycle(count);}
			}
		}
		function amImageReloadCycle(count) {
			if (am.images.debug) {console.log('Start Image '+count+' Reload Cycle');}
			var amDoImageReloadCycle = function(count) {
				am.imagedata.reloads[count] = setInterval(function() {
					if (am.imagedata.tries[count] > am.images.attempts) {
						if (am.images.debug) {console.log('Image '+count+' Reload Cycle Finished');}
						clearInterval(am.imagedata.reloads[count]); return;
					}
					thisimage = am.imagedata.images[count];
					testimage = amTestImageLoad(thisimage);
					if (!testimage) {
						if (am.images.debug) {console.log('Image '+count+' Reload Cycling');}
						external = amIsExternal(imagesrc);
						if (external && am.images.cache) {
							if (typeof amLoadCachedImage == 'function') {
								loadcached = amLoadCachedImage(count);
								if (loadcached) {
									if (am.images.debug) {console.log('Image '+count+' Reload Cycle Cleared');}
									clearInterval(am.imagedata.reloads[count]); return;
								}
							}
						}
						am.imagedata.images[count].src = 'javascript:void(0);';
						am.imagedata.images[count].src = amCacheBust(am.imagedata.sources[count]);
						if (am.images.debug) {console.log('Reloaded Image '+count+': '+am.imagedata.sources[count]);}
						am.imagedata.tries[count] = am.imagedata.tries[count] + 1;
						return;
					}
					clearInterval(am.imagedata.reloads[count]);
					if (am.images.debug) {console.log('Image '+count+' Reload Cycle Cleared');}
			 	}, (am.images.cycle * 1000) );
			 	amDoImageReloadCycle(count);
			}
		}
		function amLoopImages() {
			amAllImages = document.getElementsByTagName('img');
			for (var j = 0; j < amAllImages.length; j++) {
				thisimage = amAllImages[j];
				imagesrc = thisimage.src;
				count = am.imagedata.count;
				if (imagesrc.indexOf('javascript:void') < 0) {
					testimage = amTestImageLoad(thisimage);
					if (!testimage) {
						if (am.images.debug) {console.log('Image '+count+' Source: '+imagesrc);}
						external = amIsExternal(imagesrc);
						if (external) {
						}
						if (!external || (external && am.images.external)) {
							am.imagedata.tries[count] = 0;
							am.imagedata.images[count] = thisimage;
							am.imagedata.sources[count] = imagesrc;
							var amImageReloadTimer = function(c) {
								if (am.images.debug) {console.log('Image '+c+' reloading in '+am.images.delay+' seconds.');}
								setTimeout(function() {amDoImageReload(c);}, (am.images.delay * 1000) );
							}
							amImageReloadTimer(count);
							am.imagedata.count++;
						}
					}
				}
			}
		}
		amLoopImages();
	}
	if (am.styles.reload == '1') {
		am.styledata = {'tries':[], 'links':[], 'sources':[], 'reloads':[], 'cached':[], 'count':0};
		function amTestStylesheetLoad(stylesheet) {
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
		function amReloadStyle(thislink,stylesheet,newurl,oldurl) {
			if (am.styles.debug) {console.log('Reloading Stylesheet...');}
			newstyle = document.createElement('style');
			if ('id' in thislink) {newstyle.setAttribute('id',thislink.id);}
			if ('media' in thislink) {newstyle.setAttribute('media',thislink.media);}
			newstyle.textContent = '@import(\"'+newurl+'\");';
			newstyle.setAttribute('data-href',oldurl);
			document.getElementsByTagName('head')[0].appendChild(newstyle);
			if (am.styles.debug) {console.log('Added Style Import: '+newurl);}
		}
		function amReloadExternalStyle(thislink,stylesheet,newurl,oldurl) {
			if (am.styles.debug) {console.log('Reloading External Stylesheet...');}
			newstyle = document.createElement('style');
			newstyle.textContent = '@import(\"'+newurl+'\");';
			newstyle.setAttribute('data-href',oldurl);
			document.getElementsByTagName('head')[0].appendChild(newstyle);
		}
		function amLoadCachedStyle(count) {
			return false;
		}
		function amStyleReload(count) {
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
				external = amIsExternal(href);
				if (!external || (external && am.styles.external)) {
					zerorules = amTestStylesheetLoad(stylesheet);
				}
				if (zerorules) {
					if (am.styles.debug) {
						console.log('Stylesheet '+thisi+' has no rules.');
						console.log('Style '+count+' Reloading: '+am.styledata.sources[count]);
					}
					newurl = amCacheBust(am.styledata.sources[count]);
					amReloadStyle(thislink,stylesheet,newurl,am.styledata.sources[count]);
					if (am.styles.cycling == '1') {amStyleReloadCycle(count);}
				}
			}
		}
		function amStyleReloadCycle(count) {
			if (am.styles.debug) {console.log('Style '+count+' Reload Cycle: '+am.styledata.sources[count]);}
			var amDoStyleReloadCycle = function(count) {
				am.styledata.reloads[count] = setInterval(function() {
					if (am.styledata.tries[count] > am.styles.attempts) {
						if (am.styles.debug) {console.log('Stylesheet '+count+' Cycle Finished');}
						clearInterval(am.styledata.reloads[count]);	return;
					}
					datahref = '';
					href = am.styledata.sources[count];
					thislink = am.styledata.links[count];
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
						external = amIsExternal(href);
						if (!external || (external && am.styles.external)) {
							zerorules = amTestStylesheetLoad(stylesheet);
						}
						if (zerorules) {
							var newurl = amCacheBust(am.styledata.sources[count]);
							amReloadStyle(thislink,stylesheet,newurl,am.styledata.sources[count]);
							am.styledata.tries[count] = am.styledata.tries[count] + 1;
							return;
						}
					}
					clearInterval(am.styledata.reloads[count]);
					if (am.styles.debug) {console.log('Stylesheet '+count+' Reload Cycle Cleared');}
			 }, (am.styles.cycle * 1000) );}
			 amDoStyleReloadCycle(count);
		}
		function amLoopStyles() {
			var amAllLinks = document.getElementsByTagName('link');
			for (var j = 0; j < amAllLinks.length; j++) {
				thislink = amAllLinks[j];
				count = am.styledata.count;
				datahref = false;
				if ('datahref' in thislink) {datahref = thislink.getAttribute('data-href');}
				if (thislink.rel && (thislink.rel.toLowerCase() == 'stylesheet')) {
					if (!thislink.href) {
						if (thislink.url) {href = thislink.url;}
					} else {href = thislink.href;}
					if (href && (href.indexOf('javascript:void') < 0)) {
						am.styledata.links[count] = thislink;
						am.styledata.sources[count] = href;
						if (am.styles.debug) {console.log('Stylesheet '+count+' URL: '+href);}
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
						if (stylesheet) {
							external = amIsExternal(href);
							if (!external) {
								zerorules = amTestStylesheetLoad(stylesheet);
							} else {
								zerorules = true;
							}
							if (zerorules) {
								if (!external || (external && am.styles.external) ) {
									if (am.styles.debug) {console.log('Link '+count+' (Stylesheet '+thisi+') has no rules yet.');}
									am.styledata.tries[count] = 0;
									var amStyleReloadTimer = function(c) {
										if (am.styles.debug) {console.log('Stylesheet '+c+' reloading in '+am.styles.delay+' seconds.');}
										setTimeout(function() {amStyleReload(c);}, (am.styles.delay * 1000) );
									}
									amStyleReloadTimer(count);
									am.styledata.count++;
								}
							}
						}
					}
				}
			}
		}
		amLoopStyles();
	}
}
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
window.documentReady(AutoMedic);