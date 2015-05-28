
function builder_image_widget_change(image)
{
	if (ether.upload_caller != null)
	{
		jQuery(ether.upload_caller).closest('fieldset').find('img.upload_image').attr('src', image);
		jQuery(ether.upload_caller).closest('fieldset').find('input.upload_image').val(image);
	}
}

function builder_gallery_widget_change(image)
{
	if (ether.upload_caller != null)
	{
		jQuery(ether.upload_caller).closest('.group-item').find('img.upload_image').attr('src', image);
		jQuery(ether.upload_caller).closest('.group-item').find('input.upload_image').val(image);
	}
}

function builder_gallery_widget_insert(image)
{
	if (ether.upload_caller != null)
	{
		var $container = jQuery(ether.upload_caller).closest('fieldset');
		var $gallery = $container.find('.group-content').children().eq(0);
		var $first = $container.find('.group-prototype').children().eq(0);
		var $clone = $first.clone();

		$clone.find('input, textarea, select').val('');
		$clone.find('textarea').text('');
		$clone.find('img').attr('src', image);
		$clone.find('input.upload_image').val(image);

		$gallery.append($clone.clone().show().css('display',''))

		if ( ! $gallery.hasClass('ui-sortable') || ! $gallery.hasClass('ui-sortable-refreshed'))
		{
			$gallery.sortable({
				handle: '.group-item-title',
				appendTo: 'parent',
				tolerance: 'pointer',
				delay: 100,
				forceHelperSize: true,
				start: function (evt, ui)
				{
					ui.helper.css({width:ui.item.width() + 60});
					ui.placeholder.css({height: ui.item.children().eq(0).height() + 32});
				}
			});
			$gallery.addClass('ui-sortable-refreshed');
		} else
		{
			$gallery.sortable().sortable('refresh');
		}

		ether.set_dynamic_label($gallery);

	}
}

function get_attr_val($widget, attr)
{
	var $attr = $widget.find('select, input').filter('[name*="' + attr + '"]');
	var val = null;

	if ($attr[0].nodeName === 'SELECT')
	{
		val = $attr.val();
	} else if ($attr[0].nodeName === 'INPUT')
	{
		switch ($attr.attr('type'))
		{
			case 'text':
			{
				val = $attr.text();
				break;
			}
			case 'checkbox':
			{
				val = $attr.is('checked')
				break;
			}
			case 'radio':
			{
				val = $attr.is('selected');
				break;
			}
		}
	}

	return val;
}

(function($){$(function()
{
	$.expr[':'].icontains = function(obj, index, meta, stack){return (obj.textContent || obj.innerText || jQuery(obj).text() || '').toLowerCase().indexOf(meta[3].toLowerCase()) >= 0;};

	function update_select_length ($elem, length)
	{
		//$elem - target select elem
		//length - count (and values of) option elements

		//shift hack > 1 for tabs / accordion / 0 for slider position

		if ($elem.length === 0)
		{
			return false;
		}

		var shift = ($elem.attr('name').match(/current/) ? 1 : 0);

		//shift hack #2 > count slider groups
		shift === 0 ? length = Math.ceil(length / (get_attr_val($elem.parents('fieldset').eq(0), 'rows') * get_attr_val($elem.parents('fieldset').eq(0), 'columns'))) : '';

		var $options = $elem.children('option');
		var options_length = $options.length;
		var current_option_index = $elem.children('option:selected').index();


		if ($options.length > length + shift)
		{
			$elem.children().remove().end().append($options.slice(0, length + shift));

			if (current_option_index > length + shift)
			{
				$elem.children('option').eq(0).attr('selected', 'selected');
			}
		} else if (options_length - shift < length)
		{
			for (var i = 0; i < length - (options_length - shift); i++)
			{
				$elem.append('<option value="' + ((options_length - shift) + i) + '">' + ((options_length - shift) + i + 1) + '</option>');
			}
		}
	}

	var WIDGET_UPDATE =
	{
		ICON:
		{
			list: function ($widget)
			{
				var icon = $widget.find('select.builder-list-widget-bullet-field').val();

				icon === '' ? icon = 'default' : '';

				$widget.find('.builder-widget-icon').attr('class', 'builder-widget-icon builder-widget-icon-list builder-list-widget-icon-' + icon);
			},
			message: function ($widget)
			{
				$widget.find('.builder-widget-icon').attr('class', 'builder-widget-icon builder-widget-icon-message ether-msg ether-msg-' + $widget.find('select.builder-message-widget-type-field').val());
			}
		},

		PREVIEW:
		{
			tabs: function ($widget)
			{
				this.multi($widget, 'tabs', 'tabs');
			},
			accordion: function ($widget)
			{
				this.multi($widget, 'accordion', 'tabs');
			},
			multi: function ($widget, type, subtype, title_field, content_field)
			{
				var $items = $widget.find('.group-item').slice(1);
				var $wrap = $widget.find('.builder-multi-preview-wrap');

				title_field = title_field || 'title';
				content_field = content_field || 'content';

				$items.each(function (id)
				{
					var $title = $(this).find('input.builder-' + type + '-widget-' + subtype + '-' + title_field + '-field').val();
					var $content = $(this).find('textarea.builder-' + type + '-widget-' + subtype + '-' + content_field + '-field').val();

					if ( ! $wrap.children().eq(id).length)
					{
						$wrap.append('<div class="builder-multi-preview"><div class="builder-multi-preview-title"></div><div class="builder-multi-preview-content"></div>');
					}

					$wrap.find('.builder-multi-preview').eq(id)
						.children('.builder-multi-preview-title').text($title.substring(0, 24)).end()
						.children('.builder-multi-preview-content').text($content.substring(0, 128));
				});

				$wrap.find('.builder-multi-preview').slice($items.length).remove();
			},
			gallery: function ($widget)
			{
				var $preview = $widget.find('.builder-widget-gallery-preview');
				var $items = $widget.find('.group-item').slice(1);

				$preview.children().slice($items.length).remove();

				$items.each(function (id)
				{
					var $img = $(this).find('img');
					var url = $img.attr('src');
					var alt = $img.attr('alt');
					// var thumb_url = (url.indexOf(ether.thumb_size) === -1 ? url.replace(/(\.\w+)$/, ether.thumb_size + '$1') : url);
					var thumb_url = ( ! url.match(/\/placeholder\.\w+/) && url.indexOf(ether.thumb_size) === -1 ? url.replace(/(\.\w+)$/, ether.thumb_size + '$1') : url);

					if ( ! $preview.children().eq(id).length)
					{
						$preview.append('<img />');
					}

					$preview.children().eq(id)
						.attr('src', thumb_url)
						.attr('alt', alt);
				});
			},
			image: function ($widget)
			{
				var $preview = $widget.find('.builder-widget-image-preview');
				var $img = $widget.find('.preview-img-wrap img');
				var url = $img.attr('src');
				var alt = $img.attr('alt');
				var thumb_url = ( ! url.match(/\/placeholder\.\w+/) && url.indexOf(ether.thumb_size) === -1 ? url.replace(/(\.\w+)$/, ether.thumb_size + '$1') : url);

				if ( ! $preview.children().length)
				{
					$preview.append('<img />');
				}

				$preview.children('img')
						.attr('src', thumb_url)
						.attr('alt', alt);
			},

			services: function ($widget)
			{
				var $items = $widget.find('.group-item').slice(1);
				var $wrap = $widget.find('.builder-multi-preview-wrap');

				this.multi($widget, 'services', 'service');

				//custom code
			},

			testimonials: function ($widget)
			{
				var $items = $widget.find('.group-item').slice(1);
				var $wrap = $widget.find('.builder-multi-preview-wrap');

				this.multi($widget, 'testimonials', 'testimonial', 'author');

				//custom code
			},

			'pricing-box': function ($widget)
			{
				var $items = $widget.find('.group-item').slice(1);
				var $wrap = $widget.find('.builder-multi-preview-wrap');

				this.multi($widget, 'pricing-box', 'box');

				//custom code
			}
		},

		TITLE:
		{
			divider: function ($widget)
			{
				var result = '';
				result += $widget.find('select.builder-divider-widget-back-to-top-title-field').find('option:selected').text();
				return result;
			},
			gallery: function ($widget)
			{
				// console.log($widget.find('.group-item').length - 1);
				return 'Items: ' + $widget.find('.group-item').length - 1;
			},
			button: function ($widget)
			{
				var $title = $widget.find('.builder-widget-title');
				var title = $widget.find('input.builder-button-widget-label-field').val();

				title === '' ? title = 'button' : '';

				$title.children('span')
					.text(title)
					.css({
						'background-color': $widget.find('input.builder-button-widget-background-field').val(),
						'color': $widget.find('input.builder-button-widget-color-field').val()
					});
			},
			video: function ($widget)
			{
				var $title = $widget.find('.builder-widget-title');
				var title = $widget.find('input.builder-video-widget-url-field').val();

				title !== '' ? $title.text(title) : '';
			}
		},

		SUMMARY:
		{
			list: function ($widget)
			{
				var result = '';
				result += $widget.find('select.builder-list-widget-items-layout-field').find('option:selected').text();
				return result;
			},

			heading: function ($widget)
			{
				var result = '';
				result += $widget.find('select.builder-heading-widget-type-field').find('option:selected').text();
				return result;
			},

			code: function ($widget)
			{
				var result = '';
				result += $widget.find('select.builder-code-widget-type-field').find('option:selected').text();
				return result;
			},

			blockquote: function ($widget)
			{
				var result = '';
				result += $widget.find('select.builder-blockquote-widget-style-field').find('option:selected').text();
				return result;
			},

			button: function ($widget)
			{
				var result = '';
				result += ($widget.find('select.builder-button-widget-style-field').find('option:selected').text() + ' ' + $widget.find('input.builder-button-widget-url-field').val());
				return result;
			},

			'post-feed': function ($widget)
			{
				var result = '';
				result += ('Category: ' + $widget.find('select.builder-post-feed-widget-term-field').find('option:selected').text() + ' Order by: ' + $widget.find('select.builder-post-feed-widget-orderby-field').find('option:selected').text() + ' Count: ' + $widget.find('select.builder-post-feed-widget-numberposts-field').find('option:selected').text() + '; ' + $widget.find('select.builder-post-feed-widget-style-field').find('option:selected').text());
				return result;
			},

			'page-feed': function ($widget)
			{
				var result = '';
				result += ('Category: ' + $widget.find('select.builder-page-feed-widget-term-field').find('option:selected').text() + ' Order by: ' + $widget.find('select.builder-page-feed-widget-orderby-field').find('option:selected').text() + ' Count: ' + $widget.find('select.builder-page-feed-widget-numberposts-field').find('option:selected').text() + '; ' + $widget.find('select.builder-page-feed-widget-style-field').find('option:selected').text());
				return result;
			},

			'custom-feed': function ($widget)
			{
				var result = '';
				result += ('Category: ' + $widget.find('select.builder-custom-feed-widget-term-field').find('option:selected').text() + ' Order by: ' + $widget.find('select.builder-custom-feed-widget-orderby-field').find('option:selected').text() + ' Count: ' + $widget.find('select.builder-custom-feed-widget-numberposts-field').find('option:selected').text() + '; ' + $widget.find('select.builder-custom-feed-widget-style-field').find('option:selected').text());
				return result;
			},
			divider: function ($widget)
			{
				var result = '';
				result += ($widget.find('input.builder-divider-widget-back-to-top-field').is(':checked') ? '#' + $widget.find('input.builder-divider-widget-back-to-top-custom-link-field').val() : '');
				return result;
			},
			grid_slider: function ($widget)
			{
				var result = '';
				result += ('Cols: ' + $widget.find('select.ether-field-column-count').find('option:checked').text() + ' Rows: ' + $widget.find('select.ether-field-row-count').find('option:checked').text() + ' Slider: ' + ($widget.find('input.ether-field-slider-options').is(':checked') ? 'Yes' : 'No'));
				return result;
			},
			gallery: function ($widget)
			{
				var result = '';
				result += this.grid_slider($widget);
				return result;
			},
			'twitter-feed': function ($widget)
			{
				var result = '';
				result += this.grid_slider($widget);
				return result;
			},
			'flickr': function ($widget)
			{
				var result = '';
				result += this.grid_slider($widget);
				return result;
			},
			testimonials: function ($widget)
			{
				var result = '';
				result += this.grid_slider($widget);
				return result;
			},
			services: function ($widget)
			{
				var result = '';
				result += this.grid_slider($widget);
				return result;
			},
			'pricing-box': function ($widget)
			{
				var result = '';
				result += this.grid_slider($widget);
				return result;
			},
			tabs: function ($widget)
			{
				var result = '';
				result += ('Style: ' + $widget.find('select.builder-tabs-widget-style-field').find('option:selected').text() + ' Type: ' + $widget.find('select.builder-tabs-widget-type-field').find('option:selected').text() + ' Selected tab: ' + $widget.find('select.builder-tabs-widget-current-field').find('option:selected').text());
				return result;
			},
			accordion: function ($widget)
			{
				var result = '';
				result += ('Style: ' + $widget.find('select.builder-accordion-widget-style-field').find('option:selected').text() + ' Type: ' + $widget.find('select.builder-accordion-widget-type-field').find('option:selected').text() + ' Selected tab: ' + $widget.find('select.builder-accordion-widget-current-field').find('option:selected').text());
				return result;
			},
			image: function ($widget)
			{
				var result = '';
				var width = $widget.find('input.builder-image-widget-image-width-field').val();
				var height = $widget.find('input.builder-image-widget-image-height-field').val();
				var desc = $widget.find('input.builder-image-widget-description-field').val();
				var url = $widget.find('input.builder-image-widget-url-field').val();

				width === '' ? width = $widget.find('input.builder-image-widget-image-crop-width-field').val() : '';
				height === '' ? height = $widget.find('input.builder-image-widget-image-crop-height-field').val() : '';

				result += (width.length ? 'W: ' + width : '') + ' ' + (height.length ? 'H: ' + height : '') + ' ' + ((desc.length ? (desc + ' ') : '') + ' ' + (url.length ? ('- ' + url) : ''));

				// console.log('result:' + result);

				return result;
			},

			video: function ($widget)
			{
				return this.width_height($widget);
			},

			width_height: function ($widget)
			{
				var result = '';
				var width = $widget.find('input.builder-widget-width-field').val() || '';
				var height = $widget.find('input.builder-widget-height-field').val() || '';

				result += (width.length ? 'W: ' + width : '') + ' ' + (height.length ? 'H: ' + height : '');

				return result;
			},

			googlemap: function ($widget)
			{
				var result = '';
				result += ($widget.find('input.builder-googlemap-widget-address-field').val() + ' ' + $widget.find('select.builder-googlemap-widget-view-field').find('option:selected').text());
				return result;	
			},

			template: function ($widget)
			{
				var template = $widget.find('select.builder-template-widget-template-field').find('option:selected').text() || '';
				! template.length ? template = 'No template selected' : '';
				
				return template;
			}
		},
	}

	function get_widget_title (widget_name, $widget)
	{
		// console.log('get widget title: ' + widget_name + ' ' + (WIDGET_UPDATE.TITLE[widget_name] ? 'yes' : ''));
		return WIDGET_UPDATE.TITLE[widget_name] ? WIDGET_UPDATE.TITLE[widget_name]($widget) : '';
	}

	function update_widget_title ($widget)
	{
		var $title = $widget.find('.builder-widget-bar-info .builder-widget-title').eq(0);
		var widget = $widget.cattr('builder-widget-type');
		var title = get_widget_title(widget, $widget);

		if (title && title.length)
		{
			$title.text(title);
		}
	}

	function get_widget_preview (widget_name, $widget)
	{
		// console.log('get widget preview: ' + widget_name  + ' ' + (WIDGET_UPDATE.PREVIEW[widget_name] ? 'yes' : ''));
		return WIDGET_UPDATE.PREVIEW[widget_name] ? WIDGET_UPDATE.PREVIEW[widget_name]($widget) : '';
	}

	function update_widget_preview ($widget)
	{
		var $preview = $widget.find('.builder-widget-location-preview').eq(0);
		var widget = $widget.cattr('builder-widget-type');

		get_widget_preview(widget, $widget);
	}

	function get_widget_summary (widget_name, $widget)
	{
		// console.log('get widget summary: ' + widget_name + ' ' + (WIDGET_UPDATE.SUMMARY[widget_name] ? 'yes' : ''));
		return WIDGET_UPDATE.SUMMARY[widget_name] ? WIDGET_UPDATE.SUMMARY[widget_name]($widget) : '';
	}

	function update_widget_summary ($widget)
	{
		var $summary = $widget.find('.builder-widget-summary');
		var widget = $widget.cattr('builder-widget-type');
		var summary = get_widget_summary(widget, $widget);

		if (summary && summary.length)
		{
			$summary.show().text(summary);
		} else
		{
			$summary.hide();
		}
	}

	function get_widget_icon (widget_name, $widget)
	{
		// console.log('get widget icon: ' + widget_name + ' ' + (WIDGET_UPDATE.ICON[widget_name] ? 'yes' : ''));
		return WIDGET_UPDATE.ICON[widget_name] ? WIDGET_UPDATE.ICON[widget_name]($widget) : '';
	}

	function update_widget_icon ($widget)
	{
		var widget = $widget.cattr('builder-widget-type');

		get_widget_icon(widget, $widget);
	}

	function update_widget_excerpt($widget)
	{
		var excerpt = '';

		if ( ! $widget.hasClass('builder-widget-core'))
		{
			$widget.find('input[type=text], textarea').each( function()
			{
				var val = $(this).val();

				if (val.length > 0)
				{
					excerpt += ' ' + val;
				}
			});
		}

		if (excerpt.length > 0)
		{
			excerpt = excerpt.replace(/<\/?[^>]+>/gi, '');
		}

		if (excerpt.length > 30)
		{
			excerpt = excerpt.substring(0, 30) + '...';
		}

		excerpt = $.trim(excerpt);

		$widget.find('.builder-widget-excerpt').text(excerpt);

		if (excerpt.length > 0)
		{
			$widget.find('.builder-widget-excerpt').show();
		} else
		{
			$widget.find('.builder-widget-excerpt').hide();
		}
	}

	function update_widget ($widget)
	{
		update_widget_icon($widget);
		update_widget_title($widget);
		update_widget_summary($widget);
		update_widget_excerpt($widget);
		update_widget_preview($widget);
	}

	function update_widget_visibility ($widget, state, limit)
	{
		$widget.hasClass('builder-widget') ? $widget = $widget.parent() : '';
		limit = limit || 1;

		$widget.toggleClass('builder-hidden-widget', ! state);
		$widget.find('.builder-widget-visibility-field').each(function ()
		{
			if (limit <= 0)
				return;

			limit -= 1;
			$(this).attr('checked', ! state);
		});
	}

	function update_widget_alignment ($widget, alignment)
	{
		$widget.hasClass('builder-widget') ? $widget = $widget.parent() : '';
		$widget
			.attr('class', $widget.attr('class').replace(/\bbuilder-align\w+\b/, ''))
			.addClass('builder-align' + alignment)
	}

	function update_widget_width ($widget, width)
	{
		$widget.hasClass('builder-widget') ? $widget = $widget.parent() : '';
		width.match(/(\d*)(.*)/)[2] === '' ? width += 'px' : '';
		$widget.css('width', width);
	}

	function update_widget_clearfloat ($widget, state)
	{
		$widget.hasClass('builder-widget') ? $widget = $widget.parent() : '';
		$widget.toggleClass('builder-clearfloat-indicator', state);
	}

	function update_button_widget_live_preview ($widget, prop, value)
	{
		var $preview;
		var class_name;

		$widget.hasClass('builder-widget') ? $widget = $widget.parent() : '';

		if ( ! $widget.hasClass('builder-widget-type-button'))
			return;

		typeof prop === 'string' ? prop = [prop] : '';
		typeof value === 'string' ? value = [value] : '';

		$preview = $widget.find('.ether-button-preview');

		if ( ! $preview)
			return;

		prop.forEach(function (p, p_id)
		{
			var v = value[p_id];

			switch (p)
			{
				case 'align':
				{
					$preview.attr('class', $preview.attr('class').replace(/builder-align\w+/, ''))
					$preview.addClass('builder-align' + v.toLowerCase());
					break;
				}
				case 'width':
				{
					$preview.css('width', v);
					break;
				}
				case 'label':
				{
					$preview.text(v);
					break;
				}
				case 'style':
				{
					// console.log($preview.attr('class').match(/ether-button-style-\w+/, ''), v)
					$preview.attr('class', $preview.attr('class').replace(/ether-button-style-\w+/, ''))
					$preview.addClass('ether-button-style-' + v.toLowerCase());
					break;
				}
				case 'background':
				{
					$preview.css('background-color', v);
					break;
				}
				case 'color':
				{
					$preview.css('color', v);
					break;
				}
			}
		});
	}

	function filter_social()
	{
		var filter = $(this).val();
		var $parent = $(this).closest('.cols-2');

		if (filter != '')
		{
			$parent.children('.cols-2').children('div.col').filter(':not(:icontains(' + filter + '))').hide();
			$parent.children('.cols-2').children('div.col').filter(':icontains(' + filter + ')').show();
		} else
		{
			$parent.children('.cols-2').children('div.col').show();
		}
	}

	function filter_widgets()
	{
		var filter = $(this).val().toLowerCase();

		var $parent = $(this).closest('#builder-widgets');

		if (filter != '')
		{
			$parent.find('.builder-widget-wrapper').filter( function(index)
			{
				var data = $(this).find('.builder-widget-title').text().toLowerCase() + ' ' + $(this).find('.builder-widget-label').text().toLowerCase();

				return data.indexOf(filter) < 0;
			}).hide();

			$parent.find('.builder-widget-wrapper').filter( function(index)
			{
				var data = $(this).find('.builder-widget-title').text().toLowerCase() + ' ' + $(this).find('.builder-widget-label').text().toLowerCase();

				return data.indexOf(filter) >= 0;
			}).show();
		} else
		{
			$parent.find('.builder-widget-wrapper').show();
		}
	}

	function init_richtext($textarea, force)
	{
		if (typeof force == 'undefined')
		{
			force = false;
		}

		var id = $textarea.attr('id');

		if (typeof tinyMCEPreInit != 'undefined' && ! window.builder_tinymce)
		{
			// workaround for wp-includes/js/tinymce/langs/en.js 404 error
			if (typeof tinyMCEPreInit.mceInit.etherbuildereditorfix != 'undefined')
			{
				tinyMCE.init(tinyMCEPreInit.mceInit.etherbuildereditorfix);
			} else if (typeof tinyMCEPreInit.mceInit.content != 'undefined')
			{
				tinyMCE.init(tinyMCEPreInit.mceInit.content);
			} else if (typeof tinyMCEPreInit.mceInit[''] != 'undefined')
			{
				tinyMCE.init(tinyMCEPreInit.mceInit['']);
			}

			window.builder_tinymce = true;
		}

		if (typeof tinyMCE != 'undefined' || force)
		{
			tinyMCE.execCommand('mceAddEditor', false, id);
		}

	}

	function destroy_richtext($textarea, force)
	{
		if (typeof force == 'undefined')
		{
			force = false;
		}

		var id = $textarea.attr('id');

		// wordpress 3.9
		if (typeof tinyMCE.getInstanceById == 'undefined')
		{
			tinyMCE.getInstanceById = function(id)
			{
				var instance = this.get(id);

				if (instance == null)
				{
					return undefined;
				}

				return instance;
			};
		}

		if (typeof tinyMCE != 'undefined' && typeof tinyMCE.getInstanceById(id) != 'undefined')
		{
			tinyMCE.execCommand('mceFocus', false, id);

			tinyMCE.triggerSave();
			//$textarea.val(tinyMCE.getInstanceById(id).getContent());
			__RICH_CONTENT__ = tinyMCE.getInstanceById(id).getContent();

			if ($('.widgets-holder-wrap').length > 0 || force)
			{
    			tinyMCE.execCommand('mceRemoveEditor', false, id);
    			//tinyMCE.execCommand('mceRemoveEditor', false, id);
    		}
    	}
	}

	var ID_LIST = {};

	function get_unique_id(id_list)
	{
		var id = new Date().getTime() + Math.floor(Math.random() * 999999999);
		if ( ! id_list[id])
		{
			id_list[id] = id;
			return id;
		} else
		{
			return get_unique_id(id_list);
		}
	}

	var get_widget_id_list = function ()
	{
		var ID_LIST = {};

		$data = get_widget_data($('#builder-location-main .builder-widget-wrapper'), true);

		$data.each(function ()
		{
			var id;
			$name = $(this).attr('name');

			id = $name.match(/((\[.*?\]){3})(\[(.*?)\])/)[4];

			if ( ! ID_LIST[id])
			{
				ID_LIST[id] = id;
			}
		});

		return ID_LIST;
	}

	function update_widget_data(element)
	{
		//console.log('update widget data')
		var $data = element.find('input[name^=ether_builder_widget], select[name^=ether_builder_widget], textarea[name^=ether_builder_widget], button[name^=ether_builder_widget]');

		var NEW_ID = get_unique_id(ID_LIST);

		$data.each( function()
		{
			var attrs = { 'name': 'name', 'id': 'id' };

			for (var attr in attrs)
			{
				if (typeof $(this).attr(attr) != 'undefined' && $(this).attr(attr) !== false)
				{
					var data = 'ether_builder_widget[__LOCATION__][__ROW__][__COLUMN__][__ID__][__SLUG__]';
					var current_data = /\[(.*?)\]\[(.*?)\]\[(.*?)\]\[(.*?)\]\[(.*?)\]/.exec($(this).attr(attr));

					if (current_data != null)
					{
						var is_array = false;

						if ($(this).attr(attr).length > 2)
						{
							is_array = ($(this).attr(attr).substring($(this).attr(attr).length - 2) == '[]');
						}

						if (current_data.length == 6)
						{
							data = data.replace(/__SLUG__/g, current_data[5]);
						}

						if (current_data.length == 6 && current_data[4] != '__ID__')
						{
							data = data.replace(/__ID__/g, current_data[4]);
						} else
						{
							data = data.replace(/__ID__/g, NEW_ID);
						}

						var $row = $(this).parents('div[class*=builder-widget-type-row]');

						if ($row.length > 0)
						{
							data = data.replace(/__ROW__/g, $row.index());

							var $column = $(this).parents('div.builder-widget-column');

							if ($column.length > 0)
							{
								data = data.replace(/__COLUMN__/g, $column.index());
							}
						} else
						{
							data = data.replace(/__ROW__/g, $(this).parents('.builder-widget-wrapper').index());
						}

						var $location = $(this).parents('.builder-location-wrapper:not(.read-only) .builder-location');

						if ($location.length > 0)
						{
							data = data.replace(/__LOCATION__/g, $location.attr('id').replace('builder-location-', ''));
						}

						$(this).attr(attr, data + (is_array ? '[]' : ''));
					}
				}
			}
		});

		$(this).addClass('initialized');

		widget_debug.debug_id();
	}

	function get_widget_data($elem, deep)
	{
		var $settings;
		var $location;
		var $data;

		if (deep === true)
		{
			$settings = $elem.find('input[name^=ether_builder_widget], select[name^=ether_builder_widget], textarea[name^=ether_builder_widget], button[name^=ether_builder_widget]');
			$location = [];
		} else
		{
			$settings = $elem.find('.builder-widget-content-form').eq(0).find('input[name^=ether_builder_widget], select[name^=ether_builder_widget], textarea[name^=ether_builder_widget], button[name^=ether_builder_widget]');
			$location = $elem.find('.builder-widget').eq(0).find('input[name^=ether_builder_widget]');
		}
		var $data = $.merge($settings, $location);

		return $data;
	}

	function is_top_level($elem)
	{
		if ($elem.hasClass('builder-widget-core') || $elem.parent('.builder-location').length > 0)
		{
			return true;
		} else
		{
			return false;
		}
	}

	function update_widget_col_order($elem)
	{
		//console.log('update widget col order')
		var id = $elem.parent('.builder-widget-column').length > 0 ? $elem.parent('.builder-widget-column').index() : '__COLUMN__';

		$elem.each(function ()
		{
			var $data = get_widget_data($(this));

			$data.each(function ()
			{
				$name = $(this).attr('name');
				$(this).attr('name', $name.replace(/((\[.*?\]){2})(\[(.*?)\])/, '$1[' + id + ']'));
			});
		});

		widget_debug.debug_id();
	}

	function update_widget_row_order($elem)
	{
		//console.log('update widget row order')
		var row_id;

		//work only on this elem if it's not top level
		if ( ! is_top_level($elem.eq(0)))
		{
			$elem = $elem.eq(0);
			row_id = $elem.parents('.builder-widget-core').index();
		} else
		{
			row_id = $elem.eq(0).index();
		}

		$elem.each(function (elem_id)
		{
			var top_level_elem = is_top_level($(this));
			var $data = get_widget_data($(this), true);

			if (top_level_elem && elem_id !== 0)
			{
				row_id += 1;
			}

			$data.each(function ()
			{
				$name = $(this).attr('name');
				$(this).attr('name', $name.replace(/((\[.*?\]){1})(\[(.*?)\])/, '$1[' + row_id + ']'));
			});
			//row_id += 1
		})

		widget_debug.debug_id();
	}


	function make_duplicate($elem, update_widget_data, append_widget_to_dom)
	{
		var $clone;

		//clone does not get through sortable destroy properly so destroy it on $elem element before doing anything else
		$elem.find('.ui-sortable').sortable().sortable('destroy');
		$elem.find('.group-content').children().sortable().sortable('destroy');

		//check if a widget contains any other widgets and if so match all widgets
		if ($elem.find('.builder-widget-wrapper').length > 0)
		{
			$elem = $.merge($elem, $elem.find('.builder-widget-wrapper'))
		}

		//tinyMCE editor must be entirely destroyed before making a clone, so if there is one, uninit it from $elem first
		$elem.each(function ()
		{
			var $textarea;
			var textarea_id;

			//this affects rich text widget only and not rich texts within group items within services, testimonials etc.
			if ($(this).hasClass('builder-widget-type-rich-text'))
			{
				$textarea = $(this).find('textarea');
				textarea_id = $textarea.attr('id');

				if ($textarea.length == 1 && ($textarea.hasClass('tinymce') || $('.widgets-holder-wrap').length > 0))
				{
					destroy_richtext($textarea);
					tinyMCE.execCommand('mceRemoveEditor', false, textarea_id);
				}
			}
		});

		//now we can clone safely
		$clone = $elem.eq(0).clone(true, true);
		$clone = $.merge($clone, $clone.find('.builder-widget-wrapper'));

		if (update_widget_data === true)
		{
			//console.log('update widget data #2')
			$clone.each(function ()
			{
				var $data = get_widget_data($(this));
				var NEW_ID = get_unique_id(ID_LIST);
				var $textarea;
				var textarea_id;

				$data.each(function ()
				{
					$name = $(this).attr('name');
					$(this).attr('name', $name.replace(/((\[.*?\]){3})(\[(.*?)\])/, '$1[' + NEW_ID + ']'));
				});

				if ($(this).hasClass('builder-widget-type-rich-text'))
				{
					$textarea = $(this).find('textarea');
					textarea_id = $textarea.attr('id');

					if ($textarea.length == 1 && ($textarea.hasClass('tinymce') || $('.widgets-holder-wrap').length > 0))
					{
						$textarea.attr('id', $textarea.attr('name'));
					}
				}
			});
		}

		$.merge($elem.eq(0), $clone.eq(0)).find('.group-content').children().sortable({
			handle: '.group-item-title',
			appendTo: 'parent',
			tolerance: 'pointer',
			delay: 100,
			forceHelperSize: true,
			start: function (evt, ui)
			{
				ui.helper.css({width:ui.item.width() + 60});
				ui.placeholder.css({height: ui.item.children().eq(0).height() + 32});
			},
		});

		if (append_widget_to_dom === true)
		{
			$clone.eq(0).insertAfter($elem.eq(0)).hide().slideDown(500).queue(function ()
			{
				$(this)
					.css('display', 'block')
					.dequeue();
			});

			$clone.eq(0).find('.builder-widget-row-options').hide();

			update_widget_row_order($elem.eq(0).nextAll());

			$('.builder-location-wrapper:not(.read-only) .builder-location, .builder-location-wrapper:not(.read-only) .builder-location .builder-widget-row > div.builder-widget-column').each(make_sortable);
		} else
		{
			return $clone.eq(0);
		}

		widget_debug.debug_id();
	}

	ether.enable_widget_debug = false;

	var widget_debug =
	{
		get_widget_fields_id: function ($widget)
		{
			var ID;
			var id_mismatch;
			var fields_data = [];

			var $fields_wrap = $widget.children('.builder-widget-content') //make explicit selection to avoid matching too many elements
			var $fields = $fields_wrap
				.find('input').add($fields_wrap.find('textarea')).add($fields_wrap.find('select'))
				.add($widget.children('input')).add($widget.children('textarea')).add($widget.children('select'))

			$fields.each(function ()
			{
				var name = $(this).attr('name');
				var id = name.match(/(?:\[.*?\]){3}\[(\d+)\]/)[1];

				// var id = name.match(/\[(\d+)\]\[[\w-]+\]$/); //doesn't work on testimonial_author due to an extra []
				// console.log(name, id, $(this));
				// id !== null ? id = id[1] : '';

				fields_data.push([id, name]);

				if ( ! ID)
				{
					ID = id;
				} else if (id !== id)
				{
					console.error('Incorrect field id: ' + id + '; should be: ' + ID);
					id_mismatch = true;
					return;
				}
			});

			! id_mismatch ? console.log('Field IDs ok: ' + $fields.length + 'x' + ID) : console.error('Field IDs mismatch', fields_data, $(this));

			return ID;
		},

		update_widget_debug_info: function ($widget, info, duplicate_id)
		{
			$widget.hasClass('.builder-widget-wrapper') ? $widget = $widget.children('.builder-widget').eq(0) : '';

			var $widget_top_bar = $widget.children('.builder-widget-bar').eq(0);
			var $debug_name_attr = $widget_top_bar.eq(0).children('.debug-name-attr').eq(0);

			$debug_name_attr.length > 0 ? $debug_name_attr.text(info) : $widget_top_bar.prepend('<div class="debug-name-attr">' + info + '</div>');

			if (duplicate_id)
			{
				$widget_top_bar.children('.debug-name-attr').eq(0).attr('style', 'background: red !important;');
			}
		},

		debug_id: function ()
		{
			var self = this;
			var ID = [];

			if ( ! ether.enable_widget_debug)
			{
				return false;
			}

			$('.builder-location .builder-widget').each(function ()
			{
				var duplicate_id;
				var id = self.get_widget_fields_id($(this));

				if ( $.inArray(id, ID) !== -1 )
				{
					duplicate_id = true;
				}

				self.update_widget_debug_info($(this), id, duplicate_id);
			});
		}
	}


/*
	$(window).on('click', function ()
	{
		$('.builder-location .builder-widget').each(function ()
		{
			var name = $(this).find('*[name*="ether_builder_widget"]').eq(0).attr('name');
			var re = new RegExp('\\[\\S*\\]*');
			name = name.match(re).join('');

			var $bar = $(this).children('.builder-widget-bar').eq(0);
			var $debug_name_attr = $bar.find('.debug-name-attr');

			$debug_name_attr.length > 0 ? $debug_name_attr.text(name) : $bar.prepend('<div class="debug-name-attr">' + name + '</div>');
		});
	});
*/
	/*function make_sortable()
	{
		$(this).sortable
		({
			handle: '.builder-widget-bar',
			connectWith: '.builder-widget-row > div.builder-widget-column, .builder-location'
		});
	}

	$('.builder-location, .builder-widget-row > div.builder-widget-column').each(make_sortable);

	$('#builder-widgets .builder-widget-wrapper').draggable
	({
		connectToSortable: '.builder-location.ui-sortable, .builder-widget-row > div.builder-widget-column.ui-sortable',
		helper: 'clone',
		stop: function(e, ui)
		{
			$('.builder-location, .builder-widget-row > div.builder-widget-column').each(make_sortable);
		}
	});*/

	// code above should work, but it doesnt, sometimes creates duplicated placeholders and appends elements twice
	// so..... HACKORING BEGIN
	// needs optimising?


	function make_sortable()
	{
		$(this).sortable().sortable('destroy');

		$(this).sortable
		({
			handle: '.builder-widget > .builder-widget-bar',
			connectWith: '.builder-location-wrapper:not(.read-only) .builder-location, .builder-location-wrapper:not(.read-only) .builder-location .builder-widget-row > div.builder-widget-column',
			appendTo: 'body',
			distance: 40,
			forceHelperSize: true,
			tolerance: 'pointer',
			helper: function(e, ui)
			{
				if (ui.hasClass('builder-widget-core'))
				{
					$('.builder-location-wrapper:not(.read-only) .builder-location .builder-widget-row > div.builder-widget-column').each( function()
					{
						$(this).sortable().sortable('disable');
					});
				}

				$('.builder-location-wrapper:not(.read-only) .builder-location, .builder-location-wrapper:not(.read-only) .builder-location .builder-widget-row > div.builder-widget-column').sortable().sortable('refresh');

				return ui;
				//return make_clonable(e, ui);
			},
			start: function (e, ui)
			{
				ether.widget_drag = true;
				ui.item.attr('data-top-level', is_top_level(ui.item));
				ui.item.attr('data-prev-id', ui.item.index());
			},
			beforeStop: function(e, ui)
			{
				if ($(ui.helper).hasClass('builder-widget-core'))
				{
					$('.builder-location-wrapper:not(.read-only) .builder-location .builder-widget-row > div.builder-widget-column').each( function()
					{
						$(this).sortable().sortable('enable');
						$(this).sortable().sortable('refresh');
					});
				}
			},
			stop: function(e, ui)
			{
				var prev_id = parseInt(ui.item.attr('data-prev-id'), 10);
				var was_top = ui.item.attr('data-top-level') === 'true' ? true : false;
				var is_top = is_top_level(ui.item);
				var reorder_start_id = null;
				var reorder_self = null;
				var reorder_col = null;

				if (was_top === true && is_top === true)
				{
					reorder_start_id = Math.min(prev_id, ui.item.index());
				} else if (was_top === true && is_top === false)
				{
					reorder_start_id = prev_id;
					reorder_self = true;
					reorder_col = true;
				} else if (was_top === false && is_top === true)
				{
					reorder_start_id = ui.item.index();
					reorder_self = true;
					reorder_col = true;
				} else if (was_top === false && is_top === false)
				{
					reorder_self = true;
					reorder_col = true;
				}

				if (reorder_start_id !== null)
				{
					update_widget_row_order($('.builder-location > .builder-widget-wrapper').slice(reorder_start_id));
				}

				if (reorder_self === true)
				{
					update_widget_row_order(ui.item);
				}

				if (reorder_col === true)
				{
					update_widget_col_order(ui.item);
				}

				ui.item.attr('data-prev-id', '');
				ui.item.attr('data-top-level', '')

				$('.builder-location-wrapper:not(.read-only) .builder-location, .builder-location-wrapper:not(.read-only) .builder-location .builder-widget-row > div.builder-widget-column').each( function()
				{
					$(this).sortable().sortable('enable');
					$(this).sortable().sortable('refresh');
				});

				ether.widget_drag = false;
			},
			out: function(e, ui)
			{
				/*if ($(ui.placeholder).parent().hasClass('builder-widget-column'))
				{
					$('.builder-location-wrapper:not(.read-only) .builder-location').each( function()
					{
						$(this).sortable('enable');
						$(this).sortable('refresh');
					});
				}*/
			},
			over: function(e, ui)
			{
				/*if ($(ui.placeholder).parent().hasClass('builder-widget-column'))
				{
					$('.builder-location-wrapper:not(.read-only) .builder-location').each( function()
					{
						$(this).sortable('disable');
					});
				} else
				{
					$(this).sortable('enable');
				}*/

				$('.builder-location-wrapper:not(.read-only) .builder-location, .builder-location-wrapper:not(.read-only) .builder-location .builder-widget-row > div.builder-widget-column').each( function()
				{
					$(this).sortable().sortable('refresh');
				});
			},
			update: function (e, ui)
			{
				//if (ui.item.css('display', 'none'))
				//{
				//	ui.item.css('display', 'block');
				//}
			}
		});
	}

	function make_clonable(e, ui)
	{
		var copy = ui.clone().insertAfter(ui);

		$('.builder-location-wrapper:not(.read-only) .builder-location, .builder-location-wrapper:not(.read-only) .builder-location .builder-widget-row > div.builder-widget-column').each(make_sortable);

		if (copy.hasClass('builder-widget-core'))
		{
			$('.builder-location-wrapper:not(.read-only) .builder-location .builder-widget-row > div.builder-widget-column').each( function()
			{
				$(this).sortable().sortable('disable');
			});
		}

		$('#builder-widgets').sortable().sortable('refresh');

		return ui.clone();
	}

	var on_builder_widget_modal_open = function ($widget)
	{
		var $textarea = null;
		var $group_content_wrap = null;
		var group_content_wrap_length = null;

		//this was used only for widget modals before. now widget select modal uses this function too
		ether.$current_builder_widget_modal = $widget;

		if ($widget.attr('id') !== 'builder-widgets')
		{
			ether.set_builder_widget_tabs($widget);
			ether.set_dynamic_label($widget);
			ether.init_cond_groups({$scope: $widget.find('.builder-widget-content-form .ether-form')});

			$group_content_wrap = $widget.find('.group-content-wrap');
			$group_content_wrap.each(function ()
			{
				if ($(this).find('.group-content').children('.cols').children().length > 0)
				{
					$(this).find('.buttonset-1').eq(1).show();
				} else
				{
					$(this).find('.buttonset-1').eq(1).hide();
				}
			});


			ether.$current_builder_widget_modal_clone = make_duplicate($widget);

			if ($widget.hasClass('builder-widget-type-rich-text'))
			{
				$textarea = $widget.find('textarea');

				if ($textarea.length == 1 && ($textarea.hasClass('tinymce') || $('.widgets-holder-wrap').length > 0))
				{
					init_richtext($textarea);
				}
			}
		}
	}

	var on_builder_widget_modal_close = function ()
	{
		delete ether.$current_builder_widget_modal;
	}

	ether.cancel_builder_modal = function (evt)
	{
		var $modal = ether.$current_builder_widget_modal;
		var $modal_clone = ether.$current_builder_widget_modal_clone;

		if ( ! ether.$current_builder_widget_modal)
		{
			return false;
		}

		if ($modal.attr('id') === 'builder-widgets')
		{
			$modal.hide();
			$('#builder-widget-overlay').stop(true, true).fadeTo('fast', 0.0, function() { $(this).hide(); });
			on_builder_widget_modal_close();
		} else
		{
			if ($modal.find('.builder-widget-inner:visible .builder-widget-rich-form').length > 0)
			{
				if (confirm(ether.builder_lang.changes))
				{
					var $textarea = $modal.find('.builder-widget-inner:visible #builder-rich-content');
					destroy_richtext($textarea, true);

					$modal.find('.builder-widget-inner:visible .builder-widget-rich-form').remove();
					$modal.find('.builder-widget-inner:visible .builder-widget-content-form').show();
				}
			} else
			{
				if (confirm(ether.builder_lang.quit))
				{
					if ( ! $modal.hasClass('ether-virgin'))
					{
						$modal_clone.insertAfter($modal);
						$modal.find('.save').click();
						ether.remove_widget($modal, true);
						//$modal.find('.save').click();
					} else
					{
						$modal.find('.save').click();
						ether.remove_widget($modal, true);
						//ether.remove_widget($modal_clone, true);
					}

					on_builder_widget_modal_close();
				}
			}
		}

		if (evt)
		{
			evt.preventDefault();
		}

		return false;
	}

	set_builder_location_width = function ()
	{
		//need this or widgets with explicitly set width overflow and break layout; 
		//max-width / width combination don't help

		var width = $('#builder-location-wrapper').width() - 4; 

		$('.builder-location').each(function ()
		{
			$(this).width(width);
		});
	}

	ether.remove_widget = function ($widget, force)
	{
		var $next;

		if (force === true)
		{
			$next = $widget.eq(0).nextAll();
			ether.remove_cond_data.call($widget);
			$widget.remove();
			update_widget_row_order($next);
			$('#builder-widget-overlay').stop(true, true).fadeTo('fast', 0.0, function() { $(this).hide(); });
		} else
		{
			if (confirm(ether.builder_lang.sure))
			{
				$next = $widget.eq(0).nextAll();
				ether.remove_cond_data.call($widget);
				$widget.remove();
				update_widget_row_order($next);
				$('#builder-widget-overlay').stop(true, true).fadeTo('fast', 0.0, function() { $(this).hide(); });
			}
		}

		return false;
	}

	ether.recursively_show = function ($elem)
	{
		$elem.addClass('dynamic-label-guard');

		if ($elem.parent().is(':hidden'))
		{
			ether.recursively_show($elem.parent());
		}
	}

	ether.remove_dynamic_label_guard = function ($elem)
	{
		$elem.removeClass('dynamic-label-guard').find('.dynamic-label-guard').removeClass('dynamic-label-guard');
	}

	ether.set_dynamic_label = function ($widget)
	{
		var $label;

		if ( ! $widget )
		{
			if (ether && ether.$current_builder_widget_modal)
			{
				$widget = ether.$current_builder_widget_modal;
			} else
			{
				//$label = $('label');
			}
		}

		//careful with the scope though
		//$label = $widget.find('.ether-form label');
		$label = $widget.find('label');

		if ($label.hasClass('label-dynamic'))
		{
			$label.each(function ()
			{
				var $field = $(this).children('input:not(input[type="checkbox"]):not(input[type="radio"]), select');
				$field.each(function ()
				{
					$(this).css('width', '');
				});
			});
		}

		$label.each(function ()
		{
			var $field = $(this).children('input:not(input[type="checkbox"]):not(input[type="radio"]), select');
			var $tooltip = $(this).children('small');
			var $label_title = $(this).children('.label-title');
			var $tooltip_handle;
			var label_w = $(this).width();

			$(this).addClass('label-dynamic');

			if ($(this).children('input[type="checkbox"], input[type="radio"]').length > 0)
			{
				$label_title
					.prepend($(this).children('input[type="checkbox"], input[type="radio"]'))
					.addClass('expanded-title');
			}

			if (label_w === 0)
			{
				ether.recursively_show($(this));
				label_w = $(this).width();
			}

			if ($tooltip.length !== 0)
			{
				$tooltip
					.css({'top': 0, 'bottom': 'auto'})
					.addClass('label-tooltip')
					.css({
						'left': $label_title.outerWidth() - $tooltip.width() / 2
					});

				if ($label_title.children('.label-tooltip-handle').length === 0)
				{
					$tooltip_handle = $('<div class="label-tooltip-handle"></div>');

					$label_title.prepend($tooltip, $tooltip_handle);

				}
			}


			if($field.length !== 0)
			{
				$field.css('width', ($(this).width() - $label_title.outerWidth()));
				//$field.width($(this).width() - $label_title.outerWidth());
			}

			ether.remove_dynamic_label_guard($widget);
		});

		if ( ! ether.dynamic_label_tooltip_live_events)
		{
			ether.dynamic_label_tooltip_live_events = true;

			$('.ether-form .label-tooltip-handle').live('mouseenter', function ()
			{
				var $parent = $(this).parents('.builder-widget-inner').eq(0);
				var $tooltip = $(this).siblings('.label-tooltip');

				$tooltip.css({'display': 'block', opacity: 0, 'top': 0});

				if ($parent.length > 0)
				{
					var min_x = $parent.offset().left
					var max_x = $parent.offset().left + $parent.outerWidth();
					var shift_x = 0;
					var min_y = $parent.offset().top;
					var max_y = $parent.offset().top + $parent.height();
					var shift_y = $tooltip.outerHeight();

					if ($tooltip.offset().left < min_x)
					{
						shift_x = min_x - $tooltip.offset().left + 8;
					} else if ($tooltip.offset().left + $tooltip.outerWidth() > max_x)
					{
						shift_x = max_x - ($tooltip.offset().left + $tooltip.outerWidth() + 30);
					}

					if ($tooltip.offset().top - $tooltip.outerHeight() < min_y)
					{
						shift_y = $tooltip.offset().top - min_y - 10;
					}
				}

				if (shift_x !== 0)
				{
					$tooltip.css({'left': $tooltip.position().left + shift_x});
				}

				$tooltip
					.stop(true, true)
					.delay(250)
					.animate({
						'top': -shift_y,
						'opacity': 1
					}, 250);
			}).live('mouseleave', function ()
			{
				var $tooltip = $(this).siblings('.label-tooltip');

				$tooltip
					.stop(true, true)
					.delay(250)
					.animate({
						'top': -$tooltip.outerHeight() * 2,
						'opacity': 0
					}, 250, function ()
					{
						$(this).css({'display': 'none'});
					});
			});
		}
	}

	// ether.js has its own version of this function
	// i felt like this kind of feature should be available widely across all ether plugins

	ether.set_builder_widget_tabs = function ($widget)
	{
		var $widget_content;

		$widget_content = $widget.children('.builder-widget').children('.builder-widget-content');

		if ($widget.attr('data-ether-tabs') && $widget.attr('data-ether-tabs') === 'set')
		{
			return false;
		}

		if ( ! ether.tab_title_live_events)
		{
			$('.ether-tabs .ether-tab-title').live('click', function ()
			{
				var window_pos_y = $(window).scrollTop();
				var $tab_content;
				var id = $(this).index();

				if ($(this).closest('.builder-widget-wrapper').length === 0)
				{
					$tab_content = $(this).closest('.ether-tabs').eq(0).find('.ether-tab-content');
				} else
				{
					$tab_content = $(this).closest('.builder-widget-wrapper').find('.ether-tabs').eq(1).find('.ether-tab-content');
				}

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

				//evt.preventDefault();
			});

			ether.tab_title_live_events = true;
		}

		var $tab_title = $widget_content.find('.ether-tab-title');
		var $tab_content = $widget_content.find('.ether-tab-content');
		var has_visible_tab;
		var $widget_title_bar = $widget_content.find('.builder-widget-content-bar').eq(0); //this is ether builder modal custom var (see notes below);

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

		$tab_title.parent().wrap('<div class="ether-tabs ether-tabs-x ether-tabs-left"></div>');
		$tab_title.parent().parent().appendTo($widget_title_bar);
		$tab_content.parent().wrap('<div class="ether-tabs ether-tabs-x ether-tabs-left"></div>');

		$widget.attr('data-ether-tabs', 'set');
	}

	ID_LIST = get_widget_id_list();

	//ID_LIST keeps track of all widget elements in the #builder-location-main tree
	//unique ids based on this list are later generated via get_unique_id when duplicating / moving or editing widgets in any way to make sure they don't get duplicated and ignored on page update


	// drag drop for widget list
	// replaced by modal box
	/*$('#builder-widgets').sortable
	({
		connectWith: '.builder-location, .builder-widget-row > div.builder-widget-column',
		helper: make_clonable,
		beforeStop: function(e, ui)
		{
			if ($(ui.helper).hasClass('builder-widget-core'))
			{
				$('.builder-widget-row > div.builder-widget-column').each( function()
				{
					$(this).sortable('enable');
				});
			}

			if ($(ui.item).parents('.builder-location').length == 0)
			{
				$(ui.item).remove();
			}
		},
		stop: function(e, ui)
		{
			update_widget_data($('.builder-location-wrapper .builder-widget-wrapper'));
		}
	}).disableSelection();*/

	$('input[name=builder-widget-filter]').live('keyup', filter_widgets).live('change', filter_widgets);
	$('input[name=filter_social]').live('keyup', filter_social).live('change', filter_social);

	$('.group-content').children().sortable({
		handle: '.group-item-title',
		appendTo: 'parent',
		tolerance: 'pointer',
		delay: 100,
		forceHelperSize: true,
		start: function (evt, ui)
		{
			ui.helper.css({width:ui.item.width() + 60});
			ui.placeholder.css({height: ui.item.children().eq(0).height() + 32});
		},
	});

	$('button.builder-widget-group-item-add').live('click', function()
	{
		var $container = $(this).closest('.group-content-wrap');
		var $group_items = $container.find('.group-content').children().eq(0);
		var $first = $container.find('.group-prototype').children().eq(0);
		var $clone = $first.clone();
		var $new = $clone.clone();

		$clone.find('input, textarea, select').val('');
		$clone.find('textarea').text('');
		//$clone.show();

		$new
			.appendTo($group_items)
			.hide()
			.slideDown(500, function () { $(this).css('display','');})

		ether.set_dynamic_label($new);

		if ( ! $group_items.hasClass('ui-sortable') || ! $group_items.hasClass('ui-sortable-refreshed'))
		{
			$group_items.sortable({
				handle: '.group-item-title',
				appendTo: 'parent',
				tolerance: 'pointer',
				delay: 100,
				forceHelperSize: true,
				start: function (evt, ui)
				{
					ui.helper.css({width:ui.item.width() + 60});
					ui.placeholder.css({height: ui.item.children().eq(0).height() + 32});
				},
			});
			$group_items.addClass('ui-sortable-refreshed');
		} else
		{
			$group_items.sortable().sortable('refresh');
		}

		//check_current_indicator($(this));
		update_select_length($(this).parents('fieldset').eq(0).find('select[name*="current"], select[name*="view_pos"]'), $container.find('.group-content').find('.group-item').length);

		//if ($group_items.children().length === 1)
		{
			$container.children('.buttonset-1').eq(1).fadeIn(500);
		}

		return false;
	});

	$('button.builder-widget-group-item-remove').live('click', function()
	{
		if (confirm(ether.builder_lang.sure))
		{
			var $container = $(this).closest('.group-content-wrap');
			var $group_items = $container.find('.group-content').children().eq(0);

			$(this).closest('.col')
				.hide(500)
				.queue(function ()
				{
					//check_current_indicator($(this));
					update_select_length($(this).parents('fieldset').eq(0).find('select[name*="current"], select[name*="view_pos"]'), $container.find('.group-content').find('.group-item').length - 1);

					if ($group_items.children().length === 1)
					{
						$container.find('.buttonset-1').eq(1).fadeOut(250);
					}

					$(this).remove().dequeue();
				});
		}

		return false;
	});

	// holds original textarea object
	var __RICH_TEXTAREA__ = null;
	var __RICH_CONTENT__ = '';

	ether.thumb_size = $('#builder-thumb-size').attr('class');

	$('button.builder-widget-group-item-rich').live('click', function()
	{
		var $form = $(this).closest('.builder-widget-content-form');
		var $textarea;
		var $mediabuttons = $('.wp-editor-tools').eq(0).clone();
		var $editor = $('<div class="builder-widget-content-form builder-widget-rich-form"><div class="wp-editor-wrap"><div class="wp-editor-container"><textarea name="builder-rich-content" id="builder-rich-content" cols="15" class="tinymce"></textarea></div></div></div>');

		$mediabuttons.find('.wp-switch-editor').remove();
		$mediabuttons.children().eq(0).removeAttr('id').removeClass('hide');
		$mediabuttons.find('.add_media').attr('name', 'builder-rich-content');

		$editor.prepend($mediabuttons);

		$form.after($editor);

		__RICH_TEXTAREA__ = $(this).closest('.group-item').find('textarea').eq(0);

		$textarea = $form.next().find('#builder-rich-content');
		$textarea.val(__RICH_TEXTAREA__.val());
		$textarea.text(__RICH_TEXTAREA__.val());

		init_richtext($textarea, true);

		$form.hide();

		return false;
	});

	$('.builder-widget-type-table input[name*="\[rows\]"], .builder-widget-type-table input[name*="\[columns\]"]').live('change', function(e)
	{
		var $parent = $(this).closest('fieldset');
		var $table = $parent.next('fieldset').find('table.table');
		var name = $table.find('input').eq(0).attr('name');

		var rows = parseInt($parent.find('input[name*="\[rows\]"]').val());
		var columns = parseInt($parent.find('input[name*="\[columns\]"]').val());

		if (rows <= 0 || isNaN(rows))
		{
			rows = 1;
		}

		if (rows > 60)
		{
			rows = 60;
		}

		if (columns <= 0 || isNaN(columns))
		{
			columns = 1;
		}

		if (columns > 30)
		{
			columns = 30;
		}

		$parent.find('input[name*="\[rows\]"]').val(rows);
		$parent.find('input[name*="\[columns\]"]').val(columns);

		var table_content = '';

		var _rows = $table.find('tr').length;
		var _columns = $table.find('tr').eq(0).children('td').length;

		if (rows < _rows)
		{
			$table.html($table.find('tr').slice(0, rows));
		} else if (rows > _rows)
		{
			var $clone = $table.find('tr').eq(0).children('td').eq(0).clone();
			$clone.find('input, select, textarea').val('');
			$clone.find('textarea').text('');

			var diff = rows - _rows;

			if (diff > 0)
			{
				for (var i = 0; i < diff; i++)
				{
					var $tr = $('<tr />');

					for (var j = 0; j < _columns; j++)
					{
						$tr.append($clone.clone());
					}

					$table.append($tr);
				}
			}
		}

		if (columns < _columns)
		{
			$table.find('tr').each( function()
			{
				$(this).html($(this).find('td').slice(0, columns));
			});
		} else if (columns > _columns)
		{
			var $clone = $table.find('tr').eq(0).children('td').eq(0).clone();
			$clone.find('input, select, textarea').val('');
			$clone.find('textarea').text('');

			$table.find('tr').each( function()
			{
				var diff = columns - _columns;

				if (diff > 0)
				{
					for (var i = 0; i < diff; i++)
					{
						$(this).append($clone.clone());
					}
				}
			});
		}

		e.preventDefault();

		return false;
	});

	$('.builder-widget-type-pricing-table input[name*="\[rows\]"], .builder-widget-type-pricing-table input[name*="\[columns\]"]').live('change', function(e)
	{
		var $parent = $(this).closest('fieldset');
		var $table = $parent.next('fieldset').find('table.pricing-table-data');
		var $spec_table = $parent.next('fieldset').find('table.pricing-table-header, table.pricing-table-price, table.pricing-table-buttons');
		var name = $table.find('input').eq(0).attr('name');

		var rows = parseInt($parent.find('input[name*="\[rows\]"]').val());
		var columns = parseInt($parent.find('input[name*="\[columns\]"]').val());
		var $highlight = $parent.find('select[name*="\[highlight\]"]').children('option');
		var highlights = parseInt($highlight.length - 1);

		if (rows <= 0 || isNaN(rows))
		{
			rows = 1;
		}

		if (rows > 60)
		{
			rows = 60;
		}

		if (columns <= 0 || isNaN(columns))
		{
			columns = 1;
		}

		if (columns > 10)
		{
			columns = 10;
		}

		$parent.find('input[name*="\[rows\]"]').val(rows);
		$parent.find('input[name*="\[columns\]"]').val(columns);

		var table_content = '';

		var _rows = $table.find('tr:not(:eq(0))').length;
		var _columns = $table.find('tr:not(:eq(0))').eq(0).children('td').length;

		$highlight.not(':eq(0)').remove();

		for (var i = 0; i < columns; i++)
		{
			$highlight.parent().append('<option value="' + (i + 1) + '">' + (i + 1) + '</option>');
		}

		if (rows < _rows)
		{
			$table.html($table.find('tr:not(:eq(0))').slice(0, rows));
		} else if (rows > _rows)
		{
			var $clone = $table.find('tr:not(:eq(0))').eq(0).children('td').eq(0).clone();
			$clone.find('input, select, textarea').val('');
			$clone.find('textarea').text('');

			var diff = rows - _rows;

			if (diff > 0)
			{
				for (var i = 0; i < diff; i++)
				{
					var $tr = $('<tr />');

					for (var j = 0; j < _columns; j++)
					{
						$tr.append($clone.clone());
					}

					$table.append($tr);
				}
			}
		}

		$table.find('th').attr('colspan', columns);
		$spec_table.find('th').attr('colspan', columns);

		if (columns < _columns)
		{
			$table.find('tr:not(:eq(0))').each( function()
			{
				$(this).html($(this).find('td').slice(0, columns));
			});

			$spec_table.each( function()
			{
				$(this).find('tr:not(:eq(0))').each( function()
				{
					$(this).html($(this).find('td').slice(0, columns));
				});
			});
		} else if (columns > _columns)
		{
			var $clone = $table.find('tr:not(:eq(0))').eq(0).children('td').eq(0).clone();
			$clone.find('input, select, textarea').val('');
			$clone.find('textarea').text('');

			$table.find('tr:not(:eq(0))').each( function()
			{
				var diff = columns - _columns;

				if (diff > 0)
				{
					for (var i = 0; i < diff; i++)
					{
						$(this).append($clone.clone());
					}
				}
			});

			$spec_table.each( function()
			{
				var $clone = $(this).find('tr:not(:eq(0))').eq(0).children('td').eq(0).clone();
				$clone.find('input, select, textarea').val('');
				$clone.find('textarea').text('');

				$(this).find('tr:not(:eq(0))').each( function()
				{
					var diff = columns - _columns;

					if (diff > 0)
					{
						for (var i = 0; i < diff; i++)
						{
							$(this).append($clone.clone());
						}
					}
				});
			});
		}
		$parent.next('fieldset').find('td').css({width: 100 / columns + '%'});

		ether.set_dynamic_label($parent.next('fieldset'));

		e.preventDefault();

		return false;
	});

	$('.builder-widget-type-table .save').live('click', function()
	{
		var $parent = $(this).closest('fieldset');
		var $table = $parent.next('fieldset').find('table.table');

		return false;
	});


	//$('.builder-widget-type-rich-text .edit').live('click', function()
	//{
		/*
		var $textarea = $(this).closest('.builder-widget').find('textarea');

		if ($textarea.length == 1 && ($textarea.hasClass('tinymce') || $('.widgets-holder-wrap').length > 0))
		{
			init_richtext($textarea);
		}
		*/
	//});

	$('.builder-widget-type-rich-text .save').live('click', function()
	{
		var $widget = $(this).closest('.builder-widget-wrapper');
		var $textarea = $widget.find('textarea');
		var val;

		if ($textarea.length == 1 && ($textarea.hasClass('tinymce') || $('.widgets-holder-wrap').length > 0))
		{
			val = tinyMCE.get($textarea.attr('id')).getContent();
			//$textarea.text(tinyMCE.get($textarea.attr('id')).getContent())
			//$textarea.val(tinyMCE.activeEditor.getContent())

			destroy_richtext($textarea);

			$textarea.val(val);
			$textarea.text(val);
		}
	});

	$('.builder-widget-actions .edit').live('click', function()
	{
		var $slider = $(this).closest('.builder-widget').find('input[name*="\[slider\]"]');
		var $term = $(this).closest('.builder-widget').find('select[name*="\[term\]"]');
/*
		if ($slider.length > 0)
		{
			var $target = $slider.parent().nextAll('.cols-3');

			if (typeof $slider.attr('checked') != 'undefined' && $slider.attr('checked') == 'checked')
			{
				$target.eq(0).show();
				$target.eq(1).show();
			} else
			{
				$target.eq(0).hide();
				$target.eq(1).hide();
			}
		}
*/
		/*if ($term.length > 0)
		{
			var $self = $term.parent();
			var $target = $term.closest('fieldset');

			if ($term.val() != '')
			{
				$target.next('.sortable-content').hide();
				$self.next('.cols-3').show();
			} else
			{
				$target.next('.sortable-content').show();
				$self.next('.cols-3').hide();
			}
		}*/

		return false;
	});

	$('.builder-widget select[name*="\[term\]"]').live('change', function()
	{
		var $self = $(this).parent();
		var $target = $(this).closest('fieldset');

		if ($(this).val() != '')
		{
			$target.next('.sortable-content').hide();
			$self.next('.cols-3').show();
		} else
		{
			$target.next('.sortable-content').show();
			$self.next('.cols-3').hide();
		}
	});

	$('.builder-widget-type-custom-feed .edit').live('click', function()
	{
		var $parent = $(this).closest('.builder-widget').children('.builder-widget-content');

		var $post_type = $parent.find('select[name*="\[post_type\]"]');
		var $taxonomy = $parent.find('select[name*="\[taxonomy\]"]');
		var $term = $parent.find('select[name*="\[term\]"]');

		var post_type = $post_type.val();
		var taxonomy = $taxonomy.val();
		var term = $term.val();

		$taxonomy.children('option').hide();

		$taxonomy.children('option').each( function()
		{
			if ($(this).attr('value') && $(this).attr('value').substring(0, post_type.length) == post_type)
			{
				$(this).show();
			}
		});

		$taxonomy.children('option').eq(0).show();

		$term.children('option').hide();

		$term.children('option').each( function()
		{
			if ($(this).attr('value').substring(0, taxonomy.length) == taxonomy)
			{
				$(this).show();
			}
		});

		$term.children('option').eq(0).show();
	});

	$('.builder-widget-type-custom-feed select[name*="\[taxonomy\]"]').live('change', function()
	{
		var $self = $(this).parent();
		var $parent = $(this).closest('.cols');

		var $term = $parent.find('select[name*="\[term\]"]');

		$term.val('');

		var taxonomy = $(this).val();

		if (taxonomy == null)
		{
			taxonomy = '';
		}

		$term.children('option').hide();

		if (taxonomy.length > 0)
		{
			$term.children('option').each( function()
			{
				if ($(this).attr('value').substring(0, taxonomy.length) == taxonomy)
				{
					$(this).show();
				}
			});
		}

		$term.children('option').eq(0).show();
	});

	$('.builder-widget-type-custom-feed select[name*="\[post_type\]"]').live('change', function()
	{
		var $self = $(this).parent();
		var $parent = $(this).closest('.cols');

		var $taxonomy = $parent.find('select[name*="\[taxonomy\]"]');
		$taxonomy.val('');

		var $term = $parent.find('select[name*="\[term\]"]');

		$term.val('');

		var post_type = $(this).val();

		if (post_type == null)
		{
			post_type = '';
		}

		$taxonomy.children('option').hide();

		if (post_type.length > 0)
		{
			$taxonomy.children('option').each( function()
			{
				if ($(this).attr('value').substring(0, post_type.length) == post_type)
				{
					$(this).show();
				}
			});
		}

		$taxonomy.children('option').eq(0).show();

		$term.children('option').hide();

		$term.children('option').eq(0).show();
	});

	if (ether.hide_visual_tab || (ether.hide_visual_tab && ether.hide_html_tab))
	{
		ether.builder_tab = true;
	}

	if ($('.postarea').length > 0 && $('#editor-builder-tab').length > 0)
	{
		if ($('#postdivrich').length == 0 || $('#content-html').length == 0)
		{
			if ($('#postdivrich').length > 0 && $('#content-html').length == 0)
			{
				$('.wp-editor-tools').prepend('<a id="content-html" class="hide-if-no-js wp-switch-editor switch-html active">HTML</a>');
			} else
			{
				$('#editor-toolbar').prepend('<div class="zerosize"><input accesskey="e" type="button" /></div> <a id="edButtonHTML" class="hide-if-no-js active">HTML</a>');
			}
		}

		$('.mceIframeContainer iframe').each( function()
		{
			if ($(this).height() < 50)
			{
				$(this).height(300);
			}
		});

		var $tabs = null;

		if ($('.postarea #editor-toolbar').length > 0)
		{
			$tabs = $('.postarea #editor-toolbar');
		} else if ($('.postarea #wp-content-editor-tools').length > 0)
		{
			$tabs = $('.postarea #wp-content-editor-tools');
		}

		if ($tabs.children('.wp-editor-tabs').length > 0)
		{
			$tabs = $tabs.children('.wp-editor-tabs');
		}

		if ($tabs != null)
		{
			$tabs.children('a').eq(0).after($('<a />').attr('id', 'edButtonBuilder').addClass('hide-if-no-js wp-switch-editor').text('Builder').click( function()
			{
				$('.wp-editor-wrap').removeClass('tmce-active html-active active');
				$('.wp-switch-editor').removeClass('active');
				$(this).addClass('active');
				$('#editor-toolbar #edButtonHTML, #editor-toolbar #edButtonPreview, #wp-content-editor-tools #content-html, #wp-content-editor-tools #content-tmce').removeClass('active');
				$('#editorcontainer, #quicktags, #post-status-info, #media-buttons, #wp-content-media-buttons, #wp-content-editor-container').addClass('hide');
				$('#editor-builder-tab').removeClass('hide');

				$('input[name=' + ether.prefix + 'editor_tab]').val('builder');

				return false;
			}));

			$tabs.children('a:not(#edButtonBuilder)').click( function()
			{
				if ($(this).attr('id') != 'edButtonBuilder')
				{
					$('#wp-editor-wrap').removeClass('tmce-active html-active');
					$('.wp-editor-wrap').removeClass('tmce-active html-active active');
					$('.wp-switch-editor').removeClass('active');
					$('#edButtonBuilder').removeClass('active');

					if ($(this).attr('id') == 'content-html' || $(this).attr('id') == 'edButtonHTML')
					{
						$('#edButtonHTML, #content-html').addClass('active');
						$('.wp-editor-wrap').addClass('html-active');
					} else
					{
						$('#edButtonPreview, #content-tmce').addClass('active');
						$('.wp-editor-wrap').addClass('tmce-active');
					}

					//$(this).addClass('active');
					$('#editorcontainer, #quicktags, #post-status-info, #media-buttons, #wp-content-media-buttons, #wp-content-editor-container').removeClass('hide');
					$('#editor-builder-tab').addClass('hide');

					$('input[name=' + ether.prefix + 'editor_tab]').val('');
				}
			});

			if (ether.builder_tab || $('input[name=' + ether.prefix + 'editor_tab]').val() == 'builder')
			{
				$('#editor-toolbar #edButtonHTML, #editor-toolbar #edButtonPreview, #wp-content-editor-tools #content-html, #wp-content-editor-tools #content-tmce').removeClass('active');
				$('.wp-editor-wrap').removeClass('tmce-active html-active active');
				$('.wp-switch-editor').removeClass('active');
				$('#edButtonBuilder').addClass('active');
				$('#editorcontainer, #quicktags, #post-status-info, #media-buttons, #wp-content-media-buttons, #wp-content-editor-container').addClass('hide');
				$('#editor-builder-tab').removeClass('hide');

				$('input[name=' + ether.prefix + 'editor_tab]').val('builder');
			}
		}
	}

	if (ether.hide_visual_tab)
	{
		$('#edButtonPreview, .wp-switch-editor.switch-tmce').hide();
	}

	if (ether.hide_html_tab)
	{
		$('#edButtonHTML, .wp-switch-editor.switch-html').hide();
	}

	if ( ! ether.builder_tab && (ether.hide_visual_tab || ether.hide_html_tab))
	{
		$('.wp-editor-wrap').removeClass('tmce-active html-active active');
		$('.wp-switch-editor, #edButtonHTML, #edButtonPreview').removeClass('active');

		if (ether.hide_visual_tab)
		{
			/* some kind of bug, shows up tiny mce toolbar so show builder tab instead */

			/*$('#edButtonHTML, .wp-switch-editor.switch-html').addClass('active');
			$('.wp-editor-wrap').addClass('html-active');
			switchEditors.go('content', 'html');

			$('.mceEditor, #content_parent').addClass('hide');
			$('#content').show();*/
		} else
		{
			$('#edButtonPreview, .wp-switch-editor.switch-tmce').addClass('active');
			$('.wp-editor-wrap').addClass('tmce-active');

			if (typeof switchEditors != 'undefined' && $('#wp-content-wrap').length > 0)
			{
				switchEditors.go('content', 'tinymce');
			}
		}
	}

	$('body').append($('<div />').attr('id', 'builder-widget-overlay'));
	$('#builder-widget-overlay').hide();

	$('.builder-location-wrapper:not(.read-only) .builder-location, .builder-location-wrapper:not(.read-only) .builder-location .builder-widget-row > div.builder-widget-column').each(make_sortable);

	$(document).keydown( function(e)
	{
		if (e.keyCode == 27)
		{
			ether.cancel_builder_modal();
		}
	});

	var __BUILDER_TARGET__ = null;
	var __BUILDER_POSITION__ = null;

	$('button[name=builder-widget-add]').live('click', function()
	{
		$('input[name=builder-widget-filter]').val('');
		$('#builder-widgets .builder-widget-wrapper').show();

		$('#builder-widgets').show();
		$('#builder-widget-overlay').stop(true, true).fadeTo('fast', 0.9, function()
		{
			$('input[name=builder-widget-filter]').focus();
		});

		on_builder_widget_modal_open($('#builder-widgets'));

		if ($(this).closest('.builder-widget-core').length > 0)
		{
			var index = $(this).parent('.builder-widget-column-options').index();
			__BUILDER_TARGET__ = $(this).closest('.builder-widget').children('.builder-widget-row').children('.builder-widget-column').eq(index);
			__BUILDER_POSITION__ = 'append';
		} else if ($(this).closest('.builder-location-wrapper').length > 0)
		{
			__BUILDER_TARGET__ = $('.builder-location-wrapper:not(.read-only) #builder-location-main');

			if ($(this).closest('.builder-location-widget-add').next('.builder-location-wrapper:not(.read-only) #builder-location-main').length == 1)
			{
				__BUILDER_POSITION__ = 'prepend';
			} else
			{
				__BUILDER_POSITION__ = 'append';
			}
		}

		return false;
	});

	$('#builder-widgets .builder-widget-wrapper').click( function()
	{
		var $clone = $(this).clone();

		$clone.addClass('ether-virgin');

		if (__BUILDER_TARGET__ != null)
		{
			if ($clone.hasClass('builder-widget-core'))
			{
				__BUILDER_TARGET__ = $('.builder-location-wrapper:not(.read-only) #builder-location-main');
			}

			if (__BUILDER_POSITION__ == 'prepend')
			{
				__BUILDER_TARGET__.prepend($clone);
				update_widget_data($clone);
				update_widget_row_order($('.builder-location .builder-widget-wrapper'));
			} else
			{
				__BUILDER_TARGET__.append($clone);
				update_widget_data($clone);
			}

			$('#builder-widgets').hide();
			$('#builder-widget-overlay').stop(true, true).fadeTo('fast', 0.0, function() { $(this).hide(); });

			if ( ! $clone.hasClass('builder-widget-core'))
			{
				$clone.find('a.edit').click();
			} else
			{
				$clone.find('.builder-widget-row-options').hide();
				$('.builder-location-wrapper:not(.read-only) .builder-location .builder-widget-row > div.builder-widget-column').each( function()
				{
					$(this).sortable().sortable('enable');
				});
			}

			$('.builder-location-wrapper:not(.read-only) .builder-location, .builder-location-wrapper:not(.read-only) .builder-location .builder-widget-row > div.builder-widget-column').each(make_sortable);
		}
	});

	$('.builder-widget-actions a.duplicate').live('click', function()
	{
		make_duplicate($(this).closest('.builder-widget-wrapper').eq(0), true, true);

		return false;
	});

	$('.builder-widget-actions a.toggle-visibility').live('click', function()
	{
		var $widget = $(this).closest('.builder-widget-wrapper').eq(0);
		var current_state = $widget.hasClass('builder-hidden-widget');

		update_widget_visibility($widget, current_state);

		return false;
	});

	$('.builder-widget-actions a.edit').live('click', function()
	{
		var $widget = $(this).closest('.builder-widget-wrapper').eq(0);

		$(this).find('.builder-widget-content').addClass('closed');
		$('#builder-widget-overlay').stop(true, true).fadeTo('fast', 0.9);

		$(this).closest('.builder-widget').children('.builder-widget-content').toggleClass('closed');
		$(this).closest('.builder-widget').children('.builder-widget-content').find('input[type!=hidden], select, textarea').eq(0).focus();

		on_builder_widget_modal_open($widget);

		return false;
	});

	$('.builder-widget .save').live('click', function()
	{
		var $widget = $(this).closest('.builder-widget-wrapper');

		//this is a marker class for widgets that are just added from #builder-widgets, it gets removed as soon as a wigdet is saved for the first time
		$widget.removeClass('ether-virgin');

		if ($widget.find('.builder-widget-rich-form').length > 0)
		{
			var $textarea = $widget.find('#builder-rich-content');

			destroy_richtext($textarea, true);

			__RICH_TEXTAREA__.val(__RICH_CONTENT__);
			__RICH_TEXTAREA__.text(__RICH_CONTENT__);

			$widget.find('.builder-widget-rich-form').remove();
			$widget.find('.builder-widget-content-form').show();

			return false;
		}

		update_widget($widget);

		$('.builder-widget-content').addClass('closed');
		$('#builder-widget-overlay').stop(true, true).fadeTo('fast', 0.0, function() { $(this).hide(); });

		var $sidebar_inside = $(this).closest('.widget-inside');

		if ($sidebar_inside.length > 0)
		{
			$sidebar_inside.find('input[name=savewidget]').click();
		}

		on_builder_widget_modal_close();

		return false;
	});

	$('.builder-modal-close').live('click', function (evt)
	{
		ether.cancel_builder_modal(evt);
	});

	$('.builder-location-wrapper:not(.read-only) .builder-widget .remove').live('click', function()
	{
		ether.remove_widget($(this).closest('.builder-widget-wrapper'));
	});

	$('.builder-location .builder-widget-wrapper').each( function()
	{
		update_widget($(this));
	});

	$('#builder-widgets .hidden-widgets-show a').click( function()
	{
		$('#builder-widgets').find('.builder-widget-wrapper.hide').removeClass('hide').removeAttr('style');
		$('#builder-widgets .hidden-widgets-show, #builder-widgets .hidden-widgets-count').remove();

		return false;
	});

	$('.builder-options > p > a').click( function()
	{
		var index = $(this).index();

		var $current = $(this).parent().nextAll('fieldset').eq(index);
		$(this).parent().parent().find('fieldset').not($current).slideUp();

		$current.stop(true, true).slideDown();

		return false;
	});

	$('.builder-widget select').live('change', function ()
	{
		var val = $(this).val();

		$(this)
			.children('option[value="' + val + '"]').siblings().removeAttr('selected').end()
			.attr('selected', 'selected');
	});

	$('.builder-widget input').live('keydown', function (evt)
	{
		if (evt.keyCode === 13)
		{
			//$(this).blur();
			return false;
		}
	});

	$('.builder-widget input[type="checkbox"]').click(function ()
	{
		$(this).attr('checked', this.checked);
	});

	//$('.builder-widget').find('select[name*="rows"]').add($('.builder-widget').find('select[name*="columns"]')).live('change', function ()
	$('.builder-widget').find('select[name*="rows"]').live('change', function ()
	{
		//check_current_indicator($(this));
		update_select_length($(this).parents('fieldset').eq(0).find('select[name*="current"], select[name*="view_pos"]'), $(this).parents('fieldset').eq(0).find('.group-content-wrap').find('.group-content').find('.group-item').length);
	}).end().find('select[name*="columns"]').live('change', function ()
	{
		//check_current_indicator($(this));
		update_select_length($(this).parents('fieldset').eq(0).find('select[name*="current"], select[name*="view_pos"]'), $(this).parents('fieldset').eq(0).find('.group-content-wrap').find('.group-content').find('.group-item').length);
	});

	$('textarea').live('blur', function ()
	{
		var val = $(this).val();
		$(this).val(val);
		$(this).text(val);
	});

	$('.builder-location .builder-widget-wrapper').live('mousedown', function ()
	{
		ether.widget_mousedown = true;
	}).live('mouseup', function ()
	{
		ether.widget_mousedown = false;
	});

	$('.builder-location .builder-widget-row-options').hide();
	$('.builder-location .builder-widget-wrapper').live('mouseenter', function (evt)
	{
		if (ether && ! ether.widget_mousedown || ether.widget_drag && ether.widget_mousedown === false)
		{
			$(this).find('.builder-widget-row-options').stop(true, true).delay(250)
				// .slideDown(250)
				.fadeIn(250)
				// .show()
		}
	}).live('mouseleave', function (evt)
	{
		if (ether && ! ether.widget_mousedown || ether.widget_drag && ether.widget_mouseup === false)
		{
			$(this).find('.builder-widget-row-options').stop(true, true)
				// .slideUp(250)
				.fadeOut(250)
				// .hide()
		}
	});

	//LIVE PREVIEWS
	//update here only the stuff that appears inside .builder-widget-content when editing it
	//.builder-widget-location .builder-widget-bar are handled separately on .save via update_widget function

	//LIST WIDGET
	$('.builder-location .builder-widget-type-list select.builder-list-widget-bullet-field').live('change', function (evt)
	{
		var $widget = $(this).closest('.builder-widget-wrapper');

		$widget.find('.builder-list-widget-bullet-preview span').attr('class', 'builder-widget-icon builder-widget-icon-list builder-list-widget-icon-' + $(this).val()); //update preview

		// update_widget_icon($widget);
	});

	//MSG WIDGET
	$('.builder-location .builder-widget-type-message select.builder-message-widget-type-field').live('change', function (evt)
	{
		var $widget = $(this).closest('.builder-widget-wrapper');
		var $preview = $widget.find('.builder-message-widget-type-preview span'); //edit form view
		$preview.attr('class', 'ether-msg ether-msg-' + $(this).val());

		// update_widget_icon($widget);
	});

	//BUTTON WIDGET
	$('.builder-location .builder-widget-type-button')
		.find('select.builder-widget-align-field').live('change', function (evt)
		{
			update_button_widget_live_preview($(this).closest('.builder-widget-wrapper'), 'align', $(this).find('option:selected').text());
		}).end()
		.find('input.builder-widget-width-field').live('blur', function (evt)
		{
			update_button_widget_live_preview($(this).closest('.builder-widget-wrapper'), 'width', $(this).val());
		}).end()
		.find('input.builder-button-widget-label-field').live('blur', function (evt)
		{
			update_button_widget_live_preview($(this).closest('.builder-widget-wrapper'), 'label', $(this).val());
		}).end()
		.find('select.builder-button-widget-style-field').live('change', function (evt)
		{
			update_button_widget_live_preview($(this).closest('.builder-widget-wrapper'), 'style', $(this).find('option:selected').text());
		}).end()
		.find('input.builder-button-widget-background-field').live('blur change', function (evt)
		{
			update_button_widget_live_preview($(this).closest('.builder-widget-wrapper'), 'background', $(this).val());
		}).end()
		.find('input.builder-button-widget-color-field').live('blur change', function (evt)
		{
			update_button_widget_live_preview($(this).closest('.builder-widget-wrapper'), 'color', $(this).val());
		}).end();

	//IMAGE WIDGET 
	//has custom fields for width and alignment; widget visibility / alignment don't apply here
	$('.builder-location .builder-widget-type-image')
		.find('select.builder-image-widget-align-field').live('change', function (evt)
		{
			update_widget_alignment($(this).closest('.builder-widget-wrapper'), $(this).val());
		}).end()
		.find('input.builder-image-widget-image-width-field').live('blur', function (evt)
		{
			update_widget_width($(this).closest('.builder-widget-wrapper'), $(this).val());
		}).end()
		.find('input.builder-image-widget-image-crop-width-field').live('blur', function (evt)
		{
			update_widget_width($(this).closest('.builder-widget-wrapper'), $(this).val());
		});



	//WIDGET VISIBILITY / IS_HIDDEN
	$('.builder-location input.builder-widget-visibility-field').live('click', function (evt)
	{
		update_widget_visibility($(this).closest('.builder-widget-wrapper'), ! this.checked);
	});

	//WIDGET ALIGNMENT
	$('.builder-location select.builder-widget-align-field').live('change', function (evt)
	{
		update_widget_alignment($(this).closest('.builder-widget-wrapper'), $(this).val());
	});

	//WIDGET WIDTH
	$('.builder-location input.builder-widget-width-field').live('blur', function (evt)
	{
		update_widget_width($(this).closest('.builder-widget-wrapper'), $(this).val());
	});

	//WIDGET CLEARFLOAT
	$('.builder-location input.builder-widget-clearfloat-field').live('click', function (evt)
	{
		update_widget_clearfloat($(this).closest('.builder-widget-wrapper'), this.checked);
	});

	$(window).resize(function ()
	{
		if (ether.$current_builder_widget_modal && ether.$current_builder_widget_modal.css('display') !== 'none' && ether.$current_builder_widget_modal.attr('id') !== 'builder-widgets')
		{
			clearTimeout(ether.set_dynamic_label_timeout);
			ether.set_dynamic_label_timeout = setTimeout(function ()
			{
				ether.set_dynamic_label(ether.$current_builder_widget_modal);
				//delete ether.set_dynamic_label_timeout;
			}, 50);
		}

		set_builder_location_width();
	});

	$('#edButtonBuilder').click(function ()
	{
		set_builder_location_width();
	});

	set_builder_location_width();

	widget_debug.debug_id();

});})(jQuery);
