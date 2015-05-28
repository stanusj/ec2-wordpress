var ua = navigator.userAgent.toLowerCase();
var isie = /msie/.test(ua);
var iev = parseFloat((ua.match(/.*(?:rv|ie)[\/: ](.+?)([ \);]|$)/) || [])[1]);

(function($)
{
	$.fn.cattr = function(key, value, attribute)
	{
		if (typeof attribute == 'undefined')
		{
			attribute = 'className';
		}

		var $object = $(this).eq(0);
		var class_name = '';

		if (key != null)
		{
			var classes = $object[0][attribute].split(' ');

			for (var i = 0; i < classes.length; i++)
			{
				var class_data = classes[i].split('-');
				var class_value = class_data.pop();
				var class_key = class_data.join('-');

				if (class_key == key)
				{
					class_name = classes[i];
				}
			}
		}

		if (typeof value == 'undefined' || value == null)
		{
			return class_name.substr(key.length+1);
		} else
		{
			if (class_name != '')
			{
				$object[0][attribute] = $object[0][attribute].replace(class_name, key + '-' + value);
			} else
			{
				$object[0][attribute] = $object[0][attribute] + ' ' + key + '-' + value;
			}
		}

		return this;
	};

	//gridslider_start
	/*
	Ether Gridslider 1.3 jQuery Plugin for jQuery 1.5+
	created: May 2012
	latest update: September 30, 2012
	copyright: Por Design 2012
	contact: contact.pordesign@gmail.com
	website: http://ether-wp.com/ or http://pordesign.eu/
	buy license at: http://codecanyon.net/item/ether-grid-slider-jquery-plugin/1713182
	*/

	$(document).ready(function($) {

		"use strict";

		if(!jQuery.easing.easeInQuad)
		{
	jQuery.extend(jQuery.easing,{def:"easeOutQuad",swing:function(e,a,c,b,d){return jQuery.easing[jQuery.easing.def](e,a,c,b,d)},easeInQuad:function(e,a,c,b,d){return b*(a/=d)*a+c},easeOutQuad:function(e,a,c,b,d){return-b*(a/=d)*(a-2)+c},easeInOutQuad:function(e,a,c,b,d){if((a/=d/2)<1)return b/2*a*a+c;return-b/2*(--a*(a-2)-1)+c},easeInCubic:function(e,a,c,b,d){return b*(a/=d)*a*a+c},easeOutCubic:function(e,a,c,b,d){return b*((a=a/d-1)*a*a+1)+c},easeInOutCubic:function(e,a,c,b,d){if((a/=d/2)<1)return b/2*a*a*a+c;return b/2*((a-=2)*a*a+2)+c},easeInQuart:function(e,a,c,b,d){return b*(a/=d)*a*a*a+c},easeOutQuart:function(e,a,c,b,d){return-b*((a=a/d-1)*a*a*a-1)+c},easeInOutQuart:function(e,a,c,b,d){if((a/=d/2)<1)return b/2*a*a*a*a+c;return-b/2*((a-=2)*a*a*a-2)+c},easeInQuint:function(e,a,c,b,d){return b*(a/=d)*a*a*a*a+c},easeOutQuint:function(e,a,c,b,d){return b*((a=a/d-1)*a*a*a*a+1)+c},easeInOutQuint:function(e,a,c,b,d){if((a/=d/2)<1)return b/2*a*a*a*a*a+c;return b/2*((a-=2)*a*a*a*a+2)+c},easeInSine:function(e,
	a,c,b,d){return-b*Math.cos(a/d*(Math.PI/2))+b+c},easeOutSine:function(e,a,c,b,d){return b*Math.sin(a/d*(Math.PI/2))+c},easeInOutSine:function(e,a,c,b,d){return-b/2*(Math.cos(Math.PI*a/d)-1)+c},easeInExpo:function(e,a,c,b,d){return a==0?c:b*Math.pow(2,10*(a/d-1))+c},easeOutExpo:function(e,a,c,b,d){return a==d?c+b:b*(-Math.pow(2,-10*a/d)+1)+c},easeInOutExpo:function(e,a,c,b,d){if(a==0)return c;if(a==d)return c+b;if((a/=d/2)<1)return b/2*Math.pow(2,10*(a-1))+c;return b/2*(-Math.pow(2,-10*--a)+2)+c},
	easeInCirc:function(e,a,c,b,d){return-b*(Math.sqrt(1-(a/=d)*a)-1)+c},easeOutCirc:function(e,a,c,b,d){return b*Math.sqrt(1-(a=a/d-1)*a)+c},easeInOutCirc:function(e,a,c,b,d){if((a/=d/2)<1)return-b/2*(Math.sqrt(1-a*a)-1)+c;return b/2*(Math.sqrt(1-(a-=2)*a)+1)+c},easeInElastic:function(e,a,c,b,d){e=1.70158;var f=0,g=b;if(a==0)return c;if((a/=d)==1)return c+b;f||(f=d*0.3);if(g<Math.abs(b)){g=b;e=f/4}else e=f/(2*Math.PI)*Math.asin(b/g);return-(g*Math.pow(2,10*(a-=1))*Math.sin((a*d-e)*2*Math.PI/f))+c},easeOutElastic:function(e,
	a,c,b,d){e=1.70158;var f=0,g=b;if(a==0)return c;if((a/=d)==1)return c+b;f||(f=d*0.3);if(g<Math.abs(b)){g=b;e=f/4}else e=f/(2*Math.PI)*Math.asin(b/g);return g*Math.pow(2,-10*a)*Math.sin((a*d-e)*2*Math.PI/f)+b+c},easeInOutElastic:function(e,a,c,b,d){e=1.70158;var f=0,g=b;if(a==0)return c;if((a/=d/2)==2)return c+b;f||(f=d*0.3*1.5);if(g<Math.abs(b)){g=b;e=f/4}else e=f/(2*Math.PI)*Math.asin(b/g);if(a<1)return-0.5*g*Math.pow(2,10*(a-=1))*Math.sin((a*d-e)*2*Math.PI/f)+c;return g*Math.pow(2,-10*(a-=1))*Math.sin((a*
	d-e)*2*Math.PI/f)*0.5+b+c},easeInBack:function(e,a,c,b,d,f){if(f==undefined)f=1.70158;return b*(a/=d)*a*((f+1)*a-f)+c},easeOutBack:function(e,a,c,b,d,f){if(f==undefined)f=1.70158;return b*((a=a/d-1)*a*((f+1)*a+f)+1)+c},easeInOutBack:function(e,a,c,b,d,f){if(f==undefined)f=1.70158;if((a/=d/2)<1)return b/2*a*a*(((f*=1.525)+1)*a-f)+c;return b/2*((a-=2)*a*(((f*=1.525)+1)*a+f)+2)+c},easeInBounce:function(e,a,c,b,d){return b-jQuery.easing.easeOutBounce(e,d-a,0,b,d)+c},easeOutBounce:function(e,a,c,b,d){return(a/=
	d)<1/2.75?b*7.5625*a*a+c:a<2/2.75?b*(7.5625*(a-=1.5/2.75)*a+0.75)+c:a<2.5/2.75?b*(7.5625*(a-=2.25/2.75)*a+0.9375)+c:b*(7.5625*(a-=2.625/2.75)*a+0.984375)+c},easeInOutBounce:function(e,a,c,b,d){if(a<d/2)return jQuery.easing.easeInBounce(e,a*2,0,b,d)*0.5+c;return jQuery.easing.easeOutBounce(e,a*2-d,0,b,d)*0.5+b*0.5+c}});
		}

		if(!jQuery.mousewheel)
		{
			(function(d){var b=["DOMMouseScroll","mousewheel"];if(d.event.fixHooks){for(var a=b.length;a;){d.event.fixHooks[b[--a]]=d.event.mouseHooks}}d.event.special.mousewheel={setup:function(){if(this.addEventListener){for(var e=b.length;e;){this.addEventListener(b[--e],c,false)}}else{this.onmousewheel=c}},teardown:function(){if(this.removeEventListener){for(var e=b.length;e;){this.removeEventListener(b[--e],c,false)}}else{this.onmousewheel=null}}};d.fn.extend({mousewheel:function(e){return e?this.bind("mousewheel",e):this.trigger("mousewheel")},unmousewheel:function(e){return this.unbind("mousewheel",e)}});function c(j){var h=j||window.event,g=[].slice.call(arguments,1),k=0,i=true,f=0,e=0;j=d.event.fix(h);j.type="mousewheel";if(h.wheelDelta){k=h.wheelDelta/120}if(h.detail){k=-h.detail/3}e=k;if(h.axis!==undefined&&h.axis===h.HORIZONTAL_AXIS){e=0;f=-1*k}if(h.wheelDeltaY!==undefined){e=h.wheelDeltaY/120}if(h.wheelDeltaX!==undefined){f=-1*h.wheelDeltaX/120}g.unshift(j,k,f,e);return(d.event.dispatch||d.event.handle).apply(this,g)}})(jQuery);
		}

		if ( ! isie || iev > 8)
		{
			if (!jQuery.swipe)
			{
				(function(a){a.fn.swipe=function(b){var c={threshold:{x:30,y:10},swipeLeft:function(){},swipeRight:function(){}};var b=a.extend(c,b);if(!this){return false}return this.each(function(){var h=a(this);var f={x:0,y:0};var d={x:0,y:0};function e(k){f.x=k.targetTouches[0].pageX;f.y=k.targetTouches[0].pageY}function j(k){k.preventDefault();d.x=k.targetTouches[0].pageX;d.y=k.targetTouches[0].pageY}function g(k){var l=f.y-d.y;if(l<c.threshold.y&&l>(c.threshold.y*-1)){changeX=f.x-d.x;if(changeX>c.threshold.x){c.swipeLeft()}if(changeX<(c.threshold.x*-1)){c.swipeRight()}}}function e(k){f.x=k.targetTouches[0].pageX;f.y=k.targetTouches[0].pageY;d.x=f.x;d.y=f.y}function i(k){}this.addEventListener("touchstart",e,false);this.addEventListener("touchmove",j,false);this.addEventListener("touchend",g,false);this.addEventListener("touchcancel",i,false)})}})(jQuery);
			}
		}

		window.egs = {};
		egs.elem_cfg = {};
		egs.prefix = 'ether';
		egs.window_ref = $(window);

		egs.add_prefix = function (string)
		{
			if (egs.prefix && egs.prefix !== '')
			{
				return egs.prefix + '-' + string;
			} else
			{
				return string;
			}
		}


		egs.log = function (msg)
		{
			! egs.msg ? egs.msg = [msg] : egs.msg.push(msg);
			! $('body').find('#egs-log').length ? $('body').append('<div id="egs-log" style="font-size: 8pt; position: absolute; top: 0; left: 0; right: 0; background: rgb(0,0,0); z-index: 999999999"></div>') : '';
			$('body').find('#egs-log').append('<p style="margin: 0; padding: 2px 10px; font-size: 9pt; color: #fff;">' + egs.msg.length + ': ' + msg + '</p>');
			$('body').find('#egs-log').children().length > 10 ? $('body').find('#egs-log').children().eq(0).remove() : '';
		}

		egs.in_view = function($elem)
		{
			var e_t = $elem.offset().top;
			var e_h = $elem.outerHeight();
			var w_t = $(window).scrollTop();
			var w_h = $(window).height();

			if ((e_t + e_h < w_t + w_h && e_t + e_h > w_t) || (e_t < w_t + w_h && e_t > w_t))
			{
				return true;
			} else
			{
				return false;
			}
		}

		egs.set_slider_window_height = function ($elem, cfg)
		{
			if (cfg.slider)
			{
				cfg.$slider_window
					.stop(true, true)
					.animate({height: cfg.col_group_elems_height[cfg.view_pos]}, cfg.scroll_speed)
					.queue(function ()
					{
						if (! cfg.shift_busy || cfg.shift_busy === 0)
						{
							$(this).css({overflow: 'visible'});
						}
						$(this).dequeue();
					});
			//////egs.log('set_slider_window_height');
			//alert('fasd')
			}
		}

		egs.apply_shift = function ($elem, cfg)
		{
			var scroll_axis = ['x', 'y', 'z'];
			var transition = ['slide', 'slideIn', 'slideOut', 'switch', 'swap'];
			var random_axis;
			var random_transition;
			var x1 = 0;
			var y1 = 0;
			var x2 = 0;
			var y2 = 0;
			var invert1 = 1;
			var invert2 = 1;

			if (cfg.transition === 'random')
			{
				random_transition = true;
				cfg.transition = transition[Math.ceil(Math.random() * transition.length - 1)];
			}

			if (cfg.scroll_axis === 'random')
			{
				random_axis = true;
				cfg.scroll_axis = scroll_axis[Math.ceil(Math.random() * scroll_axis.length - 1)];
			}

			if (cfg.scroll_axis === 'x')
			{
				x1 = 1;
				x2 = 1;
			} else if (cfg.scroll_axis === 'y')
			{
				y1 = 1;
				y2 = 1;
			}

			if (cfg.transition === 'slideIn')
			{
				x2 = 0;
				y2 = 0;
			} else if (cfg.transition === 'slideOut')
			{
				x1 = 0;
				y1 = 0;
			} else if (cfg.transition === 'switch' || cfg.transition === 'swap' || cfg.transition === 'shuffle')
			{
				invert2 = -1;
			}

			if (random_transition === true)
			{
				cfg.transition = 'random';
			}

			if (random_axis === true)
			{
				cfg.scroll_axis = 'random';
			}

			cfg.$col_group_elems
				.eq(cfg.view_pos)
					.css({ left: cfg.shift_dir * cfg.elem_width * x1 * invert1, top: cfg.shift_dir * cfg.col_group_elems_height[cfg.view_pos] * y1 * invert1, visibility: 'visible', 'z-index': 10, opacity: 0 })
					.animate({ left: 0, top: 0, opacity: 1}, cfg.scroll_speed, cfg.easing )
					.end()
				.eq(cfg.prev_view_pos)
					.css({ left: 0, top: 0 })
					.animate({ left: -cfg.shift_dir * cfg.elem_width * x2 * invert2, top: -cfg.shift_dir * cfg.col_group_elems_height[cfg.view_pos] * y2 * invert2, opacity: 0 }, cfg.scroll_speed, cfg.easing, function ()
					{
						$(this).css({ visibility: 'hidden', 'z-index': -1 })
					});

			egs.set_slider_window_height($elem, cfg);

			var t = setTimeout(function ()
			{
				cfg.$slider_window.css({overflow: 'visible'});
				cfg.shift_busy = 0;
			}, cfg.scroll_speed);
		}

		egs.init_shift = function ($elem, cfg, shift_type, shift_dest)
		{
			if (shift_type === 'absolute' && shift_dest === cfg.view_pos)
			{
				return false;
			}

			if (cfg.col_group_elems_count === 1)
			{
				return false;
			}

			if (cfg.shift_busy !== 1)
			{
				cfg.shift_dir = function ()
				{
					if (shift_type === 'absolute')
					{
						if (shift_dest > cfg.view_pos)
						{
							return 1;
						} else if (shift_dest < cfg.view_pos)
						{
							return -1;
						} else
						{
							return 0;
						}
					} else if (shift_type === 'relative')
					{
						return shift_dest;
					}
				}();

				cfg.prev_view_pos = cfg.view_pos;

				cfg.view_pos = function ()
				{
					if (shift_type === 'relative')
					{
						if (cfg.loop === true)
						{
							if (cfg.view_pos + cfg.shift_dir < 0)
							{
								return cfg.col_group_elems_count - 1;
							} else if (cfg.view_pos + cfg.shift_dir > cfg.col_group_elems_count - 1)
							{
								return 0;
							} else
							{
								return cfg.view_pos + cfg.shift_dir;
							}
						} else if (cfg.loop === false)
						{
							if (cfg.view_pos + cfg.shift_dir < 0)
							{
								return -2;
							} else if (cfg.view_pos + cfg.shift_dir > cfg.col_group_elems_count - 1)
							{
								return -2;
							} else
							{
								return cfg.view_pos + cfg.shift_dir;
							}
						}
					} else if (shift_type === 'absolute')
					{
						return shift_dest;
					}
				}();

				if (cfg.view_pos !== -2)
				{
					if (cfg.ctrl_pag === true)
					{
						cfg.$ctrl_pag_elem
							.children()
								.removeClass(egs.add_prefix('current'))
								.eq(cfg.view_pos)
									.addClass(egs.add_prefix('current'));
					}
					cfg.shift_busy = 1;
					egs.apply_shift($elem, cfg);
					egs.resume_autoplay($elem, cfg);
				}
				else {
					cfg.view_pos = cfg.prev_view_pos;
				}
			}
		}

		egs.set_grid_rows = function ($elem, cfg)
		{
			//console.log('set rows');
			var a;
			var first_col_class = egs.add_prefix('first-col');

			//reassign first col for every row
			cfg.$col_elems.removeClass(first_col_class);

			if (cfg.grid_height === 'auto' )
			{
				for (a = 0; a < cfg.col_elem_count; a += 1)
				{
					if (a % cfg.true_cols === 0)
					{
						cfg.$col_elems.eq(a).addClass(first_col_class);
					}
				}
			}

			cfg.$col_elems.each(function ()
			{
				var $media = $(this).find('*[class*="' + egs.add_prefix('media-') + '"]');
				var $media_img = $media.find('img');

				if (cfg.grid_height === 'constrain')
				{
					$(this).css({
						height: cfg.col_elem_width * cfg.grid_height_ratio/100,
						overflow: 'hidden'
					});
				} else if (typeof cfg.grid_height === 'number')
				{
					$(this).css({
						height: cfg.grid_height,
						overflow: 'hidden'
					});
				}

				//gridslider supports build in media-x behaviour even though media-x isn't really a part of it anymore
				if ($media.length > 0)
				{
					if ($media_img.length > 0)
					{
						egs.on_image_load_end($media_img, function ()
						{
							//alert('wrap wrap: ' + $media.parent().width() + ' img wrap: ' + $media.get(0).tagName + ' ' + $media.width() + ' ' + $media.attr('width') + ' img: ' + $media_img.get(0).tagName + ' ' + $media_img.width() + ' ' + $media_img.attr('width'));
							egs.init_media ($media_img, cfg.image_stretch_mode, $media, cfg.media_height, cfg.media_height_ratio)
						});
					} else
					{
						//egs.adjust_image_size_and_pos($media_img, $media, cfg.image_stretch_mode);
					}
				}

			});
		}

		egs.init_media = function ($img, img_stretch_mode, $media, media_height, media_height_ratio)
		{
			$img.attr('style', ''); //read on the style attr notes below

			if (media_height !== 'auto')
			{
				if (media_height === 'constrain')
				{
					$media.height($media.width() * media_height_ratio / 100);
				} else if (typeof parseInt(media_height) === 'number' && media_height / media_height === 1)
				{
					$media.height(media_height);
				}
			} else
			{
				if ($media.height() > $media.parent().height())
				{
					$media.height($media.parent().height());
				}
			}

			var h = $media.height();
			var w = $media.width();
			var img_w = $img.width();
			var img_h = $img.height();
			var img_ratio;
			var parent_ratio;
			var ratio;

			/*
				NOTE:
					-style attribute may not be the cleanest way here
					-we need to have these attributes passed and native jQuery does not support !important
					-this way style attribute is exclusively taken for purpouses of proper image alignment.
					-since this widget is pretty hermetic it shouldn't be a problem as long as there are no internal conflits
			*/
			switch (img_stretch_mode)
			{
				case 'x':
				{
					if (img_h > h)
					{
						$img.attr('style', 'margin-top: ' + (-(img_h - h) / 2) + 'px !important');
					} else
					{
						$img.attr('style', 'margin-top: ' + ((h - img_h) / 2) + 'px !important');
					}

					break;
				}
				case 'y':
				{
					if (img_w > w)
					{
						$img.attr('style', 'margin-left: ' + (-(img_w - w) / 2) + 'px !important');
					}

					break;
				}
				case 'fill':
				{
					img_ratio = img_w / img_h;
					parent_ratio = w / h;
					ratio = parent_ratio / img_ratio;

					if (ratio >= 1)
					{
						////console.log('>=1')
						$img.attr('style', 'width: ' + w + 'px; ' +  'height: ' + (w / img_ratio) + 'px; ' + 'margin-top: ' + (-(w / img_ratio - h) / 2) + 'px !important;');
					} else
					{
						////console.log('<1')
						$img.attr('style', 'width: ' + (h * img_ratio) + 'px; ' + 'height: ' + h + 'px; ' + 'margin-left: ' + (-(h * img_ratio - w) / 2) + 'px !important;');
					}

					break;
				}
				case 'fit':
				{
					$img.attr('style', 'margin-top: ' + ((h - img_h) / 2) + 'px !important;');
				}
			}
		}

		egs.init_slider_functions = function ($elem, cfg)
		{
			if (cfg.col_group_elems_count > 1)
			{
				egs.init_autoplay($elem, cfg);

				if ( ! isie || iev > 8)
				{
					$elem
						.swipe({
						     swipeLeft: function()
						     {
						     	egs.init_shift($elem, cfg, 'relative', 1);
						     },
						     swipeRight: function() {
						     	egs.init_shift($elem, cfg, 'relative', -1);
						     }
						});
				}

				$elem
					.bind('mousewheel', function (event, delta, deltaX, deltaY)
					{
						if (cfg.scroll_on_mousewheel)
						{
							var shiftdest = -1;

							if (deltaY !== 0 && deltaY < 0 || deltaX !== 0 && deltaX > 0)
							{
								shiftdest = 1;
							}

							egs.init_shift($elem, cfg, 'relative', shiftdest);
							//egs.resume_autoplay($elem, cfg);
							event.preventDefault();
						}
					})
					.bind('mouseenter', function ()
					{
						//var
						//	scroll_x = $(window).scrollLeft(),
						//	scroll_y = $(window).scrollTop();
						egs.pause_autoplay($elem, cfg);
					})
					.bind('mouseleave', function ()
					{
						egs.resume_autoplay($elem, cfg);
					});

				if (cfg.$ctrl_wrap)
				{
					cfg.$ctrl_wrap
						.css({
							'margin-left': function()
							{
								return -$(this).outerWidth()/2;
							},
							'margin-top': function ()
							{
								return -$(this).outerHeight()/2 - cfg.col_spacing_size / 2 * cfg.col_spacing_enable;
							}
						})
						.find('.' + egs.add_prefix('ctrl'))
							.attr('unselectable', 'on')
							.css({
								'-ms-user-select':'none',
								'-moz-user-select':'none',
								'-webkit-user-select':'none',
								'user-select':'none'
							})
							.bind('click', function()
							{
								this.onselectstart = function()
								{
									return false;
								}

								egs.init_shift($elem, cfg, $(this).data('shifttype'), $(this).data('shiftdest'));
								//egs.resume_autoplay($elem, cfg);

								return false;
							});
				}

				if (cfg.ctrl_external.length > 0)
				{
					cfg.ctrl_external.forEach(function (elem)
					{
						var $elem = elem[0]
						var destination = elem[1];
						var shifttype = typeof destination === 'number' ? 'absolute' : 'relative';
						$elem
							.attr('data-shifttype', shifttype)
							.attr('data-shiftdest', destination === 'prev' ? '-1' : '1')
							.bind('click', function (e)
							{
								if ($(this).data('shiftdest') <= cfg.col_group_elems_count)
								{
									egs.init_shift($elem, cfg, $(this).data('shifttype'), $(this).data('shiftdest'));
									//egs.resume_autoplay($elem, cfg);

									e.preventDefault();
								}
							});
					})
				}
			}
		}

		egs.set_col_groups = function ($elem, cfg)
		{
			if (cfg.slider)
			{
				var a;
				var col_group_class = egs.add_prefix('col-group');

				//egs.on_images_load_end($elem, cfg);

				if (cfg.$col_group_elems && cfg.$col_group_elems.length > 0)
				{
					//console.log(cfg.$col_group_elems)
					//console.log(cfg.$col_group_elems.length);
					//console.log('should not  happen i fslider 0')
					cfg.$col_elems.unwrap();
				}

				for (a = 0; a < cfg.col_elem_count; a += cfg.col_group_elems_capacity)
				{
					$('<div class="' + col_group_class + '"></div>')
						.appendTo(cfg.$col_elems_wrap)
						.append(function ()
						{
							if(a + cfg.col_group_elems_capacity < cfg.col_elem_count)
							{
								return cfg.$col_elems_wrap.children().slice(0, cfg.col_group_elems_capacity);
							} else
							{
								return cfg.$col_elems_wrap.children().slice(0, cfg.col_elem_count - a);
							}
						});
				}

				cfg.$col_group_elems = $elem.find('.' + col_group_class);
				cfg.$col_group_elems
					.eq(cfg.view_pos)
						.css({'z-index': 1, visibility: 'visible'});

				//egs.on_images_load_end($elem, cfg, function ()
				//{
					cfg.col_group_elems_height = [];
					cfg.$col_group_elems.each(function (id)
					{
						cfg.col_group_elems_height.push(cfg.$col_group_elems.eq(id).outerHeight() - cfg.col_spacing_size * cfg.col_spacing_enable);
					});
				//});
			//////egs.log('set_col_groups');
			}
		};

		egs.update_slider_ctrl = function ($elem, cfg)
		{
			if (cfg.slider && cfg.ctrl_active)
			{
				if (cfg.ctrl_pag === true)
				{
					cfg.$ctrl_pag_elem
						.children()
							.eq(cfg.view_pos).addClass(egs.add_prefix('current'))
							.end()
							.css({display: 'block'})
							.slice(cfg.col_group_elems_count)
								.css({display: 'none'})
							.end()
						.end()
						.css({
							width: function ()
							{
								return cfg.col_group_elems_count * $(this).children().outerWidth(true);
							}
						});
				}

				if (cfg.ctrl_arrows === true && cfg.ctrl_pag === true)
				{
					cfg.$ctrl_wrap
						.css({
							width: function ()
							{
								if (cfg.ctrl_pag && cfg.ctrl_arrows && cfg.$ctrl_pag_elem.outerWidth() > cfg.$ctrl_car_elem.outerWidth()) {
									return cfg.$ctrl_pag_elem.outerWidth();
								}
								else {
									return cfg.$ctrl_car_elem.outerWidth();
								}
							},
							'margin-left': function()
							{
								return -$(this).outerWidth()/2;
							}
						});
				} else
				{
						cfg.$ctrl_wrap.css({ width: cfg.ctrl_car_elem_width });
				}
			}
	//////egs.log('update_slider_ctrl');
		}

		egs.init_slider_ctrl = function ($elem, cfg)
		{
			if (cfg.ctrl_arrows === true || cfg.ctrl_pag === true)
			{
				cfg.ctrl_active = true;
			} else
			{
				return false;
			}

			var ctrl_wrap_class = egs.add_prefix('ctrl-wrap');
			var ctrl_class = egs.add_prefix('ctrl');
			var ctrl_pag_class = egs.add_prefix('ctrl-pag');
			var ctrl_car_class = egs.add_prefix('ctrl-car');
			var ctrl_next_class = egs.add_prefix('next');
			var ctrl_prev_class = egs.add_prefix('prev');

			$('<div class="' + ctrl_wrap_class + '"></div>')
				.appendTo($elem)
				//.addClass(egs.addcfg.ctrl_pos)
			cfg.$ctrl_wrap = $elem.find('.' + ctrl_wrap_class);

			if (cfg.ctrl_always_visible === false)
			{
				cfg.$ctrl_wrap.hide();

				$elem
					.bind('mouseenter', function ()
					{
						cfg.$ctrl_wrap.stop(true, true).fadeIn(500);
					})
					.bind('mouseleave', function ()
					{
						cfg.$ctrl_wrap.stop(true, true).delay(500).fadeOut(500);
					});
			}

			if (cfg.ctrl_arrows === true) {
				cfg.$ctrl_wrap
					.append('<div class="' + ctrl_car_class + '"><div class="' + ctrl_class + ' ' + ctrl_prev_class + '" data-shifttype="relative" data-shiftdest="-1"></div><div class="' + ctrl_next_class + ' ' + ctrl_class + '" data-shifttype="relative" data-shiftdest="1"></div></div>');
				cfg.$ctrl_car_elem = cfg.$ctrl_wrap.find('.' + ctrl_car_class);
				cfg.ctrl_car_elem_width = cfg.$ctrl_car_elem.outerWidth();
			}

			if (cfg.ctrl_pag === true)
			{
				cfg.$ctrl_wrap
					.append(function ()
					{
						var a;
						var result = '';

						result += '<div class="' + ctrl_pag_class + '">';
						for (a = 0; a < cfg.col_elem_count; a += 1)
						{
							result += '<div class="' + ctrl_class + '" data-shifttype="absolute" data-shiftdest="' + a + '"></div>';
						}
						result += '</div>';

						return result;
					});

				cfg.$ctrl_pag_elem = cfg.$ctrl_wrap.find('.' + ctrl_pag_class);
				cfg.ctrl_pag_elem_width = cfg.$ctrl_pag_elem.outerWidth();
			}

			egs.update_slider_ctrl($elem, cfg);
		}

		egs.init_load_overlay = function ($elem, cfg)
		{
		}

		egs.init_slider_structure = function ($elem, cfg)
		{
			var slider_class;
			var slider_window_class;
			var load_overlay_class;

			if (cfg.slider === true)
			{
				//console.log('init_slider');
				slider_class = egs.add_prefix('slider');
				slider_window_class = egs.add_prefix('slider-window');
				load_overlay_class = egs.add_prefix('load-overlay');

				$elem
					.addClass(slider_class)
						.children()
							.wrapAll('<div class="' + slider_window_class + '"></div>');

				cfg.$slider_window = $elem.find('.' + slider_window_class);
				cfg.$slider_window
					.css({height: 20, overflow: 'hidden'})
					.append(function ()
					{
						if ($(this).find('.' + load_overlay_class).length === 0)
						{
							return '<div class="' + load_overlay_class + '"></div>';
						}
					});

				cfg.$slider_window.find(load_overlay_class).show();

				egs.on_images_load_end($elem, cfg, function ()
				{
					egs.set_col_groups($elem, cfg);

					cfg.$slider_window
						.css({overflow: 'hidden' })
						.children('.' + load_overlay_class).delay(cfg.scroll_speed).fadeOut(1000).end()
						.queue(function ()
						{
							$(this)
								.find('.' + load_overlay_class).remove()
								.css({'overflow': 'visible'})
								.dequeue();
						});

					egs.set_slider_window_height($elem, cfg);
				});

				egs.init_slider_ctrl($elem, cfg);
				egs.init_slider_functions($elem, cfg);
			}
		}

		egs.get_grid_data = function ($elem, cfg)
		{
			cfg.elem_width = $elem.outerWidth();
			cfg.$col_elems_wrap = $elem.find('.' + egs.add_prefix('cols')).eq(0);
			cfg.$col_elems = function ()
			{
				if( ! cfg.$col_elems_wrap.children('.' + egs.add_prefix('col')).length)
				{
					return cfg.$col_elems_wrap.children().children('.' + egs.add_prefix('col'));
				} else
				{
					return cfg.$col_elems_wrap.children()
				}
			}();
			cfg.col_elem_count = cfg.$col_elems.length;
			cfg.col_elem_width = cfg.$col_elems.outerWidth();
			cfg.img_count = $elem.find('img').length;
			cfg.true_cols = Math.round(cfg.elem_width / cfg.col_elem_width);

			if ($(window).width() < 580)
			{
				if(!cfg.original_rows)
				{
					cfg.original_rows = cfg.rows;
				}
				cfg.rows = 1;
			}

			if ($(window).width() > 580 && typeof cfg.original_rows === 'number' && cfg.rows !== cfg.original_rows)
			{
				cfg.rows = cfg.original_rows;
			}

			cfg.col_group_elems_capacity = cfg.rows * cfg.true_cols;
			cfg.col_group_elems_count = Math.ceil(cfg.col_elem_count/cfg.col_group_elems_capacity);

			if (cfg.view_pos >= cfg.col_group_elems_count)
			{
				cfg.view_pos = 0;
			}

			//////egs.log('get_grid_data');

			//egs.log('col_elems_wrap: ' + cfg.$col_elems_wrap.length + ' col_elems: ' + cfg.$col_elems.length + ' col_elem_count: ' + cfg.col_elem_count + ' col_elem_width: ' + cfg.col_elem_width + ' true_cols : ' + cfg.true_cols);
		}

		egs.generate_rules = function (obj)
		{
			var result = '';

			for(var selector in obj)
			{
				result += selector + ' { \n';
				for(var prop in obj[selector])
				{
					result += '	' + prop + ': ' + obj[selector][prop] + '; \n';
				}
				result += '} \n';
			}

			return result;
		}

		egs.generate_stylesheet_content = function ($elem, cfg)
		{
			var result = '';
			var styles = {};
			var ie7_styles = {};
			var grid_class = egs.add_prefix('grid');
			var cols_class = egs.add_prefix('cols');
			var col_class = egs.add_prefix('col');
			var ie7_grid_fix_class = egs.add_prefix('col');

			var make_selector = function ()
			{
				return 'asdf';
			}

			if (cfg.col_spacing_size !== 30 && cfg.col_spacing_enable !== 0)
			{
				styles['.' + grid_class + cfg.elem_selector + ' .' + cols_class] =
				{
					margin: -(cfg.col_spacing_size/2) + 'px'
				};
				styles['.' + grid_class + cfg.elem_selector  + ' .' +  cols_class + ' .' + col_class] =
				{
					padding: (cfg.col_spacing_size/2) + 'px'
				};

				result += egs.generate_rules(styles);

				if (isie && parseInt(iev) == 7)
				{
					ie7_styles['.' + ie7_grid_fix_class + '.cols-wrap'] =
					{
						margin: -(cfg.col_spacing_size/2) + 'px !important'
					},
					ie7_styles['.' + ie7_grid_fix_class + '.cols-wrap .' + col_class + ' > *:first-child'] =
					{
						padding: (cfg.col_spacing_size/2) + 'px !important'
					}

					result += egs.generate_rules(ie7_styles);
				}
			}
			if (cfg.width !== 'auto')
			{
				if (typeof cfg.width === 'number')
				{
					cfg.width += 'px';
				}
				result += cfg.elem_selector + ' { width: ' + cfg.width + '; }';
			}

			return result;
		}

		egs.init_gallery_title = function ($elem, cfg)
		{
			var $img = $elem.find('img');
			var img_class = egs.add_prefix('img-title');

			$img.each(function ()
			{
				if ($(this).siblings('.' + img_class).length === 0)
				{
					var title = $(this).attr('title');
					var alt = $(this).attr('alt');
					var result = '';

					if (title !== undefined)
					{
						result = title;
					} else if (alt !== undefined)
					{
						result = alt;
					}

					if (result !== '')
					{
						$('<span class="' + img_class + '">' + result + '</span>')
							.appendTo($(this).parent())

						var $title = $(this).siblings('.' + img_class);
						var title_h = $title.outerHeight();

						$title
							.css({opacity: 0, 'bottom': -title_h})

						$(this).parent().on('mouseenter', function ()
						{
							$title
								.stop(true, true).animate({opacity: 1, bottom: 0}, 500);
						})
						.bind('mouseleave', function ()
						{
							$title .delay(250).animate({opacity: 0, bottom: -title_h}, 500);
						})
					}
				}
			});
		}

		egs.on_images_load_end = function ($elem, cfg, callback)
		{
			if (cfg.img_count > 0)
			{
				if (cfg.all_images_loaded !== true)
				{
					var loaded = 0;
					var locked = 0;
					var broken = 0;
					var $img = $elem.find('img');
					var img_count = $img.length;

					$img.each(function (id)
					{
						$(this)
							.bind('load', function ()
							{
								loaded += 1;
								//what was the (loaded !== img_count && id === img_count - 1 && locked === 0) thing for?
								//if ((loaded === img_count && locked === 0) || (loaded !== img_count && id === img_count - 1 && locked === 0))
								if ((loaded === img_count && locked === 0))
								{
									locked = 1;
									cfg.all_images_loaded = true;

									callback ? callback($elem, cfg) : '';

									//console.log('all ' + loaded + ' of ' + cfg.img_count + ' detected images have loaded.' + (broken ? '(' + broken + ') links seem to be broken ;(' : ''));
								}
							})
							.bind('error', function ()
							{
								broken += 1;
								$(this).unbind('error').attr('src', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAMAAAAoyzS7AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjQwNkI5RDRFNjFBQzExRTE5MjJDRjRGMUM2MTdDODUyIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjQwNkI5RDRGNjFBQzExRTE5MjJDRjRGMUM2MTdDODUyIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6NDA2QjlENEM2MUFDMTFFMTkyMkNGNEYxQzYxN0M4NTIiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6NDA2QjlENEQ2MUFDMTFFMTkyMkNGNEYxQzYxN0M4NTIiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz4G1oNaAAAABlBMVEX///8AAABVwtN+AAAADElEQVR42mJgAAgwAAACAAFPbVnhAAAAAElFTkSuQmCC');
							});

						if ((typeof this.complete != 'undefined' && this.complete) || (typeof this.naturalWidth != 'undefined' && this.naturalWidth > 0))
						{
							$(this)
								.trigger('load')
								.unbind('load');
						}
					});
				}  else
				{
					callback ? callback($elem, cfg) : '';
				}
			} else
			{
				callback ? callback($elem, cfg) : '';
			}
		}

		egs.on_image_load_end = function ($img, callback)
		{
			$img.each(function ()
			{
				$(this).bind('load', function ()
				{
					callback();
				}).bind('error', function ()
				{
					 $(this)
					 	.unbind('error')
					 	.attr('src', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAMAAAAoyzS7AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjQwNkI5RDRFNjFBQzExRTE5MjJDRjRGMUM2MTdDODUyIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjQwNkI5RDRGNjFBQzExRTE5MjJDRjRGMUM2MTdDODUyIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6NDA2QjlENEM2MUFDMTFFMTkyMkNGNEYxQzYxN0M4NTIiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6NDA2QjlENEQ2MUFDMTFFMTkyMkNGNEYxQzYxN0M4NTIiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz4G1oNaAAAABlBMVEX///8AAABVwtN+AAAADElEQVR42mJgAAgwAAACAAFPbVnhAAAAAElFTkSuQmCC');
				});

				if ((typeof this.complete != 'undefined' && this.complete) || (typeof this.naturalWidth != 'undefined' && this.naturalWidth > 0))
				{
					$(this)
						.trigger('load')
						.unbind('load');
				}
			});
		}

		egs.update_elem = function ($elem, cfg)
		{
			egs.get_grid_data($elem, cfg);
			egs.set_grid_rows($elem, cfg);

			if (cfg.gallery_img_title === true)
			{
				egs.init_gallery_title($elem, cfg);
			}

			if (cfg.slider === true)
			{
				egs.set_col_groups($elem, cfg);
				egs.set_slider_window_height($elem, cfg);
				egs.update_slider_ctrl($elem, cfg);
			}
			//////egs.log('update');
		}

		egs.add_elements = function ($gridslider_elem, $target_elem, remove_target, callback)
		{
			var re = new RegExp(egs.prefix + '-id-(\\d+)');

			$gridslider_elem.each(function ()
			{
				var cfg = egs.elem_cfg[$(this).attr('class').match(re)[1]];

				$target_elem.each(function ()
				{
					var $col_elem = $('<div class="' + egs.add_prefix('col') +'"></div>').append($(this).clone(true, true));

					if (cfg.slider === true)
					{
						if (cfg.$col_group_elems.eq(-1).children().length < cfg.col_group_elems_capacity)
						{
							$col_elem
								.appendTo(cfg.$col_group_elems.eq(-1))
								.hide()
								.fadeIn(500);
						} else
						{
							$('<div class="' + egs.add_prefix('col-group') + '"></div>')
								.appendTo(cfg.$col_elems_wrap)
							$col_elem
								.appendTo(cfg.$col_elems_wrap.children().eq(-1))
								.hide()
								.fadeIn(500)
						}
					} else
					{
						$col_elem
							.appendTo(cfg.$col_elems_wrap)
							.hide()
							.fadeIn(500);
					}
				});

				egs.update_elem(cfg.$elem, cfg);

				if (callback)
				{
					callback();
				}
			});

			if (remove_target === true)
			{
				$target_elem.remove();
			}
		}

		egs.remove_elements = function ($gridslider_elem, targets_id, callback)
		{
			var re = new RegExp(egs.prefix + '-id-(\\d+)');

			$gridslider_elem.each(function ()
			{
				var cfg = egs.elem_cfg[$(this).attr('class').match(re)[1]];

				if (typeof targets_id === 'number')
				{
					if (cfg.$col_elems.eq(targets_id).length > 0)
					{
						cfg.$col_elems.eq(targets_id)
							//.fadeOut(500)
							//.queue(function ()
							//{
							//	$(this)
									.remove()
							//		.dequeue();
							//});
					}
				} else
				{
					targets_id.forEach(function (elem)
					{
						if (cfg.$col_elems.eq(targets_id[elem]).length > 0)
						{
							cfg.$col_elems.eq(targets_id[elem])
								//.fadeOut(500)
								//.queue(function ()
								//{
								//	$(this)
										.remove()
								//		.dequeue();
								//});
						}
					});
				}

				if (cfg.$col_group_elems.eq(-1).children().length === 0)
				{
					cfg.$col_group_elems.eq(-1).remove();
				}

				egs.update_elem($(this), cfg);

				if (callback)
				{
					callback();
				}
			});
		}

		egs.pause_autoplay = function ($elem, cfg)
		{
			cfg.autoplay_active = 0;
			clearTimeout(cfg.autoplay_stamp);
		}

		egs.resume_autoplay = function ($elem, cfg)
		{
			clearTimeout(cfg.autoplay_stamp);
			if (cfg.autoplay_enable === true && cfg.force_autoplay_pause !== 1)
			{
				cfg.autoplay_active = 1;
				cfg.autoplay_stamp = setTimeout(function ()
				{
					egs.init_shift($elem, cfg, 'relative', cfg.autoplay_shift_dir);
				}, cfg.autoplay_interval * 1000);
			}
		}

		egs.init_autoplay = function ($elem, cfg)
		{
			if (cfg.autoplay_enable === true)
			{
				$(window)
					.bind('load', function ()
					{
						if (egs.in_view($elem))
						{
							egs.resume_autoplay($elem, cfg);
						}
					})
					.bind('blur', function ()
					{
						egs.pause_autoplay($elem, cfg);
					})
					.bind('focus', function ()
					{
						egs.resume_autoplay($elem, cfg);
					})
					.bind('scroll', function ()
					{
						if (!egs.in_view($elem))
						{
							if (cfg.autoplay_active === 1 && cfg.autoplay_enable === true)
							{
								egs.pause_autoplay($elem, cfg)
							}
						} else
						{
							if (cfg.autoplay_active !== 1  && cfg.autoplay_enable === true)
							{
								egs.resume_autoplay($elem, cfg)
							}
						}
					});
			} else
			{
				clearTimeout(cfg.autoplay_stamp)
				cfg.autoplay_active = 0;
			}
		}

		egs.class_string_from_arr = function (arr)
		{
			var result = '';

			arr.forEach(function (elem)
			{
				result += egs.add_prefix(elem) + ' ';
			});

			return result;
		}

		egs.init_grid_structure = function ($elem, cfg)
		{
			/*
			var elem_class = [
				'grid',
				'grid-height'
			];
			var cols_class = [
				'cols',
				'cols-' + cfg.cols,
				'rows-' + cfg.rows,
				'spacing-' + (cfg.col_spacing_enable === true ? 1 : 0)
			];
			*/

			var target_elem_classes = function ()
			{
				var arr = [
					egs.add_prefix('grid'),
					egs.add_prefix('scroll-axis-' + cfg.scroll_axis),
					egs.add_prefix('grid-height-' + cfg.grid_height),
					egs.add_prefix('image-stretch-mode-' + cfg.image_stretch_mode),
					egs.add_prefix('align' + cfg.align)
				];

				return arr.join(' ');
			};

			var cols_container_classes = function ()
			{
				var arr = [
					egs.add_prefix('cols'),
					egs.add_prefix('cols-' + cfg.cols),
					egs.add_prefix('rows-' + cfg.rows),
					egs.add_prefix('spacing-' + (cfg.col_spacing_enable === true ? 1 : 0))
				];

				return arr.join(' ');
			}

			if (egs.browser_msie_7)
			{
				$elem.addClass(egs.add_prefix('ie7-grid-fix'));
			}

		 	if ($elem.find('.' + egs.add_prefix('cols')).length === 0)
		 	{
		 		if (cfg.hide_grid_cell_overflow === true)
		 		{
			 		$elem.children().each(function ()
			 		{
			 			if ($(this).prop('tagName') === 'IMG')
			 			{
			 				$(this).wrap('<span class="' + egs.add_prefix('hide-grid-cell-overflow') + '"></span>');

			 			} else if ($(this).children().length === 1 && $(this).children().prop('tagName') === 'IMG' && $(this).css('overflow') !== 'hidden')
	 					{
	 						$(this).css({overflow: 'hidden'});
	 					}
			 		});
			 	}

		 		$elem
		 			.addClass(target_elem_classes())
			 		.children()
			 			.wrapAll('<div class="' + cols_container_classes() + '"></div>')
				 		.wrap('<div class="' + egs.add_prefix('col') + '"></div>');
			}

			if (!egs.css)
			{
				egs.css = '';
			}
			egs.css += egs.generate_stylesheet_content($elem, cfg);

			$(egs.style_destination)
				.find('.' + egs.add_prefix('gridslider-custom-styles')).remove().end()
				.append('<style class="' + egs.add_prefix('gridslider-custom-styles') + '">' + egs.css + '</style>');

			egs.get_grid_data($elem, cfg);
			egs.set_grid_rows($elem, cfg);

			if (cfg.gallery_img_title === true)
			{
				egs.init_gallery_title($elem, cfg);
			}
		}

		egs.assign_gridslider_id = function ()
		{
			var id = Math.round(Math.random()*10000);

			if ( ! egs.elem_cfg[id])
			{
				return id;
			} else
			{
				egs.assign_gridslider_id();
				return false;
			}
		}

		egs.init_gridslider = function ($elem, cfg)
		{
			if ( ! $elem.attr('data-egs'))
			{
				//console.log('start egs initialization');

				$elem.attr('data-egs', true);

				if ( ! egs.style_destination)
				{
					if (isie && iev < 9) {
						egs.style_destination = 'body';
					}
					else {
						egs.style_destination = 'head';
					}
				}

				if (egs.browser_msie_7 === undefined)
				{
					egs.browser_msie_7 = (isie && parseInt(iev) == 7);
				}

				if ( ! cfg.elem_id)
				{
					cfg.elem_id = egs.assign_gridslider_id();
					$elem.addClass(egs.add_prefix('id-' + cfg.elem_id));
				}
				cfg.$elem = $elem;
				if ( ! cfg.defaults)
				{
					cfg.defaults = jQuery.extend({}, true, cfg);
				}
				egs.elem_cfg[cfg.elem_id] = cfg;
				egs.init_grid_structure($elem, cfg);
				egs.init_slider_structure($elem, cfg);

				$(window).bind('resize.egs', function ()
				{
					egs.update_elem($elem, cfg);
				});
				//console.log('end egs initialization');
			} else
			{
				//console.log('there\'s a gridslider for this elem defined already!');
			}
		}

		egs.uninit_gridslider = function (id)
		{
			var cfg = egs.elem_cfg[id];
			var css_dep = [
				'scroll-axis-',
				'grid-height-',
				'image-stretch-mode-',
				'align'
			];

			if (cfg.slider == 0)
			{
				cfg.$col_elems.children().unwrap().unwrap();
			} else if (cfg.slider == 1)
			{
				cfg.$col_elems.children().unwrap().unwrap().unwrap().unwrap();
				if (cfg.$ctrl_wrap && cfg.$ctrl_wrap.length > 0)
				{
					cfg.$ctrl_wrap.remove();
				}
			}

			if (cfg.gallery_img_title == 1)
			{
				cfg.$elem.find('.' + egs.add_prefix('img-title')).remove();
			}

			css_dep.forEach(function (name)
		 	{
		 		var class_prop = cfg.$elem.prop('class');
		 		var re = new RegExp(egs.add_prefix(name + '\\S+\\s*'))
		 		cfg.$elem.prop('class', class_prop.replace(re, ''))
		 	});

		 	cfg.$elem.attr('data-egs', '');

		 	$(window).unbind('resize.egs' + id);
		}

		egs.reinit_gridslider = function (id)
		{
			var $elem = egs.elem_cfg[id].$elem;
			var new_cfg = jQuery.extend({}, true, egs.elem_cfg[id].defaults);

			new_cfg.elem_id = id;

			for (var key in cfg)
			{
				new_cfg[key] = cfg[key];
			}

			egs.uninit_gridslider(id);
		}

	    $.fn.gridSlider = function(options) {

	        var defaults = {
	        	elem_selector: $(this).selector,
	            slider: true,
				cols: 1,
				rows: 1,
				width: 'auto',
				align: 'auto',
				col_spacing_enable: true,
				col_spacing_size: 30,
				ctrl_arrows: true,
				ctrl_pag: true,
				ctrl_external: [],
				ctrl_always_visible: false,
				scroll_axis: 'x',
				transition: 'slide',
				easing: 'swing',
				scroll_speed: 500,
				autoplay_enable: false,
				autoplay_interval: 5,
				autoplay_shift_dir: 1,
				view_pos: 0,
				grid_height: 'auto',
				grid_height_ratio: 100,
				media_height: 'auto',
				media_height_ratio: 100,
				image_stretch_mode: 'auto',
				gallery_img_title: false,
				loop: true,
				hide_grid_cell_overflow: false,
				scroll_on_mousewheel: true
				//ctrl_autoplay_toggle: 0,
				//ctrl_timer: 0,
				//ctrl_position: 'top',
				//ctrl_mode: 'compact',
				//trigger_delay: 0,
				//timeout_delay: 0,
	        }

			var options = $.extend(defaults, options);


			return this.each(function()
			{
				egs.init_gridslider($(this), jQuery.extend(true, {}, options));
			});
	    }
	});//(jQuery);

//gridslider_end


	$(document).ready(function ()
	{
		if (typeof SyntaxHighlighter != 'undefined')
		{
			var sh_base_path = $('script[src*="shCore.js"]').attr('src').split('shCore.js')[0];

			function sh_path(data)
			{
				for (var i = 0; i < data.length; i++)
				{
					data[i] = data[i].replace('@', sh_base_path);
				}

				return data;
			}

			SyntaxHighlighter.autoloader.apply(null, sh_path
			([
				'applescript @shBrushAppleScript.js',
				'actionscript3 as3 @shBrushAS3.js',
				'bash shell @shBrushBash.js',
				'coldfusion cf @shBrushColdFusion.js',
				'cpp c @shBrushCpp.js',
				'c# c-sharp csharp @shBrushCSharp.js',
				'css @shBrushCss.js',
				'delphi pascal @shBrushDelphi.js',
				'diff patch pas @shBrushDiff.js',
				'erl erlang @shBrushErlang.js',
				'groovy @shBrushGroovy.js',
				'java @shBrushJava.js',
				'jfx javafx @shBrushJavaFX.js',
				'js jscript javascript @shBrushJScript.js',
				'perl pl @shBrushPerl.js',
				'php @shBrushPhp.js',
				'text plain @shBrushPlain.js',
				'py python @shBrushPython.js',
				'ruby rails ror rb @shBrushRuby.js',
				'sass scss @shBrushSass.js',
				'scala @shBrushScala.js',
				'sql @shBrushSql.js',
				'vb vbnet @shBrushVb.js',
				'xml xhtml xslt html @shBrushXml.js'
			]));

			SyntaxHighlighter.all();
		}


	    $('.' + egs.add_prefix('grid')).each( function()
		{
			$(this).gridSlider
			({
				slider: $(this).hasClass('ether-slider'),
				cols: $(this).children().cattr(egs.add_prefix('cols')),
				rows: $(this).children().cattr(egs.add_prefix('rows')),
				col_spacing_enable: $(this).children().cattr(egs.add_prefix('spacing')) == 1,
				ctrl_pag: $(this).cattr(egs.add_prefix('ctrl-pag')) == 1,
				ctrl_arrows: $(this).cattr(egs.add_prefix('ctrl-arrows')) == 1,
				autoplay_enable: $(this).cattr(egs.add_prefix('autoplay')) == 1,
				autoplay_interval: $(this).cattr(egs.add_prefix('autoplay-interval')),
				autoplay_shift_dir: $(this).cattr(egs.add_prefix('autoplay-invert')) == 1 ? -1 : 1,
				image_stretch_mode: $(this).cattr(egs.add_prefix('image-stretch-mode')),
				scroll_axis: $(this).cattr(egs.add_prefix('scroll-axis')),
				transition: $(this).cattr(egs.add_prefix('transition')),
				grid_height: $(this).cattr(egs.add_prefix('grid-height')),
				grid_height_ratio: $(this).cattr(egs.add_prefix('grid-height-ratio')),
				media_height: $(this).cattr(egs.add_prefix('media-height')),
				media_height_ratio: $(this).cattr(egs.add_prefix('media-height-ratio')),
				gallery_img_title: $(this).cattr(egs.add_prefix('gallery-img-title')) == 1,
				hide_grid_cell_overflow: $(this).cattr(egs.add_prefix('hide-grid-cell-overflow')) == 1
			});
		});

		$('.ether-back-to-top').click( function()
		{
			$('html,body').scrollTop(0);

			return false;
		});

		var $scrollspy_menu = $('.ether-scrollspy a');

		var $scrollspy_elements = $scrollspy_menu.map( function()
		{
			var $item = $($(this).attr('href'));

			if ($item.length)
			{
				return $item;
			}
		});

		$scrollspy_menu.click( function(e)
		{
			var $target = $(this);
			var offset = $($(this).attr('href')).offset().top + $($(this).attr('href')).height();

			$('html, body').stop().animate({ scrollTop: offset }, 300, function()
			{
				$target.parent().siblings().removeClass('ether-current').end().addClass('ether-current');
			});

			return false;
		});

		var scrollspy_last_id;

		$(window).scroll( function()
		{
			var top = $(this).scrollTop();

			var current = $scrollspy_elements.map( function()
			{
				if ($(this).offset().top < top)
				{
					return this;
				}
			});

			if (current.length > 0)
			{
				current = current[current.length - 1];

				var id = current[0].id;

				$scrollspy_menu.parent().removeClass('ether-current').end().filter('[href=#' + id + ']').parent().addClass('ether-current');
			}
		});

		if (typeof $.fn.colorbox != 'undefined')
		{
			var cbox_rels = {};

			$('a[rel*=lightbox]').each( function()
			{
				var rel = $(this).attr('rel');

				if (typeof cbox_rels[rel] == 'undefined')
				{
					cbox_rels[rel] = true;
				}
			});

			for (var rel in cbox_rels)
			{
				rel = rel.replace('[', '\\[').replace(']', '\\]');

				if ( ! $('a[rel=' + rel + ']').eq(0).hasClass('cboxElement'))
				{
					$('a[rel=' + rel + ']').colorbox({ rel: rel, maxWidth: '80%', maxHeight: '80%', fixed: true });
				}
			}
		}

		var class_attr_to_cfg_arr = function (elem, re)
		{
			if	(elem.length > 0)
			{
				return elem.attr('class').match(re);
			}
		};

		var init_msg_boxes = function ()
		{
			var $elem = $('.' + egs.add_prefix('msg'));

			if ($elem.length > 0)
			{

				$('<div class="' + egs.add_prefix('ctrl-close') + '">close this window</div>')
					.appendTo($elem)
					.bind('click', function () {
						$(this).parent().hide(250);
					});

				$elem
					.bind('mouseenter', function () {
						$(this).children('.' + egs.add_prefix('ctrl-close')).stop(true, true).fadeIn(250);
					})
					.bind('mouseleave', function () {
						$(this).children('.' + egs.add_prefix('ctrl-close')).stop(true, true).fadeOut(250);
					});
			}
		};

		var accordion_init = function ($elem)
		{
			var $button = $elem.children('.' + egs.add_prefix('title'));
			var $content = $elem.children('.' + egs.add_prefix('content'));

			$content.hide();
			$button.each(function ()
			{
				if ($(this).hasClass(egs.add_prefix('current')))
				{
					$(this).next().stop(true).show(250);
				}
			});

			if ($elem.hasClass(egs.add_prefix('constrain-0')))
			{
				$button.click(function ()
				{
					$(this)
						.toggleClass(egs.add_prefix('current'))
							.next()
								.toggle(250)
				});
			} else
			{
				$button.click(function ()
				{
					$(this)
						.addClass(egs.add_prefix('current'))
						.siblings().removeClass(egs.add_prefix('current')).end()
							.next()
								.show(250)
								.siblings('.' + egs.add_prefix('content'))
									.hide(250);
				});
			}
		};

		var tabs_init = function ($elem)
		{
			$('<div class="' + egs.add_prefix('ctrl-tabs-1') + '"></div>').insertBefore($elem.children(':first'));

			var $title = $elem.children('.' + egs.add_prefix('title'));
			var $content = $elem.children('.' + egs.add_prefix('content'));
			var $ctrl = $elem.find('.' + egs.add_prefix('ctrl-tabs-1'));

			$ctrl.append($title);
			$content.hide();
			$title
				.addClass(egs.add_prefix('ref'))
				.each(function(id) {
					if ($(this).hasClass(egs.add_prefix('current'))) {
						$content.eq(id).stop(true).show();
					}
				})
				.click(function () {
					$(this).addClass(egs.add_prefix('current'))
						.siblings().removeClass(egs.add_prefix('current'));
					$content.eq($(this).index()).fadeIn(250).siblings('.' + egs.add_prefix('content')).fadeOut(250);
				})
		};

		var init_multi = function ()
		{
			var $elem = $('.' + egs.add_prefix('multi'));

			if ($elem.length > 0)
			{
				$elem.each(function()
				{
					var re = /(?:type)-(\w+)/;
					var type = class_attr_to_cfg_arr($(this), re)[1];

					if (type === 'acc')
					{
						accordion_init($(this));
					} else if (type === 'tabs')
					{
						tabs_init($(this));
					}
				});
			}

			$(window).resize(function ()
			{
				/*
				$elem.each(function ()
				{

					if ($(this).hasClass(egs.add_prefix('tabs-y')) && $(this).outerWidth() < 500)
					{
						$(this)
							.removeClass(egs.add_prefix('tabs-y'))
							.addClass(egs.add_prefix('tabs-x'))
							.addClass(egs.add_prefix('tabs-y-marker'));
					} else if ($(this).hasClass(egs.add_prefix('tabs-y-marker')) && $(this).outerWidth() >= 500)
					{
						$(this)
							.removeClass(egs.add_prefix('tabs-x'))
							.addClass(egs.add_prefix('tabs-y'));
					}
				});
				*/
			});

		};

		var init_pricing_table = function ()
		{
			var $elem = $('.' + egs.add_prefix('prc'));

			if ($elem.length > 0)
			{
				$elem.each(function () {
					var $prc = $(this);
					var $tr = $(this).find('tr');
					var $td = $(this).find('td');
					var $button_wrap = $(this).find('.' + egs.add_prefix('prc-button'));
					var $button = $($button_wrap).children('a');
					var h = $button.height();
					var timeout = [];

					if (($(this)).hasClass(egs.add_prefix('prc-2'))) {
						$button.css({height: 0});
					}

					var width = 100 / $(this).find('tr').eq(0).children().length;
					$(this).find('tr').eq(0).children().each(function ()
					{
						$(this).css({width: width + '%'});
					});

					$td
						.bind('mouseenter', function () {
							var id = $(this).index();
							clearTimeout(timeout[id]);
							delete timeout[id];
							if ( ! $(this).hasClass(egs.add_prefix('prc-col-hover'))) {
								$tr.each(function () {
									$(this).find('td').eq(id).addClass(egs.add_prefix('prc-col-hover'));
								});
								if (($prc).hasClass(egs.add_prefix('prc-2'))) {
									$button_wrap.eq(id).find('a')
										.stop(true, true)
										.animate({height: h}, 250);
								}
							}

						})
						.bind('mouseleave', function () {
							var id = $(this).index();
							timeout[id] = setTimeout(function () {
								$tr.each(function () {
									$(this).find('td').eq(id).removeClass(egs.add_prefix('prc-col-hover'));
								});
								if (($prc).hasClass(egs.add_prefix('prc-2'))) {
									$button_wrap.eq(id).find('a')
										.stop(true, true)
										.animate({height: 0}, 250);
								}
							}, 250);
						});
				});
			}
		};

		var fix_ie7_grid = function (elem)
		{
			var ie7_class = egs.add_prefix('ie7');

			elem.find('.' + egs.add_prefix('col')).each(function ()
			{
				var nested_cols;
				var current_col_width;
				var current_col_padding;

				if ( ! $(this).hasClass(ie7_class))
				{
					$(this)
						.addClass(ie7_class)
						.children().wrapAll('<div class="' + egs.add_prefix('ie7-padding-maker')+ '"></div>');
				}


				nested_cols = $(this).find('.ether-cols').eq(0);
				nested_cols = nested_cols.add(nested_cols.siblings('.ether-cols'));

				if (nested_cols.length > 0)
				{
					current_col_width = $(this).width();
					current_col_padding = parseInt($(this).css('padding-left'), 10);

					nested_cols.each(function ()
					{
						$(this).css({
							'width': current_col_width + 2 * current_col_padding,
							'margin-left': -current_col_padding
						});
					});
				}


			});


			elem.each(function ()
			{
				if ($(this).hasClass(egs.add_prefix('cols')))
				{
					$(this).width('');
					$(this).width($(this).width() + parseInt($(this).find('.' + egs.add_prefix('ie7-padding-maker')).css('padding-left'), 10) * 2);
					//$(this).width($(this).width());

				}
			});

		};

		var fix_lteie9_grid = function ()
		{
			if (isie && iev < 9)
			{
				var $elem = $('.' + egs.add_prefix('twitter-feed') + '.' + egs.add_prefix('cols') +  ', .' + egs.add_prefix('gallery .') + egs.add_prefix('cols') + ', .' + egs.add_prefix('cols'));

				$elem.find('.ether-col').each(function ()
				{
					$(this).children().eq(-1)
						.addClass('last-child');
				});

				if(iev <= 8)
				{
					$elem
						.each(function () {
							fix_ie7_grid($(this));
						});

					$(window)
						.bind('resize', function () {
							$elem.each(function () {
								fix_ie7_grid($(this));
							});
						});
				}
			}
		};

		var init_image_widget = function ()
		{
			var $centered_img_widget = $('a.' + egs.add_prefix('img') + '.' + egs.add_prefix('aligncenter'));

			egs.on_image_load_end($centered_img_widget, function ($elem)
			{
				$elem.css({
					width: $(this).children('img').width()
				});
			});

			init_img_widget_title();
		}

		init_media_img = function ()
		{
			$media_img = $('.' + egs.add_prefix('media-img'));

			$media_img.each(function ()
			{
				if ($(this).hasClass(''))
				{
				}
			});
		}

		init_video_widget = function ()
		{
			var $elem = $('.' + egs.add_prefix('media-wrap') + '.' + egs.add_prefix('aligncenter'));

			$elem.each(function ()
			{
				$(this).width($(this).children().eq().width());
			});

			$(window).resize(function ()
			{
				$elem.each(function ()
				{
					$(this).width($(this).children().eq().width());
				});
			});
		}

		init_img_widget_title = function ()
		{
			var $elem = $('.' + egs.add_prefix('show-img-title'));

			$elem.each(function ()
			{
				if ($(this).get(0).tagName === 'IMG')
				{
					$(this).wrap('<span>');
					$img_elem = $(this);
					$wrap_elem = $img_elem.parent();

					$wrap_elem
						.attr('class', $img_elem.attr('class'))
						.attr('style', ($img_elem.attr('style') || ''))
						.addClass(egs.add_prefix('frame'))
						.css('height', 'auto');

					$img_elem.attr('class', '');
					$img_elem.attr('style', '');

					if (
						! $wrap_elem.attr('style') ||
						$wrap_elem.attr('style') && $wrap_elem.attr('style').match('width') === null
					)
					{
						$wrap_elem.css('width', $img_elem.attr('width'));
					}

					$elem = $wrap_elem;
				} else
				{
					$elem = $(this);
				}

				egs.init_gallery_title($elem);
			});
		}

		init_image_widget();
		init_media_img();
		init_msg_boxes();
		init_multi();
		init_pricing_table();
		init_video_widget();
		fix_lteie9_grid();
	});
})(jQuery);
