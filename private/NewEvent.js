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
	function reload(id,table) {
		jQuery(id).disable();
		jQuery(id).val('getting data...');
		jQuery.getJSON('GetList.php?table='+table, function(data) {
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
		});
	}
	reload("#calendar","TbClCalendar");
	reload("#company","TbBsCompanies");
	reload("#course","TbBsCourses");
	jQuery("#reload_calendar").click(function() { reload("#calendar","TbClCalendar");} );
	jQuery("#reload_company").click(function() { reload("#company","TbBsCompanies");} );
	jQuery("#reload_course").click(function() { reload("#course","TbBsCourses");} );
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
