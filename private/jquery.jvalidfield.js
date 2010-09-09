/*
 * This is a validating field.
 * You can pass any regexp to validate against.
 */
jQuery(document).ready(function() {
	jQuery.fn.setError=function(errormsg) {
		var widget=jQuery(this);
		var w_error=widget.data('w_error');
		w_error.html(errormsg);
		widget.data('error',true);
        }
	jQuery.fn.setOk=function() {
		var widget=jQuery(this);
		var w_error=widget.data('w_error');
		w_error.html('');
		widget.data('error',false);
	}
	jQuery.fn.doFocusin=function() {
		var widget=jQuery(this);
		var w_input=widget.data('w_input');
		var options=widget.data('options');
		w_input.addClass(options.activeClass);
		if(widget.data('initState')==true) {
			// reset the value
			w_input.val('');
			widget.data('initState',false);
			widget.validate();
		}
	}
	jQuery.fn.doFocusout=function() {
		var widget=jQuery(this);
		var w_input=widget.data('w_input');
		var options=widget.data('options');
		w_input.removeClass(options.activeClass);
		widget.validate();
	}
	jQuery.fn.validate=function() {
		var widget=jQuery(this);
		var options=widget.data('options');
		var w_input=widget.data('w_input');
		if(options.regex.test(w_input.val())) {
			widget.setOk();
		} else {
			widget.setError('regex error');
		}
	}
	jQuery.fn.extend({
		jvalidfield: function(options) {
			var defaults = {
				regex: '',
				addLabel: true,
				type: 'input',
				inputtype: 'text',
				name: 'No name',
				initMsg: 'put some text here',
				activeClass: 'focus',
				mustInput: true,
				rows: 10,
				initError: true,
			};
			var o=jQuery.extend(defaults, options);
			return this.each(function() {
				var widget=jQuery(this);
				if(o.addLabel==true) {
					var w_label=jQuery('<label>',{}).html(o.name).appendTo(this);
				}
				widget.data('options',o);
				var attrs={
					val: o.initMsg,
					focusin: function() {
						widget.doFocusin();
					},
					focusout: function() {
						widget.doFocusout();
					},
					keyup: function() {
						widget.validate();
					},
						/*
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
						*/
				}
				if(o.type=='input') {
					attrs.type=o.inputtype;
				}
				if(o.type=='textarea') {
					attrs.rows=o.rows;
				}
				var w_input=jQuery('<'+o.type+'>',attrs);
				w_input.appendTo(this);
				
				var w_error=jQuery('<span>',{
					'class': 'validation_error',
				});
				w_error.appendTo(this);
				
				widget.data('initState',true);

				widget.data('w_error',w_error);
				widget.data('w_input',w_input);
				widget.data('w_label',w_label);
				//widget.validate();
			});
		}
	});
});
