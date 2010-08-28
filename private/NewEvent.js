jQuery(document).ready(function() {
	// enhance jQuery to have disable/enable functions
	jQuery.fn.disable=function() {
		jQuery(this).attr("disabled", true);
	}
	jQuery.fn.enable=function() {
		jQuery(this).attr("disabled", false);
	}
	// set styles for focus on the field currently being filled...
	jQuery('.inputfield').css({backgroundColor:"#FFFFFF"});
	jQuery('.inputfield').focus(function(){
		jQuery(this).css({backgroundColor:"#EB8F00"});
	});
	jQuery('.inputfield').blur(function(){
		jQuery(this).css({backgroundColor:"#FFFFFF"});
	});
	// general logging function
	function log(message) {
		// add the message to the log
		jQuery("<div/>").text(message).appendTo("#log");
		// scroll to the bottom
		jQuery("#log").attr("scrollTop", jQuery("#log").attr("scrollHeight"));
	}
	jQuery(document).ajaxSend(function(event,request,settings) {
		log('ajaxStart '+settings.url);
	});
	jQuery(document).ajaxComplete(function(event,request,settings) {
		log('ajaxComplete '+settings.url);
	});

	// construct two date pickers
	jQuery("#from_date").datepicker();
	jQuery("#to_date").datepicker();
	var init={ "secView":false };
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
						log(ui.item ? (id+" selected: " + ui.item.value + " aka " + ui.item.id) : "Nothing selected, input was " + this.value);
					}
				});
				// now company can be selected
				jQuery(id).enable();
				// set error state as non selected
				jQuery(id).val(data[0].label);
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				log("ajax error: "+errorThrown+','+textStatus+','+XMLHttpRequest.responseText);
				jQuery(id).val('ERROR IN GETTING DATA');
			}
		});
	}
	reload("#calendar","TbClCalendar");
	reload("#company","TbBsCompanies");
	reload("#course","TbBsCourses");
	reload("#location","TbLcNamed");
	reload("#creator","TbIdPerson");
	jQuery("#reload_calendar").click(function() { reload("#calendar","TbClCalendar");} );
	jQuery("#reload_company").click(function() { reload("#company","TbBsCompanies");} );
	jQuery("#reload_course").click(function() { reload("#course","TbBsCourses");} );
	jQuery("#reload_location").click(function() { reload("#location","TbLcNamed");} );
	jQuery("#reload_creator").click(function() { reload("#creator","TbIdPerson");} );
	// set focus to the first input field
	//jQuery("input#company").select().focus();
	// disable sumbit on first entering the form
	jQuery("#submit").disable();
	jQuery("#submit").click(function() {
		// disable the submit button to prevent double sumbit
		jQuery("#submit").disable();
		// multi validations (validations involving several fields)
		// I don't have any such validations at the moment.
		// serialize all data (this is a jQuery function)
		var dataString=$('#myform').serialize();
		log("submitting: "+dataString);
		jQuery.ajax({
			type: "POST",
			url: "NewEvent.php",
			data: dataString,
			error: function(data) {
				log(data);
			},
			// only on success
			success: function(data) {
				log(data);
			},
			// function which is called on erorr on success
			complete: function() {
				// TODO: need to check for validations
				jQuery("#submit").enable();
			}
		});
		return false;
	});
});
