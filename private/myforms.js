jQuery(document).ready(function() {
	// enhance jQuery to have disable/enable functions
	jQuery.fn.disable=function() {
		jQuery(this).attr('disabled', true);
	}
	jQuery.fn.enable=function() {
		jQuery(this).attr('disabled', false);
	}
	jQuery.fn.error=function(msg) {
		jQuery(this).val(msg);
		jQuery(this).addClass('error');
	}
	jQuery.fn.setval=function(val) {
		jQuery(this).val(val);
		jQuery(this).removeClass('error');
	}
	// general logging function
	jQuery.fn.log=function(message,error) {
		//var ownName = arguments.callee.toString();
		//ownName = ownName.substr('function '.length);        // trim off "function "
		//ownName = ownName.substr(0, ownName.indexOf('('));        // trim off everything after the function name
		// create new element
		var element=jQuery('<div/>').text(message);
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
	jQuery('.inputfield').focus(function(){
		jQuery(this).addClass('focus');
	});
	jQuery('.inputfield').blur(function(){
		jQuery(this).removeClass('focus');
	});
	jQuery(document).ajaxSend(function(event,request,settings) {
		jQuery('#log').log('ajaxStart '+settings.url,false);
	});
	jQuery(document).ajaxComplete(function(event,request,settings) {
		jQuery('#log').log('ajaxComplete '+settings.url,false);
	});

	jQuery.fn.reload=function(table) {
		jQuery(this).disable();
		jQuery(this).val('getting data...');
		var id=jQuery(this);
		jQuery.ajax({
			url: 'GetList.php?table='+table,
			dataType: 'json',
			//data: data,
			success: function(data, textStatus, XMLHttpRequest) {
				id.autocomplete({
					source: data,
					minLength: 2,
					select: function(event, ui) {
						jQuery('#log').log(ui.item ? (id+' selected: ' + ui.item.value + ' aka ' + ui.item.id) : 'Nothing selected, input was ' + this.value,false);
					},
					change: function(event, ui) {
						jQuery('#log').log(ui.item ? (id+' change: ' + ui.item.value + ' aka ' + ui.item.id) : 'Nothing selected, input was ' + this.value,false);
					}
				});
				// now field can be selected
				id.enable();
				// set error state as non selected
				id.setval('');
				id.setval(data[0].label);
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				jQuery('#log').log('ajax error: '+errorThrown+','+textStatus+','+XMLHttpRequest.responseText,true);
				id.error('ERROR IN GETTING DATA');
			}
		});
	}
	// set focus to the first input field
	//jQuery('input#company').select().focus();
	// disable sumbit on first entering the form
	jQuery('#submit').disable();
	jQuery('#submit').click(function() {
		// disable the submit button to prevent double sumbit
		jQuery('#submit').disable();
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
				jQuery('#submit').enable();
			}
		});
		return false;
	});
});
// redefine the alert function so that we would not use it by mistake
function alert(msg) {
	jQuery.log(msg);
}
