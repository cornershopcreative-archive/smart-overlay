/**
 * Featherbox-powered conditional overlay
 */
(function($) {

	$.coverlay = function( options ) {
		options = options || window.coverlay_opts;
		var force = window.location.search.indexOf("forceoverlay");
		var alreadyOpened = false;
		var settings = $.extend({
			context : 'none',
			suppress: 'never',
			trigger : 'immediate',
			amount  : 1,
			width   : 600,
			id      : 'coverlay'
		}, options );

		// @todo refactor all code to still increment timers, etc even when not on pages that lightbox will be displayed on...
		// do nothing if it's disabled...
		if ( 'none' === settings.context ) return;

		// ...if it's supposed to be on the homepage and we're not...
		if ( 'home' === settings.context && !$('body').hasClass('home') ) return;

		// ...or if the already-seen cookie is still around
		if ( $.cookie('coverlay-seen-' + settings.id ) == 'true' && force === -1 ) return;

		// let's see if we meet our open criteria!
		// right away
		if ( 'immediate' == settings.trigger ) {
			openLightbox();
		// delay by n seconds
		} else if ( 'delay' == settings.trigger ) {
			setTimeout( function() { openLightbox(); }, settings.amount * 1000 );
		} else if ( 'scroll' == settings.trigger ) {
			$(window).on('scroll', function() {
				if ( $(window).scrollTop() >= settings.amount ) {
					openLightbox();
				}
			});
		} else if ( 'scroll-half' == settings.trigger ) {
			var halfHeight = ( $(document).height() / 2 ) - 20;
			$(window).on('scroll', function() {
				if ( $(window).scrollTop() >= halfHeight ) {
					openLightbox();
				}
			});
		} else if ( 'scroll-full' == settings.trigger ) {
			var offsetFullHeight = $(document).height() - 20;
			$(window).on('scroll', function() {
				if ( $(window).scrollTop() + $(window).height() >= offsetFullHeight ) {
					openLightbox();
				}
			});
		} else if ( 'pages' == settings.trigger ) {
			var pageCount = $.cookie('coverlay-pages-' + settings.id) || 0;
			if ( ++pageCount >= settings.amount ) {
				openLightbox();
				$.removeCookie('coverlay-pages-' + settings.id, { expires: 90, path: '/' });
			} else {
				$.cookie('coverlay-pages-' + settings.id, pageCount, { expires: 90, path: '/' });
			}
		} else if ( 'minutes' == settings.trigger ) {
			var minuteCount = parseFloat($.cookie('coverlay-minutes-' + settings.id)) || 0;
			if ( minuteCount >= settings.amount ) {
				openLightbox();
				$.removeCookie('coverlay-minutes-' + settings.id, { path: '/' });
			} else {
				setInterval( function() { lightboxTimer(); }, 6000 );
			}
		}

		function openLightbox() {

			if ( alreadyOpened ) return;

			// width... somewhere?
			$.featherlight( $('#coverlay-inner'), {type:'html'});
			alreadyOpened = true;

			// never hide
			if ( 'never' === settings.suppress ) return;

			var cookieOpts = { path: '/' };

			// set a cookie with appropriate expiration
			if ( settings.suppress == 'always' ) {
				cookieOpts.expires = 365 * 5;	// 5 years is basically forever
			} else if ( settings.suppress == 'wait-7' ) {
				cookieOpts.expires = 7;
			} else if ( settings.suppress == 'wait-30' ) {
				cookieOpts.expires = 30;
			} else if ( settings.suppress == 'wait-90' ) {
				cookieOpts.expires = 30;
			}

			$.cookie( 'coverlay-seen-' + settings.id, 'true', cookieOpts );
		}

		function lightboxTimer() {
			var maxMinutes = settings.amount,
				timePassed = parseFloat($.cookie('coverlay-minutes-' + settings.id)) || 0;
			// increment every half-second (setInterval = 500)
			timePassed += 0.1;
			if ( timePassed >= maxMinutes ) {
				openLightbox();
				$.removeCookie('coverlay-minutes-' + settings.id, { path: '/' });
			} else {
				$.cookie('coverlay-minutes-' + settings.id, timePassed, { path: '/'});
			}
		}

	};
}(jQuery));

jQuery(document).ready(function($) {
	$.coverlay();
});

/**
 * Featherlight - ultra slim jQuery lightbox
 * Version 1.2.3 - http://noelboss.github.io/featherlight/
 *
 * Copyright 2015, Noël Raoul Bossart (http://www.noelboss.com)
 * MIT Licensed.
**/
!function(a){"use strict";function b(a,c){if(!(this instanceof b)){var d=new b(a,c);return d.open(),d}this.id=b.id++,this.setup(a,c),this.chainCallbacks(b._callbackChain)}if("undefined"==typeof a)return void("console"in window&&window.console.info("Too much lightness, Featherlight needs jQuery."));var c=[],d=function(b){return c=a.grep(c,function(a){return a!==b&&a.$instance.closest("body").length>0})},e=function(a,b){var c={},d=new RegExp("^"+b+"([A-Z])(.*)");for(var e in a){var f=e.match(d);if(f){var g=(f[1]+f[2].replace(/([A-Z])/g,"-$1")).toLowerCase();c[g]=a[e]}}return c},f={keyup:"onKeyUp",resize:"onResize"},g=function(c){a.each(b.opened().reverse(),function(){return c.isDefaultPrevented()||!1!==this[f[c.type]](c)?void 0:(c.preventDefault(),c.stopPropagation(),!1)})},h=function(c){if(c!==b._globalHandlerInstalled){b._globalHandlerInstalled=c;var d=a.map(f,function(a,c){return c+"."+b.prototype.namespace}).join(" ");a(window)[c?"on":"off"](d,g)}};b.prototype={constructor:b,namespace:"featherlight",targetAttr:"data-featherlight",variant:null,resetCss:!1,background:null,openTrigger:"click",closeTrigger:"click",filter:null,root:"body",openSpeed:250,closeSpeed:250,closeOnClick:"background",closeOnEsc:!0,closeIcon:"&#10005;",loading:"",otherClose:null,beforeOpen:a.noop,beforeContent:a.noop,beforeClose:a.noop,afterOpen:a.noop,afterContent:a.noop,afterClose:a.noop,onKeyUp:a.noop,onResize:a.noop,type:null,contentFilters:["jquery","image","html","ajax","iframe","text"],setup:function(b,c){"object"!=typeof b||b instanceof a!=!1||c||(c=b,b=void 0);var d=a.extend(this,c,{target:b}),e=d.resetCss?d.namespace+"-reset":d.namespace,f=a(d.background||['<div class="'+e+"-loading "+e+'">','<div class="'+e+'-content">','<span class="'+e+"-close-icon "+d.namespace+'-close">',d.closeIcon,"</span>",'<div class="'+d.namespace+'-inner">'+d.loading+"</div>","</div>","</div>"].join("")),g="."+d.namespace+"-close"+(d.otherClose?","+d.otherClose:"");return d.$instance=f.clone().addClass(d.variant),d.$instance.on(d.closeTrigger+"."+d.namespace,function(b){var c=a(b.target);("background"===d.closeOnClick&&c.is("."+d.namespace)||"anywhere"===d.closeOnClick||c.closest(g).length)&&(b.preventDefault(),d.close())}),this},getContent:function(){var b=this,c=this.constructor.contentFilters,d=function(a){return b.$currentTarget&&b.$currentTarget.attr(a)},e=d(b.targetAttr),f=b.target||e||"",g=c[b.type];if(!g&&f in c&&(g=c[f],f=b.target&&e),f=f||d("href")||"",!g)for(var h in c)b[h]&&(g=c[h],f=b[h]);if(!g){var i=f;if(f=null,a.each(b.contentFilters,function(){return g=c[this],g.test&&(f=g.test(i)),!f&&g.regex&&i.match&&i.match(g.regex)&&(f=i),!f}),!f)return"console"in window&&window.console.error("Featherlight: no content filter found "+(i?' for "'+i+'"':" (no target specified)")),!1}return g.process.call(b,f)},setContent:function(b){var c=this;return(b.is("iframe")||a("iframe",b).length>0)&&c.$instance.addClass(c.namespace+"-iframe"),c.$instance.removeClass(c.namespace+"-loading"),c.$instance.find("."+c.namespace+"-inner").slice(1).remove().end().replaceWith(a.contains(c.$instance[0],b[0])?"":b),c.$content=b.addClass(c.namespace+"-inner"),c},open:function(b){var d=this;if(d.$instance.hide().appendTo(d.root),!(b&&b.isDefaultPrevented()||d.beforeOpen(b)===!1)){b&&b.preventDefault();var e=d.getContent();if(e)return c.push(d),h(!0),d.$instance.fadeIn(d.openSpeed),d.beforeContent(b),a.when(e).always(function(a){d.setContent(a),d.afterContent(b)}).then(d.$instance.promise()).done(function(){d.afterOpen(b)})}return d.$instance.detach(),a.Deferred().reject().promise()},close:function(b){var c=this,e=a.Deferred();return c.beforeClose(b)===!1?e.reject():(0===d(c).length&&h(!1),c.$instance.fadeOut(c.closeSpeed,function(){c.$instance.detach(),c.afterClose(b),e.resolve()})),e.promise()},chainCallbacks:function(b){for(var c in b)this[c]=a.proxy(b[c],this,a.proxy(this[c],this))}},a.extend(b,{id:0,autoBind:"[data-featherlight]",defaults:b.prototype,contentFilters:{jquery:{regex:/^[#.]\w/,test:function(b){return b instanceof a&&b},process:function(b){return a(b).clone(!0)}},image:{regex:/\.(png|jpg|jpeg|gif|tiff|bmp)(\?\S*)?$/i,process:function(b){var c=this,d=a.Deferred(),e=new Image,f=a('<img src="'+b+'" alt="" class="'+c.namespace+'-image" />');return e.onload=function(){f.naturalWidth=e.width,f.naturalHeight=e.height,d.resolve(f)},e.onerror=function(){d.reject(f)},e.src=b,d.promise()}},html:{regex:/^\s*<[\w!][^<]*>/,process:function(b){return a(b)}},ajax:{regex:/./,process:function(b){var c=a.Deferred(),d=a("<div></div>").load(b,function(a,b){"error"!==b&&c.resolve(d.contents()),c.fail()});return c.promise()}},iframe:{process:function(b){var c=new a.Deferred,d=a("<iframe/>").hide().attr("src",b).css(e(this,"iframe")).on("load",function(){c.resolve(d.show())}).appendTo(this.$instance.find("."+this.namespace+"-content"));return c.promise()}},text:{process:function(b){return a("<div>",{text:b})}}},functionAttributes:["beforeOpen","afterOpen","beforeContent","afterContent","beforeClose","afterClose"],readElementConfig:function(b,c){var d=this,e=new RegExp("^data-"+c+"-(.*)"),f={};return b&&b.attributes&&a.each(b.attributes,function(){var b=this.name.match(e);if(b){var c=this.value,g=a.camelCase(b[1]);if(a.inArray(g,d.functionAttributes)>=0)c=new Function(c);else try{c=a.parseJSON(c)}catch(h){}f[g]=c}}),f},extend:function(b,c){var d=function(){this.constructor=b};return d.prototype=this.prototype,b.prototype=new d,b.__super__=this.prototype,a.extend(b,this,c),b.defaults=b.prototype,b},attach:function(b,c,d){var e=this;"object"!=typeof c||c instanceof a!=!1||d||(d=c,c=void 0),d=a.extend({},d);var f=d.namespace||e.defaults.namespace,g=a.extend({},e.defaults,e.readElementConfig(b[0],f),d);return b.on(g.openTrigger+"."+g.namespace,g.filter,function(f){var h=a.extend({$source:b,$currentTarget:a(this)},e.readElementConfig(b[0],g.namespace),e.readElementConfig(this,g.namespace),d);new e(c,h).open(f)}),b},current:function(){var a=this.opened();return a[a.length-1]||null},opened:function(){var b=this;return d(),a.grep(c,function(a){return a instanceof b})},close:function(){var a=this.current();return a?a.close():void 0},_onReady:function(){var b=this;b.autoBind&&(b.attach(a(document),{filter:b.autoBind}),a(b.autoBind).filter("[data-featherlight-filter]").each(function(){b.attach(a(this))}))},_callbackChain:{onKeyUp:function(a,b){return 27===b.keyCode?(this.closeOnEsc&&this.$instance.find("."+this.namespace+"-close:first").click(),!1):a(b)},onResize:function(a,b){if(this.$content.naturalWidth){var c=this.$content.naturalWidth,d=this.$content.naturalHeight;this.$content.css("width","").css("height","");var e=Math.max(c/parseInt(this.$content.parent().css("width"),10),d/parseInt(this.$content.parent().css("height"),10));e>1&&this.$content.css("width",""+c/e+"px").css("height",""+d/e+"px")}return a(b)},afterContent:function(a,b){var c=a(b);return this.onResize(b),c}}}),a.featherlight=b,a.fn.featherlight=function(a,c){return b.attach(this,a,c)},a(document).ready(function(){b._onReady()})}(jQuery);

/*!
 * jQuery Cookie Plugin v1.4.1
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2006, 2014 Klaus Hartl
 * Released under the MIT license
 */
!function(a){"function"==typeof define&&define.amd?define(["jquery"],a):"object"==typeof exports?module.exports=a(require("jquery")):a(jQuery)}(function(a){function c(a){return h.raw?a:encodeURIComponent(a)}function d(a){return h.raw?a:decodeURIComponent(a)}function e(a){return c(h.json?JSON.stringify(a):String(a))}function f(a){0===a.indexOf('"')&&(a=a.slice(1,-1).replace(/\\"/g,'"').replace(/\\\\/g,"\\"));try{return a=decodeURIComponent(a.replace(b," ")),h.json?JSON.parse(a):a}catch(c){}}function g(b,c){var d=h.raw?b:f(b);return a.isFunction(c)?c(d):d}var b=/\+/g,h=a.cookie=function(b,f,i){if(arguments.length>1&&!a.isFunction(f)){if(i=a.extend({},h.defaults,i),"number"==typeof i.expires){var j=i.expires,k=i.expires=new Date;k.setMilliseconds(k.getMilliseconds()+864e5*j)}return document.cookie=[c(b),"=",e(f),i.expires?"; expires="+i.expires.toUTCString():"",i.path?"; path="+i.path:"",i.domain?"; domain="+i.domain:"",i.secure?"; secure":""].join("")}for(var l=b?void 0:{},m=document.cookie?document.cookie.split("; "):[],n=0,o=m.length;o>n;n++){var p=m[n].split("="),q=d(p.shift()),r=p.join("=");if(b===q){l=g(r,f);break}b||void 0===(r=g(r))||(l[q]=r)}return l};h.defaults={},a.removeCookie=function(b,c){return a.cookie(b,"",a.extend({},c,{expires:-1})),!a.cookie(b)}});