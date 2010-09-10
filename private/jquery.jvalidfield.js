/*
 * This is a validating field.
 * You can pass any regexp to validate against.
 */
jQuery(document).ready(function() {
	jQuery.fn.doFocusin=function() {
		var widget=jQuery(this);
		var w_input=widget.data('w_input');
		var options=widget.data('options');
		widget.addClass(options.activeClass);
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
		widget.removeClass(options.activeClass);
		widget.validate();
	}
	jQuery.fn.validate=function() {
		var widget=jQuery(this);
		var options=widget.data('options');
		var w_input=widget.data('w_input');
		if(widget.data('initState')==true) {
			widget.setError('no data entered');
			return;
		}
		if(options.regex.test(w_input.val())) {
			widget.setOk();
		} else {
			widget.setError('regex error '+options.regex);
		}
	}
	jQuery.fn.extend({
		jvalidfield: function(options) {
			var defaults = {
				regex: /.*/,
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
				widget.addClass('ui-widget');
				widget.data('options',o);

				if(o.addLabel==true) {
					var w_label=jQuery('<label>',{}).html(o.name).appendTo(this);
					w_label.addClass('fieldlabel');
					widget.data('w_label',w_label);
				}
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
				}
				if(o.type=='input') {
					attrs.type=o.inputtype;
				}
				if(o.type=='textarea') {
					attrs.rows=o.rows;
				}
				var w_input=jQuery('<'+o.type+'>',attrs);
				w_input.addClass('anyinput');
				w_input.appendTo(this);
				widget.data('w_input',w_input);

				var w_error=jQuery('<label>');
				w_error.addClass('validation_error');
				w_error.appendTo(this);
				widget.data('w_error',w_error);

				widget.data('initState',true);
				widget.validate();
			});
		}
	});
});
