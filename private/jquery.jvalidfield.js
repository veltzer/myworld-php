/*
 * This is a validating field.
 * You can pass any function to validate against.
 * If you validate vs a regex then just pass the regex.
 */
jQuery(document).ready(function() {
	jQuery.widget('ui.jvalidfield',{
		options:{
			// regex must be set for text inputs
			regex:null,
			type:'input',
			inputtype:'text',
			name:'No name',
			initMsg:null,
			rows:10,
			initState:false,
			initval:null,
			url:null,
			// set if you want this widget to log into your logger
			logger:null,
			// override if you want your own rendering function
			render:function(data) {
				return data.label;
			},
			// override if you want your own validation function
			validate:function(widget,value) {
				return widget.options.regex.test(value);
			},
			// override if you want your own validation error function
			validate_error:function(widget,value) {
				return 'regex error '+widget.options.regex;
			},
		},
		log:function(msg,error) {
			if(this.options.logger!=null) {
				this.options.logger.log(msg,error);
			}
		},
		setInformation:function(msg) {
			this.w_msg.removeClass('errortext');
			this.w_msg.html(msg);
		},
		setError:function(msg) {
			this.w_msg.html(msg);
			this.w_msg.addClass('errortext');
			this.error=true;
		},
		setOk:function() {
			this.w_msg.removeClass('errortext');
			this.w_msg.html('');
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
			// no need to validate here since we do it on every keystroke...
			//this.validate();
		},
		validate:function() {
			// no need to validate selects...
			if(this.options.type=='select') {
				return;
			}
			// at init we are in error always..
			if(this.options.initState==true) {
				this.setError('no data entered');
				return;
			}
			// call the users validation and error functions
			if(this.options.validate(this,this.w_input.val())) {
				this.setOk();
			} else {
				var error=this.options.validate_error(this,this.w_input.val());
				this.setError(error);
			}
		},
		disable:function() {
			this.w_input.attr('disabled',true);
		},
		enable:function() {
			this.w_input.attr('disabled',false);
		},
		adddata:function(data) {
			// clear all previous options
			this.w_input.html('');
			// add new options in
			for(x in data) {
				jQuery('<option>',{
					'value':data[x].id
				}).html(this.options.render(data[x])).appendTo(this.w_input);
			}
			this.enable();
			if(this.options.initval==null) {
				this.w_input.val(data[0].id);
			} else {
				this.w_input.val(this.options.initval);
			}
		},
		fetch:function() {
			this.setOk();
			this.disable();
			this.setInformation('getting data');
			var widget=this;
			jQuery.ajax({
				url:this.options.url,
				dataType:'json',
				success:function(data, textStatus, XMLHttpRequest) {
					if(data!=null) {
						widget.setOk();
						widget.adddata(data);
					} else {
						widget.log('ajax null:'+textStatus+','+XMLHttpRequest.responseText,true);
						widget.setError('ERROR IN GETTING DATA');
					}
				},
				error:function(XMLHttpRequest, textStatus, errorThrown) {
					widget.log('ajax error:'+errorThrown+','+textStatus+','+XMLHttpRequest.responseText,true);
					widget.setError('ERROR IN GETTING DATA');
				}
			});
		},
		_create:function() {
			// comply with jquery ui ?!?
			this.element.addClass('ui-widget');
			// this variable is injected into the closure...
			var widget=this;

			// add the label
			this.w_label=jQuery('<label>');
			this.w_label.html(this.options.name);
			this.w_label.addClass('fieldlabel');
			this.w_label.appendTo(this.element);

			// add the input control
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

			// add the reload button (this is only for selects)
			if(this.options.type=='select') {
				var attrs={
					'src':'images/reload.jpg',
					click:function() {
						widget.fetch();
					},
				};
				this.w_img=jQuery('<img>',attrs);
				this.w_img.addClass('inline_image');
				this.w_img.appendTo(this.element);
			}

			// add the message label
			this.w_msg=jQuery('<label>');
			this.w_msg.appendTo(this.element);

			// for selects we need to fetch the data,
			// for inputs we need to validate...
			if(this.options.type=='select') {
				this.fetch();
			} else {
				this.validate();
			}
		},
	});
});
