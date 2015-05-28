(function($)
{
	$(function()
	{
		if (typeof ether != 'undefined' && typeof ether.form != 'undefined')
		{
			var $fields = $('.ether-form input, .ether-form textarea, .ether-form select');

			$fields.change( function()
			{
				var $form = $(this).closest('form').eq(0);
				var form_id = parseInt($form.attr('id').replace('ether-form-', ''));
				var id = /\[([^}]+)\]/g.exec($(this).attr('name'));

				if (id != null)
				{
					id = id[1];

					var multiple = (id.replace('][', '') != id);
					var multiple_select = '';

					if (multiple)
					{
						id = id.replace('][', '');
						multiple_select = '\]\[';
					}

					for (var target_id in ether.form.forms[form_id])
					{
						var conditional = ether.form.forms[form_id][target_id];

						var action = conditional.action;
						var condition = conditional.condition;
						var rules = conditional.rules;

						var cond_match = (condition == 'all' ? true : false);

						for (var i = 0; i < rules.length; i++)
						{
							var $field = $form.find('input[name="ether_form\[' + rules[i].field + multiple_select + '\]"], select[name="ether_form\[' + rules[i].field + multiple_select + '\]"], textarea[name="ether_form\[' + rules[i].field + multiple_select + '\]""]');

							if ($field.length > 0)
							{
								var value = ($field.is('select') ? $field.children('option:selected').val() : $field.val());

								if ($field.attr('type') == 'radio')
								{
									for (var j = 0; j < $field.length; j++)
									{
										if ($field.eq(j).attr('checked'))
										{
											value = $field.eq(j).val();

											break;
										}
									}

								} else if ($field.attr('type') == 'checkbox')
								{
									value = [];

									for (var j = 0; j < $field.length; j++)
									{
										if ($field.eq(j).attr('checked'))
										{
											value.push($field.eq(j).val());
										}
									}
								}

								var field_match = false;

								if (rules[i].check == 'is')
								{
									if (typeof value == 'object')
									{
										for (var j = 0; j < value.length; j++)
										{
											if (value[j] == rules[i].value)
											{
												field_match = true;

												break;
											}
										}
									} else
									{
										field_match = value == rules[i].value;
									}
								} else if (rules[i].check == 'isnot')
								{
									if (typeof value == 'object')
									{
										field_match = true;

										for (var j = 0; j < value.length; j++)
										{
											if (value[j] == rules[i].value)
											{
												field_match = false;

												break;
											}
										}
									} else
									{
										field_match = value != rules[i].value;
									}
								}

								if (condition == 'any' && field_match)
								{
									cond_match = true;
									break;
								} else if (condition == 'all' && ! field_match)
								{
									cond_match = false;
									break;
								}
							}
						}

						// removed eq(0) here, show()/hide() should affect all labeld elements?
						var $target = $form.find('label[for="ether_form\[' + target_id + '\]"]')

						if ($target.length > 0)
						{
							if (action == 'show')
							{
								if (cond_match)
								{
									$target.show();
								} else
								{
									$target.hide();
								}
							} else if (action == 'hide')
							{
								if (cond_match)
								{
									$target.hide();
								} else
								{
									$target.show();
								}
							}
						}
					}
				}
			});

			$fields.eq(0).trigger('change');
		}

		$('a.ether-form-popup').each( function()
		{
			var $form = $($(this).attr('href')).children();

			$(this).colorbox({ inline: true, href: $form, width: '80%', onOpen: function()
			{
				window.location.hash = $(this).attr('href');
			} });
		});

		if (typeof window.location.hash != 'undefined' && window.location.hash != '')
		{
			$('a.ether-form-popup[href="' + window.location.hash + '"]').click();
		}
	});
})(jQuery);
