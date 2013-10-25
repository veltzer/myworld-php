/*jsl:import myworld_utils.js*/
/*
 * This is a datepicker object.
 * It is a wrapper around the original jquery ui datepicker.
 */
jQuery(document).ready(function() {
	var running_id=0;
	jQuery.widget('ui.cont_datepicker',{
		// options
		options:{
			id:0,
			// regex must be set for text inputs
			regex:/\d\d\:\d\d/,
			type:'input',
			inputtype:'text',
			name:'No name',
			// submit name
			sname:null,
			initMsg:null,
			rows:10,
			initState:false,
			initVal:null,
			httptype:'GET',
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
				fake_use(value);
				return 'regex error '+widget.options.regex;
			},
			submit:null
		},
		log:function(msg,error) {
			if(this.options.logger!=null) {
				jQuery(this.options.logger).cont_logger('log',msg,error);
			}
		},
		report:function(state) {
			if(this.options.submit!=null) {
				jQuery(this.options.submit).cont_submit('report',this.id,state);
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
			this.report(true);
		},
		setOk:function(msg) {
			this.w_msg.removeClass('errortext');
			this.w_msg.html(msg);
			this.error=false;
			this.report(false);
		},
		doFocusin:function() {
			this.element.addClass('fieldfocus');
			if(this.options.initState===true) {
				// reset the value
				this.w_input.val('');
				this.options.initState=false;
				this.validate();
			}
		},
		doFocusout:function() {
			this.element.removeClass('fieldfocus');
			// no need to validate here since we do it on every keystroke...
			//this.validate();
		},
		disable:function() {
			this.w_input.attr('disabled',true);
		},
		enable:function() {
			this.w_input.attr('disabled',false);
		},
		// constructor
		_create:function() {
			// comply with jquery ui ?!?
			this.element.addClass('ui-widget');
			// this variable is injected into the closure...
			var widget=this;
			// set an id
			this.id=running_id;
			running_id++;

			// check that certain options have been passed
			if(this.options.sname===null) {
				throw 'must declare sname attribute';
			}

			// add the label
			this.w_label=jQuery('<label>');
			this.w_label.html(this.options.name);
			this.w_label.addClass('fieldlabel');
			this.w_label.appendTo(this.element);
			
			var attrs={
				focusin:function() {
					widget.doFocusin();
				},
				focusout:function() {
					widget.doFocusout();
				}
			};

			this.w_input=jQuery('<input>',attrs);
			this.w_input.addClass('fieldinput');
			this.w_input.appendTo(this.element);
			jQuery(this.w_input).datepicker();
		}
	});
});
