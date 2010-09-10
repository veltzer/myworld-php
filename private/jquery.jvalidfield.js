/*
 * This is a validating field.
 * You can pass any regexp to validate against.
 */
jQuery(document).ready(function() {
	jQuery.widget("ui.jvalidfield",{
		options:{
			regex: null,
			addLabel: true,
			type: 'input',
			inputtype: 'text',
			name: 'No name',
			initMsg: 'put some text here',
			mustInput: true,
			rows: 10,
			initError: true,
			initState: true,
			validate: function(widget,value) {
				return widget.options.regex.test(value);
			},
			validate_error:function(widget,value) {
				return 'regex error '+widget.options.regex;
			},
		},
		setError:function(errormsg) {
			this.w_error.html(errormsg);
			this.error=true;
		},
		setOk:function() {
			this.w_error.html('');
			this.error=false;
		},
		doFocusin:function() {
			this.element.addClass('focus');
			if(this.options.initState==true) {
				// reset the value
				this.w_input.val('');
				this.options.initState=false;
				this.validate();
			}
		},
		doFocusout:function() {
			this.element.removeClass('focus');
			this.validate();
		},
		validate:function() {
			if(this.options.initState==true) {
				this.setError('no data entered');
				return;
			}
			if(this.options.validate(this,this.w_input.val())) {
				this.setOk();
			} else {
				var error=this.options.validate_error(this,this.w_input.val());
				this.setError(error);
			}
		},
		_create:function() {
			this.element.addClass('ui-widget');
			var widget=this;
			if(this.options.addLabel==true) {
				this.w_label=jQuery('<label>');
				this.w_label.html(this.options.name);
				this.w_label.addClass('fieldlabel');
				this.w_label.appendTo(this.element);
			}
			var attrs={
				val: this.options.initMsg,
				focusin: function() {
					widget.doFocusin();
				},
				focusout: function() {
					widget.doFocusout();
				},
				keyup: function() {
					widget.validate();
				},
			};
			if(this.options.type=='input') {
				attrs.type=this.options.inputtype;
			}
			if(this.options.type=='textarea') {
				attrs.rows=this.options.rows;
			}
			this.w_input=jQuery('<'+this.options.type+'>',attrs);
			this.w_input.addClass('anyinput');
			this.w_input.appendTo(this.element);

			this.w_error=jQuery('<label>');
			this.w_error.addClass('validation_error');
			this.w_error.appendTo(this.element);

			this.validate();
		},
	});
});
