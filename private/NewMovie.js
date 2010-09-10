jQuery(document).ready(function() {
	var init={
		'name':'Name',
		'initMsg':'put the name of the movie here',
		'regex':/.+/,
	};
	jQuery('#name').jvalidfield(init);

	var init={
		'name':'Imdbid',
		'initMsg':'put the imdbid here',
		'regex':/^\d{7}$/,
	};
	jQuery('#imdbid').jvalidfield(init);
	
	var init={
		'name':'Date',
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
	};
	jQuery('#date').jvalidfield(init);

	var init={
		'type':'select',
		'name':'Rating',
		'initMsg':'put the rating (1-10) here',
		// this is for testing errors in Ajax...
		//'url':'GetList.php?table=TbFoobar',
		'url':'GetList.php?table=TbRating',
		'initval':6,
	};
	jQuery('#rating').jvalidfield(init);

	var init={
		'type':'select',
		'name':'Location',
		'initMsg':'put the location where you saw the movie',
		'url':'GetList.php?table=TbLcNamed',
		'initval':6,
	};
	jQuery('#location').jvalidfield(init);

	var init={
		'name':'Review',
		'initMsg':'Put your review here',
		'type':'textarea',
		'regex':/.+/,
	};
	jQuery('#review').jvalidfield(init);
});
