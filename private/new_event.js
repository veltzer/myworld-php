jQuery(document).ready(function() {
	jQuery.fn.disable=function() {
		jQuery(this).attr("disabled", true);
	}
	jQuery.fn.enable=function() {
		jQuery(this).attr("disabled", false);
	}
	jQuery("#from_date").datepicker();
	jQuery("#to_date").datepicker();
	function log(message) {
		// add the message to the log
		jQuery("<div/>").text(message).appendTo("#log");
		// scroll to the bottom
		jQuery("#log").attr("scrollTop", jQuery("#log").attr("scrollHeight"));
	}
	jQuery("#course").disable();
	jQuery.getJSON('GetList.php?type=course_all', function(data) {
		jQuery("#course").autocomplete({
			source: data,
			minLength: 2,
			select: function(event, ui) {
				log(ui.item ? ("Course selected: " + ui.item.value + " aka " + ui.item.id) : "Nothing selected, input was " + this.value);
				jQuery("#submit").enable();
			}
		});
		jQuery("#course").enable();
	});
	jQuery("#company").disable();
	jQuery.getJSON('GetList.php?type=company_all', function(data) {
		jQuery("#company").autocomplete({
			source: data,
			minLength: 2,
			select: function(event, ui) {
				log(ui.item ? ("Company selected: " + ui.item.value + " aka " + ui.item.id) : "Nothing selected, input was " + this.value);
				jQuery("#submit").enable();
			}
		});
		jQuery("#company").enable();
	});
	// set styles for focus on the field currently being filled...
	jQuery('.inputfield').css({backgroundColor:"#FFFFFF"});
	jQuery('.inputfield').focus(function(){
		jQuery(this).css({backgroundColor:"#EB8F00"});
	});
	jQuery('.inputfield').blur(function(){
		jQuery(this).css({backgroundColor:"#FFFFFF"});
	});
	// set focus to the first input field
	jQuery("input#company").select().focus();
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
			url: "new_event.php",
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
				jQuery("#submit").enable();
			}
		});
		return false;
	});
});
