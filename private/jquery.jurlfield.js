/*
 * This is a combo field that gets its value from the server
 * and offers auto complete capabilities
 */
jQuery(document).ready(function() {
	jQuery.fn.doUrlFocusin=function() {
		var widget=jQuery(this);
		var w_input=widget.data('w_input');
		var options=widget.data('options');
		widget.addClass(options.activeClass);
		if(widget.data('initState')) {
			// reset the value
			w_input.val('');
			widget.data('initState',false);
		}
	}
	jQuery.fn.doUrlFocusout=function() {
		var widget=jQuery(this);
		var w_input=widget.data('w_input');
		var options=widget.data('options');
		widget.removeClass(options.activeClass);
		if(w_input.val()=='') {
			if(options.mustInput) {
				widget.data('initState',true);
				w_input.val(options.initMsg);
			}
		} else {
			if(widget.data('initState')) {
				widget.data('initState',false);
			}
		}
	}
	jQuery.fn.reload=function() {
		var widget=jQuery(this);
		var w_input=widget.data('w_input');
		var options=widget.data('options');
		widget.setOk();
		w_input.disable();
		w_input.val('getting data...');
		jQuery.ajax({
			url:options.url,
			dataType:'json',
			success:function(data, textStatus, XMLHttpRequest) {
					w_input.autocomplete('option','source',data);
					w_input.enable();
					w_input.val('');
					//widget.validate();
					// set error state as non selected
					//w_input.setval(o.initMsg);
					//id.setval(data[0].label);
			},
			error:function(XMLHttpRequest, textStatus, errorThrown) {
				jQuery.log('ajax error: '+errorThrown+','+textStatus+','+XMLHttpRequest.responseText,true);
				widget.OnError('ERROR IN GETTING DATA');
			}
		});
	}
	jQuery.fn.extend({
		jurlfield:function(options) {
			var defaults = {
				url:null,
				addLabel:true,
				name:'NoName',
				initMsg:'must put value here',
				activeClass:'focus',
			};
			var o=jQuery.extend(defaults, options);
			if(o.url==null) {
				alert('must supply a url');
			}
			return this.each(function() {
				var widget=jQuery(this);
				widget.addClass('ui-widget');
				widget.data('options',o);
				if(o.addLabel==true) {
					var w_label=jQuery('<label>');
					w_label.html(o.name);
					w_label.appendTo(widget);
					w_label.addClass('fieldlabel');
					widget.data('w_label',w_label);
				}
				var attrs={
					val:o.initMsg,
					focusin:function() {
						widget.doUrlFocusin();
					},
					focusout:function() {
						widget.doUrlFocusout();
					},
				}
				var w_input=jQuery('<input>',attrs);
				w_input.addClass('anyinput');
				w_input.autocomplete({
					minLength:2,
					select:function(event, ui) {
						jQuery.log(ui.item ? (w_input+' selected: ' + ui.item.value + ' aka ' + ui.item.id):'Nothing selected, input was ' + this.value,false);
					},
					change:function(event, ui) {
						jQuery.log(ui.item ? (w_input+' change: ' + ui.item.value + ' aka ' + ui.item.id):'Nothing selected, input was ' + this.value,false);
					}
				});
				w_input.data('initState',true);
				w_input.appendTo(this);
				widget.data('w_input',w_input);

				// add the reload image
				var attrs={
					'src':'images/reload.jpg',
					'class':'inline_image',
					click:function() {
						widget.reload();
					},
				};
				var w_img=jQuery('<img>',attrs).appendTo(this);
				widget.data('w_img',w_img);

				var w_error=jQuery('<label>');
				w_error.addClass('validation_error');
				w_error.appendTo(widget);
				widget.data('w_error',w_error);

				widget.reload();
			});
		}
	});
});
