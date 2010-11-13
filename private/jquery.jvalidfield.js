/*
 * This is a validating field.
 * You can pass any function to validate against.
 * If you validate vs a regex then just pass the regex.
 *
 * TODO:
 * - remove the running id business or rewrite it better...
 */
jQuery(document).ready(function() {
	var running_id=0;
	jQuery.widget('ui.jvalidfield',{
		// options
		options:{
			// regex must be set for text inputs
			id:0,
			regex:null,
			type:'input',
			inputtype:'text',
			name:'No name',
			// submit name
			sname:null,
			initMsg:null,
			rows:10,
			initState:false,
			initval:null,
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
				return 'regex error '+widget.options.regex;
			},
			submit:null,
		},
		log:function(msg,error) {
			if(this.options.logger!=null) {
				jQuery(this.options.logger).jlogger('log',msg,error);
			}
		},
		report:function(state) {
			if(this.options.submit!=null) {
				jQuery(this.options.submit).jsubmit('report',this.id,state);
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
			if(this.options.initState==true) {
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
				//this.setOk('validated');
				this.setOk('');
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
			this.disable();
			this.setInformation('getting data');
			var widget=this;
			jQuery.ajax({
				type:this.options.httptype,
				url:this.options.url,
				dataType:'json',
				success:function(data,textStatus,XMLHttpRequest) {
					if(data!=null) {
						//widget.setOk('got data');
						widget.setOk('');
						widget.adddata(data);
					} else {
						widget.log('ajax null:'+textStatus+','+XMLHttpRequest.responseText,true);
						widget.setError('ERROR IN GETTING DATA');
					}
				},
				error:function(XMLHttpRequest,textStatus,errorThrown) {
					widget.log('ajax error:'+errorThrown+','+textStatus+','+XMLHttpRequest.responseText,true);
					widget.setError('ERROR IN GETTING DATA');
				}
			});
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
			if(this.options.sname==null) {
				throw 'must declare sname attribute';
			}

			// add the label
			this.w_label=jQuery('<label>');
			this.w_label.html(this.options.name);
			this.w_label.addClass('fieldlabel');
			this.w_label.appendTo(this.element);

			// add the input control
			var attrs={
				focusin:function() {
					widget.validate();
					widget.doFocusin();
				},
				focusout:function() {
					widget.validate();
					widget.doFocusout();
				},
				keyup:function() {
					widget.validate();
				},
				change:function() {
					widget.validate();
				},
				// this attribute is neccessary since form serialization
				// is submitting according to this name
				'name':this.options.sname,
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
			this.w_input.addClass('fieldinput');
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
				this.w_img.addClass('fieldimage');
				this.w_img.appendTo(this.element);
			}

			// add the message label
			this.w_msg=jQuery('<label>');
			this.w_msg.addClass('fieldmsg');
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
