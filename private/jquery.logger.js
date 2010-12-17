jQuery(document).ready(function() {
	jQuery.widget('ui.logger',{
		// options
		options:{
			name:null,
		},
		// constructor
		_create:function() {
			// closure
			var widget=this;
			// intercept ajax
			jQuery(document).ajaxSend(function(event,request,settings) {
				widget.log('ajaxStart '+settings.url,false);
			});
			jQuery(document).ajaxComplete(function(event,request,settings) {
				widget.log('ajaxComplete '+settings.url,false);
			});
			// redefine the alert function so that we would not use it by mistake
			alert=function(msg) {
				widget.log(msg,false);
			}
		},
		// general logging function
		log:function(message,error) {
			//var ownName = arguments.callee.toString();
			//ownName = ownName.substr('function '.length); // trim off 'function '
			//ownName = ownName.substr(0,ownName.indexOf('(')); // trim off everything after the function name
			// create new element
			var element=jQuery('<div>').text(message);
			// add error class if this is an error
			if(error) {
				element.addClass('errortext');
			}
			// add the message to the log
			this.element.append(element);
			// scroll to the bottom
			this.element.attr('scrollTop',this.element.attr('scrollHeight'));
		},
	});
});
