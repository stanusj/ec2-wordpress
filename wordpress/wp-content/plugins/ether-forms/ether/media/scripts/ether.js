window.ether = window.ether || {};

(function($){

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

		for (i = 0; i < classes.length; i++)
		{
			if (classes[i].substr(0, key.length) == key)
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
			//$object.attr(attribute, $object.attr(attribute).replace(class_name, key + '-' + value));
			$object[0][attribute] = $object[0][attribute].replace(class_name, key + '-' + value);
		} else
		{
			$object[0][attribute] = $object[0][attribute] + ' ' + key + '-' + value;
			//$object.attr(attribute, $object.attr(attribute) + ' ' + key + '-' + value);
		}
	}

	return this;
};

$( function()
{
	// quick fix
	$('.inside > .ether-form, #form .inside .builder-location-wrapper').each( function()
	{
		$(this).parent().addClass('ether-inside');
	});

	ether.set_tabs = function ($widget)
	{
		var $widget_content;

		if ($widget.closest('.postbox').length > 0)
		{
			$widget_content = $widget;

			if ($widget.attr('data-ether-tabs') && $widget.attr('data-ether-tabs') === 'set')
			{
				return false;
			}

			var $tab_title = $widget_content.find('.ether-tab-title');
			var $tab_content = $widget_content.find('.ether-tab-content');
			var has_visible_tab = false;

			$tab_content.each(function (id)
			{
				$(this)
					.hide()
					.attr('data-tab-content-id', id);
			});

			$tab_title.each(function (id)
			{
				if ($(this).hasClass('ether-current'))
				{
					$tab_content.eq(id).show();
					has_visible_tab = true;
				}

				$(this)
					.attr('data-tab-id', id);
			});

			if ( ! has_visible_tab)
			{
				$tab_title.eq(0).addClass('ether-current')
				$tab_content.eq(0).addClass('ether-current').show();
			}

			$tab_title.wrapAll('<div class="ether-tab-title-wrap"></div>');
			$tab_content.wrapAll('<div class="ether-tab-content-wrap"></div>');

			$.merge($tab_title.parent(), $tab_content.parent()).wrapAll('<div class="ether-tabs ether-tabs-x ether-tabs-left"></div>');

			$widget.attr('data-ether-tabs', 'set');
		}
	}

	ether.set_tabs($('#form-options'));

	$('#form-options .ether-tab-title').live('click', function ()
	{
		var window_pos_y = $(window).scrollTop();
		var $tab_content;
		var id = $(this).index();

		$tab_content = $(this).closest('.ether-tabs').eq(0).find('.ether-tab-content');

		if ( ! $(this).hasClass('ether-current'))
		{
			$(this).siblings().removeClass('ether-current')
			$(this).addClass('ether-current')
			$tab_content
				.removeClass('ether-current')
				.stop(true).fadeOut(250)
				.eq(id).addClass('ether-current').stop(true).fadeIn(250);
		}

		$(window).scrollTop(window_pos_y);
	});

	function ether_farbtastic_prepare()
	{
		var color = $(this).children('input').eq(0).val();

		if ($(this).children('.ether-farbtastic').length == 0)
		{
			$(this).children('input').before('<span class="ether-farbtastic-trigger" style="background-color: ' + (color == '' ? '#ffffff' : color) + ';"></span>');
			$(this).children('input').before('<div class="ether-farbtastic" style="display: none; position: absolute; z-index: 50;"></div>');
		}
	}

	function ether_farbtastic_init()
	{
		var $trigger = $(this).prev('.ether-farbtastic-trigger');
		var $input = $(this).next('input');

		if (typeof $(this).get(0).farbtastic == 'undefined')
		{
			$(this).farbtastic($trigger);
		}

		$(this).get(0).farbtastic.setColor($input.val());
		$input.change().blur();
	}

	$('label.ether-color').each(ether_farbtastic_prepare);

	$('.ether-farbtastic-trigger').live('click', function()
	{
		var picker = $(this).next('.ether-farbtastic').get(0).farbtastic;

		if (typeof picker == 'undefined')
		{
			$(this).next('.ether-farbtastic').farbtastic($(this));
			picker = $(this).next('.ether-farbtastic').get(0).farbtastic;
			picker.setColor($(this).siblings('input').val());
		}

		$(this).next('.ether-farbtastic').fadeIn();

		return false;
	});

	$('.widget-inside .builder-widget .builder-widget-actions .edit').live('click', function()
	{
		var $parent = $(this).closest('.builder-widget');

		var $color_fields = $parent.find('.ether-color');

		if ($color_fields.length > 0)
		{
			$color_fields.each(ether_farbtastic_prepare);
			$color_fields.children('.ether-farbtastic').each(ether_farbtastic_init);
		}
	});

	$('.ether-farbtastic').each(ether_farbtastic_init);

	var farbtastic_no_update = false;

	$('.ether-color input').change( function()
	{
		if ( ! farbtastic_no_update)
		{
			var picker = $(this).prevAll('.ether-farbtastic').get(0).farbtastic;

			if (typeof picker == 'undefined')
			{
				$(this).prev('.ether-farbtastic').farbtastic($(this).prevAll('.ether-farbtastic-trigger'));
				picker = $(this).prev('.ether-farbtastic').get(0).farbtastic;
				//picker.setColor($(this).siblings('input').val());
			}
			picker.setColor($(this).val());

			var color = $(this).val();

			if (color.substring(0, 3) == 'rgb')
			{
				color = to_hex(color.replace('rgb(', '').replace(')', '').split(', '));
			}
		}

		farbtastic_no_update = false;
	});

	$('.ether-farbtastic').live('mousemove', function()
	{
		var $trigger = $(this).prev('.ether-farbtastic-trigger');
		var $input = $(this).next('input');
		var picker = $(this).get(0).farbtastic;

		var val = $input.val();
		var color = picker.color;

		if (color != val)
		{
			$input.val(color);
			farbtastic_no_update = true;
			$input.change();
		}
	});

	$(document).mousedown( function()
	{
		$('.ether-farbtastic:visible').each( function()
		{
			var picker = $(this).get(0).farbtastic;

			$(this).next('input').val(picker.color);
			$(this).fadeOut();
		});
	});

	// old color picker, should be replace soon
    /*$('.ether-color-picker').hide();

    $('.ether-color-picker').each( function()
	{
		var $span = $(this).prev('.color-picker-trig');
		var $input = $(this).next('.ether-color');

		$(this).farbtastic($span);

		$(this).get(0).farbtastic.setColor($input.val());

		$span.click( function()
		{
			$(this).next('div').fadeIn();
		});
	});

	var color_no_update = false;

	$('input.ether-color').change( function()
	{
		if ( ! color_no_update)
		{
			var picker = $(this).prevAll('.ether-color-picker').get(0).farbtastic;

			picker.setColor($(this).val());

			var color = $(this).val();

			if (color.substring(0, 3) == 'rgb')
			{
				color = to_hex(color.replace('rgb(', '').replace(')', '').split(', '));
			}
		}

		color_no_update = false;
	});

	$('.ether-color-picker').live('mousemove', function()
	{
		var $span = $(this).prev('.color-picker-trig');
		var $input = $(this).next('.ether-color');
		var picker = $(this).get(0).farbtastic;
		var val = $input.val();
		var color = picker.color;

		if (color != val)
		{
			$input.val(color);
			color_no_update = true;
			$input.change();
		}
	});

	$(document).mousedown( function()
	{
		$('.ether-color-picker:visible').each( function()
		{
			var picker = $(this).get(0).farbtastic;

			$(this).next('.ether-color').val(picker.color);
			$(this).fadeOut();
		});
	});*/

	$('input[name=reset]').click( function()
	{
		if ( ! confirm('Are you sure you want to reset settings on this page? \'Cancel\' to stop, \'OK\' to reset.'))
		{
			return false;
		}
	});

	$('.confirm').live('click', function()
	{
		if ( ! confirm('Are you sure?'))
		{
			return false;
		}
	});

	function img_placeholder_src(src, force)
	{
		if (typeof force == 'undefined')
		{
			force = false;
		}

		if (src != null && typeof src.length != 'undefined')
		{
			if (src.length > 0 && (src.match(/^(?:.*?)\.?(youtube|vimeo)\.com\/(watch\?[^#]*v=(\w+)|(\d+)).+$/) || src.match ((/^(?:.*?)\.?(ted)\.com\//))))
			{
				return ether.placeholder.video;
			} else if (force || src.length == '')
			{
				return ether.placeholder.img;
			}
		}

		return src;
	}

	$('img.upload_image').each( function()
	{
		var src = img_placeholder_src($(this).attr('src'));

		$(this).attr('src', src);
	});

	$('img.upload_image').live('error', function()
	{
		var src = img_placeholder_src($(this).attr('src'), true);

		$(this).attr('src', src);
	});

	$('input.upload_image').live('change', function()
	{
		var src = img_placeholder_src($(this).val());

		$(this).closest('.group-item-content').find('img.upload_image').attr('src', src);
	});

	$('.ether img').load( function()
	{
		$(this).show();
	});

	$('.ether .hidden').hide();

	$('.wp-editor-tools .add_media').live('click', function()
	{
		ether.editor = tinyMCE.activeEditor;
	});

	$('.checkbox-group-toggle-button').click(function ()
	{
		var ref_id = $(this).attr('class').match(/checkbox-group-ref-\d/)[0];
		var state = $(this).attr('data-checkbox-group-state');
		$(this).attr('data-checkbox-group-state', state == 0 ? 1 : 0);

		if (state == 0)
		{
			$(this).text('Deselect All');
			$('#' + ref_id).find('input[type="checkbox"]').prop('checked', true);
		} else
		{
			$(this).text('Select All');
			$('#' + ref_id).find('input[type="checkbox"]').prop('checked', false);
		}
		$(this).toggleClass('icon-check icon-uncheck');
	});

	window.wp_send_to_editor = window.send_to_editor;

	window.send_to_editor = function(html)
	{
		/*if (typeof o == "object" || typeof o == "array")
		{
			if (ether.upload_dst == null)
			{
				for (var i = 0; i < o.length; i++)
				{
					add_image(o[i]);
				}
			} else
			{
				if (o.length > 0)
				{
					var src = o[0];

					$dst = $(ether.upload_dst);

					$dst.each( function()
					{
						if ($(this).is("img"))
						{
							$(this).attr("src", src).show();
						} else if ($(this).is("a"))
						{
							$(this).attr("href", src);
						} else if ($(this).is("input"))
						{
							$(this).val(src);
						}
					});
				}
			}

			tb_remove();
			ether.upload_dst = null;
		}*/

		if ((typeof html == "object" || typeof html == "array") && ether.editor == null)
		{
			if (typeof ether.upload_callback == 'string' && ether.upload_callback != '')
			{
				for (var i = 0; i < html.length; i++)
				{
					eval(ether.upload_callback + '(\'' + html[i] + '\');');
				}
			} else if (typeof ether.upload_callback == 'function')
			{
				for (var i = 0; i < html.length; i++)
				{
					ether.upload_callback(html[i]);
				}
			}

			if (ether.upload_dst != null)
			{
				if (html.length > 0)
				{
					var src = html[0];

					var $dst = $(ether.upload_dst);

					$dst.each( function()
					{
						if ($(this).is("img"))
						{
							$(this).attr("src", src).show();
						} else if ($(this).is("a"))
						{
							$(this).attr("href", src);
						} else if ($(this).is("input"))
						{
							$(this).val(src).change();
						}
					});
				}
			}

			tb_remove();
			ether.upload_dst = null;
			ether.upload_callback = '';
			ether.upload_caller = null;
		} else
		{
			if (ether.upload_dst != null)
			{
				var url = '';
				var alt = '';
				var title = '';

				if ($('img', html).length > 0)
				{
					url = $('img', html).attr('src');
					alt = $('img', html).attr('alt');
					title = $('img', html).attr('title');
				} else
				{
					url = html;
				}

				$dst = $(ether.upload_dst);
				$alt = $(ether.upload_dst + '_alt');
				$title = $(ether.upload_dst + '_title');

				$dst.each( function()
				{
					if ($(this).is('img'))
					{
						$(this).attr('src', url).show();
					} else if ($(this).is('a'))
					{
						$(this).attr('href', url);
					} else if ($(this).is('input'))
					{
						$(this).val(url).change();
					}
				});

				$alt.each( function()
				{
					if ($(this).is('img'))
					{
						$(this).attr('alt', alt).show();
					} else if ($(this).is('a'))
					{
						$(this).text(alt);
					} else if ($(this).is('input'))
					{
						$(this).val(alt);
					}
				});

				$title.each( function()
				{
					if ($(this).is('img'))
					{
						$(this).attr('title', title).show();
					} else if ($(this).is('a'))
					{
						$(this).attr('title', title);
					} else if ($(this).is('input'))
					{
						$(this).val(title);
					}
				});

				tb_remove();
				ether.upload_dst = null;
			} else
			{
				if (typeof tinyMCE != 'undefined' && tinyMCE.activeEditor != null)
				{
					if (typeof html == "object" || typeof html == "array")
					{
						for (var i = 0; i < html.length; i++)
						{
							if (window.tinyMCE.majorVersion >= 4)
							{
								tinyMCE.execCommand("mceInsertContent", false, '<a href="' + html[i] + '" class="alignleft"><img src="' + html[i] + '" alt="" width="300" /></a>');
							} else
							{
								tinyMCE.execInstanceCommand(tinyMCE.activeEditor.id, "mceInsertContent", false, '<a href="' + html[i] + '" class="alignleft"><img src="' + html[i] + '" alt="" width="300" /></a>');
							}
						}
					} else
					{
						if (window.tinyMCE.majorVersion >= 4)
						{
							tinyMCE.execCommand("mceInsertContent", false, html);
						} else
						{
							tinyMCE.execInstanceCommand(tinyMCE.activeEditor.id, "mceInsertContent", false, html);
						}
					}

					tb_remove();
				} else
				{
					window.wp_send_to_editor(html);

					if (html.indexOf('src=') != 1)
					{
						if ($(html).is('img'))
						{
							html = $(html).attr('src');
						} else
						{
							html = $(html).find('img').attr('src');
						}
					}

					if (typeof ether.upload_callback == 'string' && ether.upload_callback != '')
					{
						eval(ether.upload_callback + '(\'' + html + '\');');
					} else if (typeof ether.upload_callback == 'function')
					{
						ether.upload_callback(html);
					}

					ether.upload_callback = '';

					tb_remove();
				}
			}
		}

		ether.editor = null;
	};

	$('.remove_image').click( function()
	{
		var name = $(this).attr('name').replace('#', '').replace(ether.prefix + 'remove_', '');

		$('input.upload_' + name).val('').change();
		$('input.upload_' + name + '_alt').val('');
		$('input.upload_' + name + '_title').val('');

		$('img.upload_' + name).attr('src', ether.placeholder.img);

		return false;
	});

	$('.upload_image').live('click', function()
	{
		if ($(this).is('button'))
		{
			var name = $(this).attr('name');
			var width = $(this).cattr('width');
			var height = $(this).cattr('height');
			var single = $(this).hasClass('single');
			var tab = $(this).cattr('tab');
			var callback = $(this).cattr('callback');

			if (tab == '')
			{
				tab = 'images';
			}

			if ( ! callback)
			{
				ether.upload_dst = '.' + name.replace(ether.prefix, '').replace('[', '\[').replace(']', '\]');

				ether.upload_callback = '';
			} else
			{
				ether.upload_dst = null;
				ether.upload_callback = callback;
			}

			ether.upload_caller = $(this);

			tb_show('', 'media-upload.php?&type=image&post_id=0&ether=true&output=html&width=' + width + '&height=' + height + '&tab=' + tab + '&single=' + single + '&TB_iframe=true');

			return false;
		}
	});

	$('.upload_media').live('click', function()
	{
		if ($(this).is('button'))
		{
			var name = $(this).attr('name');
			ether.upload_dst = '.' + name.replace(ether.prefix, '');

			tb_show('', 'media-upload.php?&post_id=0&ether=true&TB_iframe=true');

			return false;
		}
	});

	// OMG FIX FOR ITHEMES BUILDER

	var ether_tb_showIframe = tb_showIframe;

	tb_showIframe = function()
	{
		ether_tb_showIframe();
		tb_position();

		setTimeout( function()
		{
			tb_position();
		}, 10);
	};

	// var ether_tb_showIframe = tb_showIframe;

	ether.get_cond_field_val = function (elem, $scope)
	{
		var val;
		var $elem;
		var is_checkbox;

		if (typeof elem === 'string')
		{
			if ($scope !== undefined)
			{
				$elem = $scope.find(elem).eq(0);
			} else
			{
				$elem = $(elem).eq(0);
			}
		} else
		{
			$elem = elem;
		}

		is_checkbox = $elem.is('input') && $elem.attr('type') == 'checkbox';

		val = $elem.val();

		if (is_checkbox)
		{
			if ($elem.attr('checked'))
			{
				val = 'on';
			} else
			{
				val = 'off';
			}
		}

		return val;
	}

	ether.update_cond_field = function ()
	{
		var id = $(this).cattr('ether-cond-field-id');
		var cond = ether.cond_fields[id];
		var $scope = $(this).parents('fieldset').eq(0);
		var val = ether.get_cond_field_val($(this));
		var field_data;
		var a;

		for (a = 0; a < cond.length; a += 1)
		{
			field_data = cond[a];
			ether.update_ruleset(field_data.group_id, field_data.action, field_data.ruleset_id);
			ether.update_actions(field_data.group_id);
		}
	}

	ether.validate_rule = function (rule)
	{
		var $cond_field = $('.ether-cond-field-id-' + rule.field_id);
		var $scope = $cond_field.parents('fieldset').eq(0);

		var val = ether.get_cond_field_val($cond_field);

		var result = [];
		var match = false;
		var a;

		for (a = 0; a < rule.values.length; a += 1)
		{
			if (rule.field_id === 79)
			{
				//console.log('val: ' + val + ' rule: ' + rule.values[a] + ' result: ' + (val === rule.values[a]));
			}


			result.push(val == rule.values[a]);
		}

		match = (rule.is ? false : true);

		for (a = 0; a < result.length; a += 1)
		{
			if (rule.is === true && result[a] === true)
			{
				match = true;
			} else if (rule.is === false && result[a] === true)
			{
				match = false;
			}
		}

		//console.log(rule)
		//console.log(match)

		return match;
	}

	ether.update_ruleset = function (cond_group_id, action, ruleset_id)
	{
		var ruleset = ether.cond_groups[cond_group_id].actions[action][ruleset_id];
		var a;
		var result = true;

		for (var a = 0; a < ruleset.rules.length; a += 1)
		{
			if ( ! ether.validate_rule(ruleset.rules[a]))
			{
				result = false;
			}
		}

		ether.cond_groups[cond_group_id].actions[action][ruleset_id].state = result;

		return result;
	}

	ether.trigger_action = function (id, key)
	{
		//console.log('trigger action: ' + key + ' on elem: ' + id)

		var cond_group = ether.cond_groups[id];
		var $cond_group = $('.ether-cond-group-id-' + id);

		switch (key)
		{
			case 'show':
			{
				$cond_group.slideDown(500);
				//$cond_group.show();
				break;
			}
			case 'hide':
			{
				$cond_group.slideUp(500, function ()
				{
					$(this).css({display: 'none'});
					$(this).dequeue();
				});
				//$cond_group.hide();
				break;
			}
		}
	}

	ether.update_actions = function (id)
	{
		var cond_group = ether.cond_groups[id];
		var result;
		var states;
		var rulesets;
		var a;

		for (key in cond_group.actions)
		{
			result = false;
			rulesets = cond_group.actions[key];

			if (rulesets.length > 0)
			{
				for (a = 0; a < rulesets.length; a += 1)
				{
					if (rulesets[a].state === true)
					{
						result = true;
					}
				}

				ether.trigger_action(id, key === 'show' ? (result === true ? 'show' : 'hide') : (key === 'hide' ? (result === true ? 'hide' : 'show') : key));
			}
		}
	}

	ether.update_cond_group = function (id)
	{
		var cond_group = ether.cond_groups[id];
		var a;
		var action;

		for (key in cond_group.actions)
		{
			action = cond_group.actions[key];

			for (a = 0; a < action.length; a += 1)
			{
				ether.update_ruleset(id, key, a);
			}
		}

		ether.update_actions(id);
	}

	ether.register_cond_field = function ($cond_field)
	{
		$cond_field.addClass('ether-cond-field-id-' + ether.cond_field_id);

		ether.cond_fields[ether.cond_field_id] = [];

		ether.cond_field_id += 1;

		return ether.cond_field_id - 1;
	}

	ether.register_cond = function ($scope, cond_field_name, cond_group, action)
	{
		$scope = $scope.length ? $scope : $('body'); //required for outside of ether builder environments //probably revision would be a good thing

		var cond;
		var $cond_field = $scope.find('.ether-field-' + cond_field_name);
		var cond_field_id;

		// console.log('register cond', arguments, $cond_field)

		if ($cond_field[0].className.indexOf('ether-cond-field-id-') !== -1)
		{
			cond_field_id = $cond_field.cattr('ether-cond-field-id');
		} else
		{
			cond_field_id = ether.register_cond_field($cond_field);
		}

		cond = {
			group_id: cond_group.cond_group_id,
			action: action,
			ruleset_id: cond_group.actions[action].length,
		}

		ether.cond_fields[cond_field_id].push(cond);

		return cond_field_id;
	}

	ether.make_rule = function (field_id, values, filters)
	{
		var result =
		{
			field_id: field_id,
			values: values,
			is: true
		};
		var a;

		if (filters !== undefined)
		{
			for (a = 0; a < filters.length; a += 1)
			{
				switch (filters[a])
				{
					case 'isnot':
					{
						result.is = false;
						break;
					}
				}
			}
		}

		return result;
	}

	ether.register_ruleset = function (cond_group, action, ruleset, $scope)
	{
		var result = {
			state: false,
			rules: []
		};
		var a;
		var rule;
		var filters;
		var cond;
		var cond_field_name;
		var cond_field_id;

		// console.log('register ruleset', arguments);

		ruleset = ruleset.split('-and-');

		for (a = 0; a < ruleset.length; a += 1)
		{
			rule = ether.exp.cond_field_exp.exec(ruleset[a]);

			filters = rule[1] !== undefined ? rule[1].split(ether.exp.filter_exp).slice(1) : undefined;
			cond = rule[2].split(ether.exp.cond_val_exp).slice(1);
			cond_field_name = rule[3].replace('ether-field-', '');

			cond_field_id = ether.register_cond($scope, cond_field_name, cond_group, action);

			result.rules.push(ether.make_rule(cond_field_id, cond, filters));
		}

		cond_group.actions[action].push(result);
	}

	ether.remove_cond_data = function ()
	{
		$cond_groups = $(this).find('.ether-cond-group');
		$cond_fields = $(this).find('.ether-cond-field');

		$cond_groups.each(function ()
		{
			var cond_group_id = $(this).cattr('ether-cond-group-id');

			delete ether.cond_groups[cond_group_id];

			//console.log('group removed: ' + cond_group_id);
		});

		$cond_fields.each(function ()
		{
			var cond_field_id = $(this).cattr('ether-cond-field-id');

			delete ether.cond_fields[cond_field_id];

			//console.log('field removed: ' + cond_field_id );
		});
	}

	ether.init_cond_group = function ()
	{

		if ($(this)[0].className.indexOf('ether-cond-group-id-') !== -1)
		{
			//console.log('been here (group)');
			return false;
		}

		var $scope = $(this).parents('fieldset').eq(0);
		var classes = $(this)[0].className.split(' ');
		var matches = [];
		var rules_str;
		var rule_str;
		var rule;
		var a;
		var b;
		var c;
		var action;
		var cond;
		var group;
		var ruleset;
		var cond_group_id = ether.cond_group_id;
		var cond_group;
		var cond_field_id;
		var cond;

		ether.cond_groups[cond_group_id] = {
			cond_group_id: cond_group_id,
			actions: {
				show: [],
				hide: []
			}
		};

		//action - action: [ruleset, ruleset, ...]

		//ruleset - {state: false, rules: [{rule}, {rule}, ...]} - all rules must return true in order for a ruleset to be true

		//rule - obj with cond_fields ids containing array of condition vals:
		//{
		//	field_id: field_id
		//	is: true, //rule must be met; flase - rule must not be met
		//	rules: []
		//}

		cond_group = ether.cond_groups[cond_group_id];

		$(this).addClass('ether-cond-group-id-' + cond_group_id);

		for (a = 0; a < classes.length; a += 1)
		{
			if (classes[a].indexOf('ether-action-') !== -1)
			{
				matches.push(classes[a]);
			}
		}

		//[
		//ether-action-show-ether-cond-1-ether-field-1-or-ether-cond-2-ether-field-2,
		//ether-action-show-ether-cond-3-ether-cond-4-ether-field-3-and-ether-cond-5-ether-field-4-or-ether-cond-6-ether-field-5
		//]

		for (a = 0; a < matches.length; a += 1)
		{
			matches[a] = {
				action: ether.exp.action_exp.exec(matches[a])[1],
				rulesets: matches[a].replace(ether.exp.action_exp, '').split('-or-')
			}
		}

		//[
		//	{ action: 'show', rules:
		//		[
		//			ether-filter-isnot-ether-cond-1-ether-field-1,
		//			ether-cond-2-ether-field-2
		//		]
		//	},
		//	{ action: 'show', rules:
		//		[
		//			ether-action-show-ether-cond-3-ether-cond-4-ether-field-3-and-ether-cond-5-ether-field-4,
		//			ether-cond-6-ether-field-5
		//		]
		//	},
		//]

		for (a = 0; a < matches.length; a += 1)
		{
			action = matches[a].action;
			rulesets = matches[a].rulesets;

			for (b = 0; b < rulesets.length; b += 1)
			{
				ether.register_ruleset(cond_group, action, rulesets[b], $scope);
			}
		}

		ether.cond_group_id += 1;

		ether.update_cond_group.call($(this), cond_group_id);
	}

	ether.init_cond_groups = function (args)
	{
		//field naming convention (NOTE! drop the brackets [])
		//ether-cond-field ether-cond-[field-unique-name]
		//SIMPLE: //ether-cond-group ether-action-[show/hide]-ether-cond-[value]-ether-field-[field-unique-name]
		//ADVANCED: //ether-cond-group ether-action-[show/hide]-ether-filter-[is/isnot]-ether-cond-[value]-ether-field-column-count-[or/and]-ether-filter-[is/isnot]-ether-cond-[value]-ether-field-[field-unique-name]

		var skip_builder = args.skip_builder;
		var $scope;

		if (args.$scope === undefined)
		{
			$scope = $('body');
		} else
		{
			$scope = args.$scope;
		}

		if ($scope.hasClass('ether-cond-group-init'))
		{
			//console.log('been here (scope)');
			return false;
		} else
		{
			$scope.addClass('ether-cond-group-init');
		}

		$scope.find('.ether-cond-group').not(skip_builder ? '#builder-widgets .ether-cond-group, .builder-location-wrapper .ether-cond-group' : '').each(ether.init_cond_group);

		$scope.find('.ether-cond-field').not(skip_builder ? '#builder-widgets .ether-cond-field, .builder-location-wrapper .ether-cond-field' : '').each(function ()
		{
			$(this).live('change', ether.update_cond_field);
		});

		$scope.find('input.ether-cond-field:text, textarea.ether-cond-field').not(skip_builder ? '#builder-widgets .ether-cond-field, .builder-location-wrapper .ether-cond-field' : '').each(function ()
		{
			$(this).live('keyup', ether.update_cond_field);
		});
	}

	ether.init_ether_cond = function ()
	{
		ether = ether || {};
		ether.cond_fields = ether.cond_fields || {};
		ether.cond_groups = ether.cond_groups || {};

		if ( ! ether.exp)
		{
			ether.exp = {
				cond_val_exp: new RegExp('-*ether-cond-'),
				cond_field_exp: new RegExp('(ether-filter-[\\d\\w|-]*?)?-{0,1}(ether-cond-[\\d|\\w|-]*?)-(ether-field-[\\d|\\w|-]*)[\\s|"]*'),
				filter_exp: new RegExp('-*ether-filter-'),
				action_exp: new RegExp('ether-action-(\\w+)-')
			}
		}

		ether.cond_group_id = 0;
		ether.cond_field_id = 0;

		ether.init_cond_groups({skip_builder: true});
	}

	ether.init_ether_cond();
});

})(jQuery);
