jQuery(document).ready(function() {
	var init={
	};
	jQuery('#log').jlogger(init);

	var init={
		'name':'Name',
		'initState':true,
		'initMsg':'put the name of the movie here',
		'regex':/.+/,
		'logger':'#log',
	};
	jQuery('#name').jvalidfield(init);

	var init={
		'name':'Imdbid',
		'initState':true,
		'initMsg':'put the imdbid here',
		'regex':/^\d{7}$/,
		'logger':'#log',
	};
	jQuery('#imdbid').jvalidfield(init);
	
	var init={
		'name':'Date',
		'initState':true,
		'initMsg':'put the date seen here',
		'regex':/.+/,
		'validate':function(widget,value) {
			var t=Date.parse(value);
			return !isNaN(t);
		},
		'validate_error':function(widget,value) {
			return 'could not parse date object';
		},
		'initval':new Date(),
		'initState':false,
		'logger':'#log',
	};
	jQuery('#date').jvalidfield(init);

	var init={
		'type':'select',
		'name':'Rating',
		'initState':true,
		'initMsg':'put the rating (1-10) here',
		// this is for testing errors in Ajax...
		//'url':'GetData.php?type=TbFoobar',
		'url':'GetData.php?type=TbRating',
		'logger':'#log',
	};
	jQuery('#rating').jvalidfield(init);

	var init={
		'type':'select',
		'name':'Location',
		'initState':true,
		'initMsg':'put the location where you saw the movie',
		'url':'GetData.php?type=video_places',
		'logger':'#log',
	};
	jQuery('#location').jvalidfield(init);
	
	var init={
		'type':'select',
		'name':'Device',
		'initState':true,
		'initMsg':'put the device on which you saw the movie',
		'url':'GetData.php?type=video_devices',
		'logger':'#log',
	};
	jQuery('#device').jvalidfield(init);

	var init={
		'name':'Review',
		'initState':true,
		'initMsg':'Put your review here',
		'type':'textarea',
		'regex':/.+/,
		'logger':'#log',
	};
	jQuery('#review').jvalidfield(init);

	var init={
		'name':'Send',
		'url':'NewMovie.php',
		'logger':'#log',
		'formid':'#myform',
	};
	jQuery('#send').jsubmit(init);
});
