jQuery(document).ready(function() {
	// this is the accordion support function
	jQuery("div.myacc_header").click(function(){
		jQuery(this).next("div").slideToggle();
		jQuery(this).toggleClass("active");
	});
	// this is the calendar support fuction
	jQuery('#calendar').fullCalendar({
		editable: false,
		events: jQuery.fullCalendar.gcalFeed('http://www.google.com/calendar/feeds/usa__en%40holiday.calendar.google.com/public/basic'),
		eventClick: function(event) {
			// opens events in a popup window
			window.open(event.url, 'gcalevent', 'width=700,height=600');
			return false;
		},
		loading: function(bool) {
			if (bool) {
				jQuery('#loading').show();
			}else{
				jQuery('#loading').hide();
			}
		},
	});
});
