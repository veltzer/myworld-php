jQuery(document).ready(function() {
	// create the tabs widget
	jQuery("#tabs").tabs();

	// order of creating is important here...
	// since widgets use the logger and submit then they must
	// be created BEFORE the widgets...

	jQuery('#log').jlogger();

	var init={
		'name':'Send',
		'url':'NewMovie.php',
		'logger':'#log',
		'formid':'#movie_form',
	};
	jQuery('#movie_send').jsubmit(init);

	var init={
		'name':'Name',
		'initState':true,
		'initMsg':'put the name of the movie here',
		'regex':/.+/,
		'sname':'name',
		'logger':'#log',
		'submit':'#movie_send',
	};
	jQuery('#movie_name').jvalidfield(init);

	var init={
		'name':'Imdbid',
		'initState':true,
		'initMsg':'put the imdbid here',
		'regex':/^\d{7}$/,
		'sname':'imdbid',
		'logger':'#log',
		'submit':'#movie_send',
	};
	jQuery('#movie_imdbid').jvalidfield(init);

	var init={
		'name':'Date',
		'initState':false,
		'initMsg':'put the date seen here',
		'regex':/.+/,
		'sname':'date',
		'validate':function(widget,value) {
			var t=Date.parse(value);
			return !isNaN(t);
		},
		'validate_error':function(widget,value) {
			return 'could not parse date object';
		},
		'initval':new Date(),
		'logger':'#log',
		'submit':'#movie_send',
	};
	jQuery('#movie_date').jvalidfield(init);

	var init={
		'name':'Rating',
		'initState':true,
		'initMsg':'put the rating (1-10) here',
		'url':'GetData.php?type=TbRating',
		'sname':'rating',
		'type':'select',
	};
	jQuery('#movie_rating').jvalidfield(init);

	var init={
		'type':'select',
		'name':'Location',
		'initState':true,
		'initMsg':'put the location where you saw the movie',
		'url':'GetData.php?type=video_places',
		'sname':'locationid',
		'logger':'#log',
		'submit':'#movie_send',
	};
	jQuery('#movie_location').jvalidfield(init);

	var init={
		'type':'select',
		'name':'Device',
		'initState':true,
		'initMsg':'put the device on which you saw the movie',
		'url':'GetData.php?type=video_devices',
		'sname':'deviceid',
		'logger':'#log',
		'submit':'#movie_send',
	};
	jQuery('#movie_device').jvalidfield(init);

	var init={
		'name':'Review',
		'initState':true,
		'initMsg':'Put your review here',
		'type':'textarea',
		'regex':/.+/,
		'sname':'review',
		'logger':'#log',
		'submit':'#movie_send',
	};
	jQuery('#movie_review').jvalidfield(init);

	// event stuff starts here...

	// first create the event send button
	var init={
		'name':'Send',
		'url':'NewEvent.php',
		'logger':'#log',
		'formid':'#event_form',
	};
	jQuery('#event_send').jsubmit(init);

	// construct two date pickers
	jQuery('#event_from_date').datepicker();
	jQuery('#event_to_date').datepicker();

	// the time pickers
	var init={ 'secView':false };
	//jQuery('#event_from_time').jtimepicker(init);
	//jQuery('#event_to_time').jtimepicker(init);

	// my own fields (from the database)
	var init={
		'type':'select',
		'name':'Calendar',
		'logger':'#log',
		'sname':'name',
		'submit':'#event_send',
	};

	init.url='GetData.php?type=TbClCalendar';
	jQuery('#event_calendar').jvalidfield(init);

	init.url='GetData.php?type=TbBsCompanies';
	init.name='Company';
	jQuery('#event_company').jvalidfield(init);

	init.url='GetData.php?type=TbBsCourses';
	init.name='Course';
	jQuery('#event_course').jvalidfield(init);

	init.url='GetData.php?type=TbLcNamed';
	init.name='Location';
	jQuery('#event_location').jvalidfield(init);

	init.url='GetData.php?type=TbIdPerson';
	init.name='Creator';
	jQuery('#event_creator').jvalidfield(init);

});
