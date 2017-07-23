=== WP AutoMedic ===
Contributors: majick
Donate link: http://wordquest.org/contribute/?plugin=wp-automedic
Tags: automedic, medic, reload, refresh, regenerate, heal, images, css, stylesheets, script, resources, fix, broken, automatic, automattic
Author URI: http://dreamjester.net
Plugin URI: http://wordquest.org/plugins/wp-automedic/
Requires at least: 3.0.0
Tested up to: 4.8
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Fixes the Internet! Self-healing nanobots for your website! ...or at least, automatically reloads broken images and other page resources.

== Description ==

Fixes the Internet! Self-healing nanobots for your website! ...or at least, automatically reloads broken images and other page resources.

You spend ages to get everything working on your site, BUT then, it can still break
for visitors with a slow connection or temporary connectivity problems. A single failed
stylesheet load can make your site look like it is ugly or broken - or just from 1994. 
The only answer (until now) was the user refreshing the whole page and starting again... 
often only to have something else break and losing your visitor to frustration.

WP AutoMedic attempts to solve this problem by checking each resource on the page,
whether it is an image or stylesheet or script, and reloading ONLY those resources 
that failed to load. While it won't fix typos pointing to resources which don't exist,
it will at least try again (and as many times as you set it to) so that the webpage 
display can "heal itself" automatically (or rather, automedically!) in the majority 
of cases - mostly for slow or temporarily bad connections - without your site visitor
having to hit the refresh button and start all over again!

[WP AutoMedic Home] (http://wordquest.org/plugins/wp-automedic/)

= So what? What's in it for me? =

Well, first off, it simply means your site visitors will have a better overall 
experience when broken images and stylesheets etc. magically heal themselves before 
their eyes. They may not even notice, but they WILL notice if they DO break, and many
people can too easily assume your "site is broken" or "badly designed" even if it is 
actually their slow or interupted network connection that is at fault! It happens far
more than you think, and especially if you have a fast connection yourself, you may 
not have even stopped to consider if your users do. Now is the time to fix that!

Browsers have decided to ignore this common problem, but you don't have to. Instead, 
having WP AutoMedic in place for your visitors increases the stickiness, friendliness 
and usability of your site, which basically means they stay on your site longer - which
is probably what you want! - and will probably lead to an increase in real page views 
and thus subscriptions and/or sales etc. etc. If that doesn't "sell" you on the benefits
of installing and using WP Automedic, that's okay, I'm not trying - it's free. :-)

= What does it actually do? =

1. Loads after the webpage says it is loaded.
1. Checks all chosen elements for broken resources.
1. Sets a timed cycle for each broken resource.
1. Attempts to reload the resource on each cycle.
1. Magically medicates and heals your webpage.

= What resources does it work for? =

Checking a resource is loaded and reloading it is no simple feat, and different for
different page elements, and different browsers - which have different javascript 
functions or support - making is rather complex and time-consuming to code and test. 
So, I have starting with the most common page elements and am going from there. :-)

Currently WP AutoMedic works for:
* Broken Images (`<img>` elements) - comparitively pretty easy!
* Broken Stylesheets (`<link rel='stylesheet'>` elements) - pretty tricky!
(and yes it can check and reload stylesheets in Firefox!)

Does not currently work for:
* initial check of external stylesheets (some workarounds possible)
* stylesheets @imported inside inline `<style>` elements (possible)
* broken image and background URLs inside stylesheets (maybe possible) 

A future Pro Version may also provide for broken scripts and iframe loading...
Mostly Working (in progress) for:
* Broken Scripts (`<script src='somescript.js'>` elements) - difficult!
Not Working yet (workarounds in progress) for:
* Broken Iframes (`<iframe src='somepage.html'>` elements) - near impossible!
...and 'unowned' iframe load checking *cannot* be checked in any case.

Of course other alternatives and workarounds are being explored, and suggestions and
contributions are very welcome! It would be nice to improve WP AutoMedic to cover
more situations and be tested and improved for different browsers.

[WP AutoMedic Home] (http://wordquest.org/plugins/wp-automedic/)

== Installation ==

1. Upload `wp-automedic.zip` via the Plugins upload page.
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Visit the Settings -> WP AutoMedic menu to choose your options.
1. Select which resources to reload and where to reload them.
1. Set the delay/cycle/attempts for each selected resource and Save.

== Frequently Asked Questions ==

= How does it work? =

1. Loads itself after the webpage says it is finished.
1. Checks all chosen element types for broken resources.
1. Timed cycles through broken resources and reloads them.
1. When a resource is checked as loaded it's cycle stops.
1. or, when number of attempts set is reached it's cycle stops.


= How do I know it is working? =

Ideally, you wouldn't need this as your page loads fine every time... but of course,
that is not always the case, hence the need for this plugin in the first place. So 
most of the time you probably won't notice WP AutoMedic is actually doing anything, 
but like a medic should be, it is on standby in case of emergencies, and firing your 
medic (deactivating the plugin) just because you are not injured ("but my pages are 
loading fine!") may not be the best idea when you don't have to (this plugin is free.)

However, you may want to make sure the medic is well trained (that this plugin actually
works and does what it says) and so do a training exercise. The *testing* of this 
plugin script particularly tricky, simply because most of the time your page DOES 
load fine! So to truly know and test this *plugin* is working correctly (rather than 
just the *page* is loading correctly - ie. no injury) we need to set up a fake page 
with some broken resources (simulate an injury) and then look at that with AutoMedic 
loaded and see exactly what happens... 

To that end, you can test WP AutoMedic by putting the `[automedic-test]` shortcode on 
a new post or page - optionally save as a draft for later - and then click on Preview 
Post. The output of the shortcode simulates a number of broken resources in the content
area so that you can see the results. You can also check the browser console log to see 
all the AutoMedic events that are logged there along ith all those lovely lines of
red broken resources. Keep in mind that the output and result can be different in 
different browsers and test those if you like. You can adjust the plugin settings 
(elements to reload and reload times etc on the settings page) and reload the page 
again to check it, and further adjust to suit your liking.

= What do the different reloading Scopes mean? == 

You can set the reloading for each type of supported resource to run on the Front End,
Back End (Admin Area) of your site - or both - or neither... Minimum recommended setting
is Front End loading for both images and stylesheets. 


= Are there any limitations? =

Yes, definitely. First, this is never going to be perfect. Depending on which browser is 
used and what it's particular limitations are, the AutoMedic script may fail in part or 
entirely, if the browser does not have the needed browser support to do what it needs. 
(But, this could be said for just about any script and supporting older browsers has 
always been a web developer's nightmare.)

On the other hand, AutoMedic was written with this kept in mind specifically from the 
start, so the needed functions have been kept to a *bare minimum* of very commonly 
supported Javascript-only usage (not jQuery, even though the whole thing would have 
been much easier to write in jQuery!) and checks are in place for even thosee remotely 
unsupported functions used, so as to make sure it works most of the time *and* can 
fallback (or rather, failback) to doing nothing if the support really isn't there. 
As it improves and adds more checks like this, it will work better across more 
browsers. For a more specific list of limitations and issues, see Other Notes section.


= Can I use this with a Content Delivery Network (CDN)? =

Yes. Depending on the CDN it may or may not limit what AutoMedic can check and reload,
as some resources may be considered 'cross-domain' due to this. Specifically that is,
for stylesheets, that depending on the domain they are loaded from they may be treated
as external stylesheets if the resource URL is for a CDN on another domain. There are
workarounds in place for external stylesheets however, so read that section for more 
details. 


= Can I use this on non-Wordpress sites? =

The short answer is no. The longer answer is 'not yet!' WP AutoMedic is written in 
pure Javascript rather than jQuery to increase it's chance of success and also so it 
can be released as a standalone minimal script for other CMS and website platforms -
or basically for any HTML page in the future so the basic functions work just by adding 
an `automedic.js` script to the page (currently image and stylesheet reloading.)

The more advanced features will more than likely require a little more setup with the 
standalone version to get working as it may require buffering the entire page output. 
(WP AutoMedic can do this already for example using the WordpPress hooks.) To begin with 
however, the Wordpress plugin version will be used for development and testing of a
standalone version. (The WP version will probably always have more features as it is 
able to hook into the existing Wordpress settings, action hooks and page content much 
more easily and provide a settings interface.)


= Can WP AutoMedic help me check for specific-browser functionality? =

No, that is not it's purpose. It is an after-production helper for improving site user
experience rather than a development tool for fixing cross-browser functionality. If 
that is what you are looking for you can check out the `modernizr.js` library for the 
latest reliable checks on browser Javascript supports and CSS stylesheet rule support 
etc. Also check out NWMatcher and NWEvents if you are looking for cross-browser event 
testing and support. WP AutoMedic does attempt to use cross-browser compatible checks 
for it's own purposes as it needs to, but these should not be relied upon outside the
scope of the plugin for browser compatibility testing.


= What's the deal with external stylesheets? = 

It is difficult to check the load success or failure of external stylesheets, (those 
on a different domain to your main site domain) some may say impossible... browsers 
prevent any access to determining whether those style rules have ever been added for 
"security" reasons (despite the common protest "c'mon it's just a stylesheet.")

Using 'local' stylesheets (in this context meaning with the same main domain name as 
your site) is preferable wherever possible. But sometimes it is not, so in this case,
one effective solution is to rewrite the style tags to change the way they are loaded
before they 'hit' the page output, so they are loading in a way they can be checked the
first time. Fortunately, AutoMedic can hook into the enqueued Wordpress style tags and 
rebuild them to use this reload method. (...or, it can buffer the page output and replace
all the style tags on the page before outputting if you prefer.)

Another alternative is to just allow them to load as they normally would and to let 
the reload cycler reload them the first time using the alternative method so that 
their load state can be checked the second time. This can mean the stylesheet is 
loaded twice anyway, but fortunately will not affect the page further as the style 
rules already exist and are cascaded in any case (the C in Css.)


= What actual checks are used to determine if a resource is loaded? =

This varies depending on the type of resource, you may need to check the specifics
of the latest version as the checks improves. But basically the following combination
of checks are used to give the best cross-browser results (so far, having tested
numerous methods, but there are certainly some others to be tried.)

**Images:**
- loops image elements `<img>` to check them
*if any of these, considers the image unloaded:*
- `imageelement.naturalWidth == 0` (mostly this does the trick)
- `typeof imageelement.naturalWidth == 'undefined'`
- `!imageelement.complete` (Internet Explorer)
- `imageelement.readystate == 'uninitialized'`

**Stylesheets**
- loops link elements `<link>` (with `rel='stylesheet') to check
- loops `document.styleSheets` (with fallback to `document.sheet`)
- matches link element href to stylesheet href to get stylesheet
*if either of these, considers stylesheet unloaded:*
- `!stylesheet.cssRules` and `!stylesheet.rules`
- `stylesheet.cssRules.length === 0` and `stylesheet.rules.length === 0`
- caveat: does not (cannot) check 'external' stylesheets with this method


= What do I do if it's not working? =

If it's really "not working", I would like to know about it so it can be fixed, since
the whole point of this plugin is fixing things! However, you need to be totally sure 
that the resource you are having problems is working fine in itself on a normal page
(one without WP AutoMedic) before even considering the problem has anything to do with 
WP AutoMedic. Seriously, please double-check this before reporting a bug, there is just
no way to fix things otherwise because there would simply be no time left to check real 
issues if it is all taken up by misreported ones. That said, here's what you can do:

1. Take a moment to think about (and test?) whether the problem is one that could be
*caused* by AutoMedic OR is one not being *fixed* by AutoMedic so you have that clear.
1. This is because possible problems being *caused* by AutoMedic must take priority.
1. Retest the issue a few times yourself to reproduce it. Bugfixing relies on testing
and if the issue cannot be reproduced effectively, it simply cannot be fixed.
1. Check the known issues/limitations of AutoMedic to make sure it is something new.
1. Take note of the browser and *exact version used* when you had the problem.
1. Take note of your operating system and the *exact version used* also.
1. Install another browser, check the page and see if the result changes.
1. Note the other browser and version number and whether the issue reoccurred or not.
1. Optionally repeat with as many browsers as you think confirms/denies the issue.
1. Clearly write all this information and describe exactly how to reproduce the issue.
1. If you are a developer yourself, help out by trying to find both the bug and the 
solution yourself and possibly contributing that directly instead of a bug report.


= How do I do a manual test? =

To manually test, you can put a broken resource on a page (a HTML tag to an image src,
or link stylesheet href) and load the page. (If you already have a broken resource on a page, 
did you *triple* check it is not just a typo?) Then manually (eg. by FTP) rename an existing 
resource to where the resource URL is already pointing to. Depending on your delay and cycle 
settings the resource will then be found and loaded. eg:

1. Add `<img src="/images/nothinghere.gif">` to a page and Save.
1. Load or preview the post/page in your browser.
1. Rename `/images/somethinghere.gif` to `/images/nothinghere.gif`
1. Wait for the image reload cycle to kick in and reload the image.
1. To retest, rename the image back again before loading page.


== Screenshots ==



== Changelog ==

= 1.4.0 =
* Public Release Version *
* Added WordQuest integration
* Split off features under development
* Fix to Cache Busting Function
* Fix to Debug console logging options

= 1.3.0 =
* Release Candidate *
* Script Reloading Beta
* Script Events Beta
* Iframe Cycler Working
* Page Buffering Working
* Fallback Resource Cache Alpha
* Dynamic Loading Method Working
* Fallback Dynamic ImageLoad Method Working
* Stylesheet Reloading Optimized
* All js functions 'am' prefixed

= 1.2.0 =
* Beta Version
* Stylesheet Reloading Working
* Image Reloading Optimized

= 1.1.0 = 
* Alpha Version
* Image Reload Working

= 1.0.0 =
* Development Version
* Reload Cycling Working (sheesh!)
* Image and Stylesheet Testing

== Upgrade Notice ==


== Other Notes ==

[WP AutoMedic Home] (http://wordquest.org/plugins/wp-automedic/)

Like this plugin? Check out more of our free plugins here: 
[WordQuest] (http://wordquest.org/plugins/ "WordQuest Plugins")

Looking for an awesome theme? Check out my child theme framework:
[BioShip Child Theme Framework] (http://bioship.space "BioShip Child Theme Framework")

= Support = 
For support or if you have an idea to improve this plugin:
[WP AutoMedic Support] (http://wordquest.org/quest/quest-category/plugin-support/wp-automedic/ "WP AutoMedic Support Quests")

= Contribute = 
Help fund support, improvements and log priority feature requests by a gift of appreciation:
[Contribute to WP AutoMedic] (http://wordquest.org/contribute/?plugin=wp-automedic)

= Development =
To aid directly in development, please fork on Github and do a pull request:
[WP AutoMedic on Github] (http://github.com/majick777/wp-automedic/)

= Limitations =
* External stylesheets cannot be checked initially
* Iframe loading for 'unowned' iframes simply cannot be checked
* Dynamic loading method for scripts/iframes needs setAttribute support

= Known Issues =
* External stylesheets must be reloaded once before they can be checked
* Does not handle alternate stylesheet yet (rel=alternate stylesheet)
* Iframe checking/reloading for 'owned' iframes is in progress
* Dynamic load method fallback needed for IE6/IE7 (operation aborted bug)
* Javascript snippet needed for non-Wordpress Child Iframes

= Planned Updates/Features =
* frature: local cache to store external images/stylesheets for fallbacks?
* improvement: synchronize document.domain to help same-domain iframe checking
* improvement: reload stylesheets @imported inside `<style>` elements?
* test: are broken images URLs inside stylesheet rules added to reloading?
* feature integration: use Fallback.io script for handling script dependencies?
* logging: admin logging and compiling of 'complete fail' events / occurences
* logging: admin logging of successful resource reloads (match fail events)
* integration: possibly reapply PrefixFree.js to reloaded stylesheets?