/*
 * An enahnced submit button that has room to show why it is cannot
 * submit and it's status.
 *
 * Notes:
 * This widget is NOT disabled by default since it has to receive notifications
 * of errors in order to be disabled.
 */
jQuery(document).ready(function() {
	jQuery.widget('ui.jsubmit',{
		options:{
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
				//this.options.logger.log(msg,error);
				mydebug(this.options.logger);
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
		_create:function() {
			// comply with jquery ui ?!?
			this.element.addClass('ui-widget');
			// this variable is injected into the closure...
			var widget=this;
			
			// add the button and connect it to the submit method
			this.w_button=jQuery('<button>');
			this.w_button.html(this.options.name);
			this.w_button.addClass('submit');
			this.w_button.appendTo(this.element);
			this.w_button.click(function() {
				widget.submit();
			});

			// add the message label
			this.w_msg=jQuery('<label>');
			this.w_msg.appendTo(this.element);
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
				error:function(data) {
					widget.log(data,true);
					widget.setError(data,true);
				},
				// only on success
				success:function(data) {
					widget.log(data,false);
					widget.setOk();
				},
				// function which is called on erorr on success
				complete:function() {
					widget.enable();
				},
			});
		},
	});
});
