/*
 * This is a combo field that gets its value from the server
 * and offers auto complete capabilities
 */
jQuery(document).ready(function() {
	jQuery.fn.extend({
		jurlfield: function(options) {
			var defaults = {
				url: null,
				addLabel: true,
				name: 'NoName',
				initMsg: 'must put value here',
				activeClass: 'focus',
			};
			var o=jQuery.extend(defaults, options);
			if(o.url==null) {
				alert('must supply a url');
			}
			return this.each(function() {
				if(o.addLabel==true) {
					jQuery('<label>',{
					}).html(o.name).appendTo(this);
				}
				var attrs={
					val: o.initMsg,
					focusin: function() {
						jQuery(this).addClass(o.activeClass);
						if(jQuery(this).data('initState')) {
							// reset the value
							jQuery(this).val('');
						}
					},
					focusout: function() {
						jQuery(this).removeClass(o.activeClass);
						if(jQuery(this).val()=='') {
							if(o.mustInput) {
								jQuery(this).data('initState',true);
								jQuery(this).val(o.initMsg);
							}
						} else {
							if(jQuery(this).data('initState')) {
								jQuery(this).data('initState',false);
							}
						}
					},
				}
				var w_input=jQuery('<input>',attrs);
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
					url: o.url,
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
				jQuery('<img>',attrs).appendTo(this);
			});
		}
	});
});
