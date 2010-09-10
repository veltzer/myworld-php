jQuery(document).ready(function() {
	// general logging function
	jQuery.fn.log=function(message,error) {
		//var ownName = arguments.callee.toString();
		//ownName = ownName.substr('function '.length);        // trim off "function "
		//ownName = ownName.substr(0,ownName.indexOf('('));        // trim off everything after the function name
		// create new element
		var element=jQuery('<div>').text(message);
		// add error class if this is an error
		if(error) {
			element.addClass('error');
		}
		// add the message to the log
		jQuery(this).append(element);
		// scroll to the bottom
		jQuery(this).attr('scrollTop',jQuery(this).attr('scrollHeight'));
	}
	// a short cut function assuming that you have a #log area
	jQuery.log=function(message,error) {
		return jQuery('#log').log(message,error);
	}
	// set styles for focus on the field currently being filled...
	jQuery(document).ajaxSend(function(event,request,settings) {
		jQuery('#log').log('ajaxStart '+settings.url,false);
	});
	jQuery(document).ajaxComplete(function(event,request,settings) {
		jQuery('#log').log('ajaxComplete '+settings.url,false);
	});

	// set focus to the first input field
	//jQuery('input#company').select().focus();
	// disable sumbit on first entering the form
	//jQuery('#submit').disable();
	jQuery('#submit').click(function() {
		// disable the submit button to prevent double sumbit
		//jQuery('#submit').disable();
		// multi validations (validations involving several fields)
		// I don't have any such validations at the moment.
		// serialize all data (this is a jQuery function)
		var dataString=$('#myform').serialize();
		jQuery('#log').log('submitting: '+dataString,false);
		jQuery.ajax({
			type: 'POST',
			url: 'NewEvent.php',
			data: dataString,
			// only on error
			error: function(data) {
				jQuery('#log').log(data,true);
			},
			// only on success
			success: function(data) {
				jQuery('#log').log(data,false);
			},
			// function which is called on erorr on success
			complete: function() {
				// TODO: need to check for validations
				//jQuery('#submit').enable();
			}
		});
		return false;
	});
});
// redefine the alert function so that we would not use it by mistake
function alert(msg) {
	jQuery.log(msg);
}
