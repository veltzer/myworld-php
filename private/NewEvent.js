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

	// construct two date pickers
	jQuery('#from_date').datepicker();
	jQuery('#to_date').datepicker();
	var init={ 'secView':false };
	jQuery('#from_time').jtimepicker(init);
	jQuery('#to_time').jtimepicker(init);
	function reload(id,table) {
		jQuery(id).disable();
		jQuery(id).val('getting data...');
		jQuery.ajax({
			url: 'GetList.php?table='+table,
			dataType: 'json',
			//data: data,
			success: function(data, textStatus, XMLHttpRequest) {
				jQuery(id).autocomplete({
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
				jQuery(id).enable();
				// set error state as non selected
				jQuery(id).setval('');
				//jQuery(id).setval(data[0].label);
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				jQuery('#log').log('ajax error: '+errorThrown+','+textStatus+','+XMLHttpRequest.responseText,true);
				jQuery(id).error('ERROR IN GETTING DATA');
			}
		});
	}
	reload('#calendar','TbClCalendar');
	reload('#company','TbBsCompanies');
	reload('#course','TbBsCourses');
	reload('#location','TbLcNamed');
	reload('#creator','TbIdPerson');
	jQuery('#reload_calendar').click(function() { reload('#calendar','TbClCalendar');} );
	jQuery('#reload_company').click(function() { reload('#company','TbBsCompanies');} );
	jQuery('#reload_course').click(function() { reload('#course','TbBsCourses');} );
	jQuery('#reload_location').click(function() { reload('#location','TbLcNamed');} );
	jQuery('#reload_creator').click(function() { reload('#creator','TbIdPerson');} );
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
