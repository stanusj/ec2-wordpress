(function($){$(function()
{
	ether.form = { field_map: {} };

	$('select[name*="[options_preset]"]').live('change', function()
	{
		var val = $(this).val();

		if (val != '' && val != 'custom')
		{
			if (typeof ether.form_presets[val] != 'undefined')
			{
				var presets = '';

				if (typeof ether.form_presets[val] == 'object')
				{
					for (var k in ether.form_presets[val])
					{
						presets += ether.form_presets[val][k] + "\n";
					}
				} else
				{
					presets = ether.form_presets[val].join("\n");
				}

				var $target = $(this).parent().nextAll('.ether-form').find('textarea[name*=preset]');

				$target.val(presets);
				$target.text(presets);
			}
		}
	});

	$('#builder-location-main .builder-widget-wrapper select[name*="\[cond_rule_field\]"]').live('change', function()
	{
		var $widget = $(this).closest('.builder-widget-wrapper');
		var $parent = $(this).closest('.group-item');

		if ($parent.closest('.group-prototype').length == 0)
		{
			var id = $(this).val();

			var $cond_rule_value = $parent.find('select[name*="\[cond_rule_value\]"]');

			var val = $cond_rule_value.children('option:selected').val();

			$cond_rule_value.children('option').remove();

			if (typeof ether.form.field_map[id] != 'undefined')
			{
				for (var i = 0; i < ether.form.field_map[id].options.length; i++)
				{
					if (ether.form.field_map[id].options[i] != '')
					{
						$cond_rule_value.append('<option>' + ether.form.field_map[id].options[i] + '</option>');
					}
				}
			}

			if (val != null)
			{
				$cond_rule_value.val(val);
			}
		}
	});

	$('.builder-widget-actions a.edit').live('click', function()
	{
		var $widget = $(this).closest('.builder-widget-wrapper');

		var $cond_rule_field = $widget.find('select[name*="\[cond_rule_field\]"]');

		if ($cond_rule_field.length > 0)
		{
			var $fields = $('#builder-location-main').find('input[name*="\[label\]"]');

			ether.form.field_map = {};

			$fields.each( function(index, element)
			{
				var name = $(this).attr('name');
				var value = $(this).val();

				var id = /((\[.*?\]){3})(\[(.*?)\])/.exec(name)[4];

				var $parent = $(this).closest('.builder-widget-wrapper');
				var $preset = $parent.find('textarea[name*="\[preset\]"]');

				var preset = $preset.text().split("\n");

				var $cond_rule_value = $parent.find('select[name*="\[cond_rule_value\]"]');

				//$cond_rule_value.children('option').remove();

				/*if (index == 0)
				{
					for (var i = 0; i < preset.length; i++)
					{
						if (preset[i] != '')
						{
							$cond_rule_value.each( function()
							{
								$(this).append('<option>' + preset[i] + '</option>');
							});
						}
					}
				}*/

				if (value == '')
				{
					value = id;
				}

				ether.form.field_map[id] =
				{
					id: id,
					name: value,
					options: preset,
					select: ($preset.length > 0)
				};
			});

			$cond_rule_field.each( function()
			{
				var val = $(this).children('option:selected').val();

				$(this).children('option').remove();

				$(this).append('<option></option>');

				for (var k in ether.form.field_map)
				{
					if (ether.form.field_map[k].select)
					{
						$(this).append('<option value="' + k + '">' + ether.form.field_map[k].name + '</option>');
					}
				}
				if (val != null)
				{
					$(this).val(val);
					$(this).trigger('change');
				}
			});
		}
	});
});})(jQuery);
