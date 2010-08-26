jQuery(document).ready(function() {
	$("#from_date").datepicker();
	$("#to_date").datepicker();
	function log(message) {
		$("<div/>").text(message).prependTo("#log");
		$("#log").attr("scrollTop", 0);
	}
	$("#course").autocomplete({
		source: "GetList.php?type=courses",
		minLength: 0,
		select: function(event, ui) {
			log(ui.item ? ("Course selected: " + ui.item.value + " aka " + ui.item.id) : "Nothing selected, input was " + this.value);
		}
	});
	$("#company").autocomplete({
		source: "GetList.php?type=companies",
		minLength: 0,
		select: function(event, ui) {
			log(ui.item ? ("Company selected: " + ui.item.value + " aka " + ui.item.id) : "Nothing selected, input was " + this.value);
		}
	});
	jQuery('.inputfield').css({backgroundColor:"#FFFFFF"});
	jQuery('.inputfield').focus(function(){
		jQuery(this).css({backgroundColor:"#EB8F00"});
	});
	jQuery('.inputfield').blur(function(){
		jQuery(this).css({backgroundColor:"#FFFFFF"});
	});
	jQuery("input#company").select().focus();
	jQuery("#submit").click(function() {
		// validate and process form
		// first hide any error messages
		var dataString='';
		var p_company=jQuery("input#company").val();
		dataString+='company='+p_company;
		var p_course=jQuery("input#course").val();
		dataString+='&course='+p_course;
		var p_from_date=jQuery("input#from_date").val();
		dataString+='&from_date='+p_from_date;
		var p_to_date=jQuery("input#to_date").val();
		dataString+='&to_date='+p_to_date;
		log("submitting: "+dataString);
		jQuery.ajax({
			type: "POST",
			url: "new_event.php",
			data: dataString,
			error: function(data) {
				log(data);
			},
			success: function(data) {
				log(data);
			}
		});
		return false;
	});
});
