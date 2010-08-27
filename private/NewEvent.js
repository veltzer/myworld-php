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
	function reload_company() {
		jQuery("#company").disable();
		jQuery("#company").val('getting data...');
		jQuery.getJSON('GetList.php?type=company_all', function(data) {
			jQuery("#company").autocomplete({
				source: data,
				minLength: 2,
				select: function(event, ui) {
					log(ui.item ? ("Company selected: " + ui.item.value + " aka " + ui.item.id) : "Nothing selected, input was " + this.value);
				}
			});
			// now company can be selected
			jQuery("#company").enable();
			// set error state as non selected
			jQuery("#company").val(data[0].label);
		});
	}
	reload_company();
	function reload_course() {
		jQuery("#course").disable();
		jQuery("#course").val('getting data...');
		jQuery.getJSON('GetList.php?type=course_all', function(data) {
			jQuery("#course").autocomplete({
				source: data,
				minLength: 2,
				select: function(event, ui) {
					log(ui.item ? ("Course selected: " + ui.item.value + " aka " + ui.item.id) : "Nothing selected, input was " + this.value);
				}
			});
			// now course can be selected
			jQuery("#course").enable();
			// set error state as non selected
			jQuery("#course").val(data[0].label);
		});
	}
	reload_course();
	jQuery("#reload_company").click(reload_company);
	jQuery("#reload_course").click(reload_course);
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
