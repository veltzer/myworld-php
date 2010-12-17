/*
 * An enahnced submit button that has room to show why it is cannot
 * submit and it's status.
 *
 * Notes:
 * This widget is NOT disabled by default since it has to receive notifications
 * of errors in order to be disabled.
 */
jQuery(document).ready(function() {
	// an object to store error states for various objects
	function Errors() {
		this.errors=[];
		this.num_errors=0;
	}
	Errors.prototype.report=function(id,state) {
		if(state) {
			if(id in this.errors) {
				// nothing to do - id is already in error
			} else {
				this.errors[id]=undefined;
				this.num_errors++;
			}
		} else {
			if(id in this.errors) {
				delete this.errors[id];
				this.num_errors--;
			} else {
				// nothing to do - id was not in error
			}
		}
	}
	Errors.prototype.getNumErrors=function() {
		return this.num_errors;
	}

	jQuery.widget('ui.submit',{
		// options
		options:{
			// text that will appear on the button
			name:null,
			// set if you want this widget to log into your logger
			logger:null,
			// the form for which this is a submit
			formid:null,
			// the type of submit
			type:'POST',
			// the url of submit
			url:null,
		},
		disable:function() {
			this.w_button.attr('disabled',true);
		},
		enable:function() {
			this.w_button.attr('disabled',false);
		},
		log:function(msg,error) {
			if(this.options.logger!=null) {
				jQuery(this.options.logger).logger('log',msg,error);
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
		setOk:function(msg) {
			this.w_msg.removeClass('errortext');
			this.w_msg.html(msg);
			this.error=false;
		},
		updateStatus:function(force) {
			var num_errors=this.errors.getNumErrors();
			var newStatus=(num_errors>0);
			if(newStatus!=this.error || force) {
				if(newStatus) {
					this.setError(this.errors.getNumErrors()+' errors exist ');
					this.disable();
				} else {
					//this.setOk('all is ok');
					this.setOk('');
					this.enable();
				}
			}
		},
		report:function(id,state) {
			//this.log('got report for '+id+','+state);
			this.errors.report(id,state);
			this.updateStatus(true);
			//this.log('num errors is '+this.errors.getNumErrors());
		},
		// constructor
		_create:function() {
			// comply with jquery ui ?!?
			this.element.addClass('ui-widget');
			// this variable is injected into the closure...
			var widget=this;
			// number of errors is zero when creating...
			this.errors=new Errors();
			// at creationg errors are false
			this.error=false;

			//TODO:
			// if critical data was not supplied in the options
			// then croak...
			
			// add the button and connect it to the submit method
			this.w_button=jQuery('<button>');
			this.w_button.html(this.options.name);
			//this.w_button.addClass('submit');
			this.w_button.appendTo(this.element);
			this.w_button.click(function() {
				widget.submit();
			});

			// prevent submit of the form... 
			jQuery(this.options.formid).submit(function() {
				return false;
			});

			// add the message label
			this.w_msg=jQuery('<label>');
			this.w_msg.appendTo(this.element);

			this.updateStatus(true);
		},
		submit:function() {
			// for closure issues
			var widget=this;
			// disable the submit button to prevent double submit
			this.disable();
			// there is no need to do validations since if there were any
			// we would not enter this function
			var dataString=jQuery(this.options.formid).serialize();
			this.log('submitting:'+dataString,false);
			jQuery.ajax({
				type:this.options.type,
				url:this.options.url,
				data:dataString,
				// only on error
				error:function(XMLHttpRequest,textStatus,errorThrown) {
					widget.log('ajax error:'+errorThrown+','+textStatus+','+XMLHttpRequest.responseText,true);
					widget.setError('error in submit');
				},
				// only on success
				success:function(data,textStatus,XMLHttpRequest) {
					if(data!=null) {
						widget.setOk('submit ok, server said ['+data+']');
					} else {
						widget.log('ajax null:'+textStatus+','+XMLHttpRequest.responseText,true);
						widget.setError('error in submit');
					}
				},
				// function which is called on erorr on success
				complete:function() {
					widget.enable();
				},
			});
		},
	});
});
