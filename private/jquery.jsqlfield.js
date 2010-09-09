/*
 */
(function($) {
	$.fn.extend({
		jsqlfield: function(options) {
			var defaults = {
				table: null,
				addLabel: true,
				name: 'NoName',
				initMsg: 'must put value here',
				activeClass: 'focus',
			};
			var o=$.extend(defaults, options);
			if(o.table==null) {
				alert('must supply table name');
			}
			return this.each(function() {
				if(o.addLabel==true) {
					$('<label>',{
					}).html(o.name).appendTo(this);
				}
				var attrs={
					val: o.initMsg,
					focusin: function() {
						$(this).addClass(o.activeClass);
						if($(this).data('initState')) {
							// reset the value
							$(this).val('');
						}
					},
					focusout: function() {
						$(this).removeClass(o.activeClass);
						if($(this).val()=='') {
							if(o.mustInput) {
								$(this).data('initState',true);
								$(this).val(o.initMsg);
							}
						} else {
							if($(this).data('initState')) {
								$(this).data('initState',false);
							}
						}
					},
				}
				var w_input=$('<input>',attrs);
				w_input.autocomplete({
					//source: data,
					minLength: 2,
					select: function(event, ui) {
						jQuery.log(ui.item ? (w_input+' selected: ' + ui.item.value + ' aka ' + ui.item.id) : 'Nothing selected, input was ' + this.value,false);
					},
					change: function(event, ui) {
						jQuery.log(ui.item ? (w_input+' change: ' + ui.item.value + ' aka ' + ui.item.id) : 'Nothing selected, input was ' + this.value,false);
					}
				});
				w_input.data('initState',true);
				w_input.appendTo(this);
				
				w_input.disable();
				w_input.val('getting data...');
				jQuery.ajax({
					url: 'GetList.php?table='+o.table,
					dataType: 'json',
					//data: data,
					success: function(data, textStatus, XMLHttpRequest) {
							// set the new data
							w_input.autocomplete('option','source',data);
							// now field can be selected
							w_input.enable();
							// set error state as non selected
							w_input.setval(o.initMsg);
							//id.setval(data[0].label);
					},
					error: function(XMLHttpRequest, textStatus, errorThrown) {
						jQuery.log('ajax error: '+errorThrown+','+textStatus+','+XMLHttpRequest.responseText,true);
						w_input.error('ERROR IN GETTING DATA');
					}
				});
				// add the reload image
				var attrs={
					'src': 'images/reload.jpg',
					'class': 'inline_image',
					click: function() {
						// need to do ajax again...
					},
				};
				$('<img>',attrs).appendTo(this);
			});
		}
	});
})(jQuery);
