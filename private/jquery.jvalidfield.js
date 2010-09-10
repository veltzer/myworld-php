/*
 * This is a validating field.
 * You can pass any function to validate against.
 * If you validate vs a regex then just pass the regex.
 */
jQuery(document).ready(function() {
	jQuery.widget('ui.jvalidfield',{
		options:{
			regex:null,
			addLabel:true,
			type:'input',
			inputtype:'text',
			name:'No name',
			initMsg:'put some text here',
			rows:10,
			initState:true,
			initval:null,
			url:null,
			validate:function(widget,value) {
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
			if(this.options.type=='select') {
				return;
			}
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
		adddata:function(data) {
			// TODO: clear all previous options
			// add new options in
			for(x in data) {
				jQuery('<option>',{
					'value':data[x].id
				}).html(data[x].label).appendTo(this.w_input);
			}
			this.w_input.enable();
			if(this.options.initval==null) {
				this.w_input.val(data[0].id);
			} else {
				this.w_input.val(this.options.initval);
			}
		},
		fetch:function() {
			this.setOk();
			this.w_input.disable();
			this.w_input.val('getting data...');
			var widget=this;
			jQuery.ajax({
				url: this.options.url,
				dataType: 'json',
				success: function(data, textStatus, XMLHttpRequest) {
					widget.adddata(data);
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					jQuery.log('ajax error: '+errorThrown+','+textStatus+','+XMLHttpRequest.responseText,true);
					widget.setError('ERROR IN GETTING DATA');
				}
			});
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
				focusin:function() {
					widget.doFocusin();
				},
				focusout:function() {
					widget.doFocusout();
				},
				keyup:function() {
					widget.validate();
				},
			};
			if(this.options.initState==true) {
				attrs.val=this.options.initMsg;
			} else {
				attrs.val=this.options.initval;
			}
			if(this.options.type=='input') {
				attrs.type=this.options.inputtype;
			}
			if(this.options.type=='textarea') {
				attrs.rows=this.options.rows;
			}
			this.w_input=jQuery('<'+this.options.type+'>',attrs);
			this.w_input.addClass('anyinput');
			this.w_input.appendTo(this.element);

			// add the reload button
			if(this.options.type=='select') {
				var attrs={
					'src': 'images/reload.jpg',
					click: function() {
						widget.fetch();
					},
				};
				this.w_img=jQuery('<img>',attrs);
				this.w_img.addClass('inline_image');
				this.w_img.appendTo(this.element);
			}

			this.w_error=jQuery('<label>');
			this.w_error.addClass('validation_error');
			this.w_error.appendTo(this.element);

			if(this.options.type=='select') {
				this.fetch();
			} else {
				this.validate();
			}
		},
	});
});
