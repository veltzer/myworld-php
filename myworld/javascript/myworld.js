jQuery(document).ready(function() {
	// this is the accordion support function
	jQuery("div.myacc_header").click(function(){
		jQuery(this).next("div").slideToggle();
		jQuery(this).toggleClass("active");
	});
	// this is the calendar support fuction
	var date = new Date();
	var d = date.getDate();
	var m = date.getMonth();
	var y = date.getFullYear();
	jQuery('#calendar').fullCalendar({
		editable: false,
		events: [
			{
				title: 'All Day Event',
				start: new Date(y, m, 1)
			},
		]
	});
});
