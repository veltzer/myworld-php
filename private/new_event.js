jQuery(document).ready(function() {
	$("#from_date").datepicker();
	$("#to_date").datepicker();
	function log(message) {
		$("<div/>").text(message).prependTo("#log");
		$("#log").attr("scrollTop", 0);
	}
	$("#company").autocomplete({
		source: "GetList.php",
		//source: "dummy.php",
		minLength: 2,
		select: function(event, ui) {
			log(ui.item ? ("Selected: " + ui.item.value + " aka " + ui.item.id) : "Nothing selected, input was " + this.value);
		}
	});
	/*
	jQuery('.error').hide();
	*/
	jQuery('input').css({backgroundColor:"#FFFFFF"});
	jQuery('input').focus(function(){
		jQuery(this).css({backgroundColor:"#FFDDAA"});
		//jQuery(this).width = 600;
		//jQuery(this).animate({ width:"600px"}, 1000); // enlarge width
	});
	jQuery('input').blur(function(){
		jQuery(this).css({backgroundColor:"#FFFFFF"});
		//jQuery(this).width = 200;
		//jQuery(this).animate({ width:"200px"}, 1000); // enlarge width
	});
	/*
	jQuery("input#name").select().focus();
	*/
	jQuery("#submit").click(function() {
		// validate and process form
		// first hide any error messages
		jQuery('.error').hide();
		var dataString='';
		var p_company=jQuery("input#company").val();
		dataString+='company='+p_company;
		var p_course=jQuery("input#course").val();
		dataString+='&course='+p_course;
		var p_from_date=jQuery("input#from_date").val();
		dataString+='&from_date='+p_from_date;
		var p_to_date=jQuery("input#to_date").val();
		dataString+='&to_date='+p_to_date;
		//alert (dataString);return false;
		jQuery.ajax({
			type: "POST",
			url: "new_event.php",
			data: dataString,
			error: function(data) {
				log(data);
				//jQuery('#message').html("error"+data);
			},
			success: function(data) {
				log(data);
				//jQuery('#message').html("ok"+data);
			}
		});
		return false;
	});
});
