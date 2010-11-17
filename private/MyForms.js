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

	// person stuff starts here

	var init={
		'name':'Send',
		'url':'NewPerson.php',
		'logger':'#log',
		'formid':'#person_form',
	};
	jQuery('#person_send').jsubmit(init);

	var init={
		'name':'First Name',
		'sname':'firstname',
		'initState':false,
		'regex':/^\w*$/,
		'logger':'#log',
		'submit':'#person_send',
	};
	jQuery('#person_firstname').jvalidfield(init);

	init.name='Sur Name';
	init.sname='surname';
	jQuery('#person_surname').jvalidfield(init);

	init.name='Other Name';
	init.sname='othername';
	init.regex=/^[\w\.]*$/;
	jQuery('#person_othername').jvalidfield(init);

	init.name='Remark';
	init.sname='remark';
	init.regex=/^[\w\. ]*$/;
	jQuery('#person_remark').jvalidfield(init);

	init.name='Ordinal';
	init.sname='ordinal';
	init.regex=/^\d*$/;
	jQuery('#person_ordinal').jvalidfield(init);

	// work stuff starts here

	var init={
		'name':'Send',
		'url':'NewWork.php',
		'logger':'#log',
		'formid':'#work_form',
	};
	jQuery('#work_send').jsubmit(init);

	var init={
		'name':'Name',
		'sname':'name',
		'initState':true,
		'initMsg':'Put the name of the work here',
		'regex':/^[\w ]+$/,
		'logger':'#log',
		'submit':'#work_send',
	};
	jQuery('#work_name').jvalidfield(init);

	var init={
		'type':'select',
		'name':'Type',
		'sname':'typeId',
		'initState':true,
		'initMsg':'Put type type of work here',
		'url':'GetData.php?type=TbWkWorkType',
		'logger':'#log',
		'submit':'#work_send',
	};
	jQuery('#work_typeid').jvalidfield(init);

	var init={
		'type':'select',
		'name':'External Id',
		'sname':'externalId',
		'initState':true,
		'initMsg':'Put the type of external id here',
		'url':'GetData.php?type=TbWkWorkExternal',
		'logger':'#log',
		'submit':'#work_send',
	};
	jQuery('#work_externalid').jvalidfield(init);

	var init={
		'name':'External Code',
		'sname':'externalCode',
		'initState':true,
		'initMsg':'Put the external code here',
		'regex':/^\w*$/,
		'logger':'#log',
		'submit':'#work_send',
	};
	jQuery('#work_externalcode').jvalidfield(init);

	// person -> work starts here
	
	var init={
		'name':'Send',
		'url':'NewPersonWork.php',
		'logger':'#log',
		'formid':'#personwork_form',
	};
	jQuery('#personwork_send').jsubmit(init);
	
	var init={
		'type':'select',
		'name':'Work',
		'sname':'workId',
		'initState':true,
		'initMsg':'Put the work Id here',
		'url':'GetData.php?type=TbWkWork',
		'logger':'#log',
		'submit':'#personwork_send',
	};
	jQuery('#personwork_workid').jvalidfield(init);
	
	var init={
		'type':'select',
		'name':'Person',
		'sname':'personId',
		'initState':true,
		'initMsg':'Put the person Id here',
		'url':'GetData.php?type=TbIdPerson',
		'logger':'#log',
		'submit':'#personwork_send',
	};
	jQuery('#personwork_personid').jvalidfield(init);
	
	var init={
		'type':'select',
		'name':'Type',
		'sname':'typeId',
		'initState':true,
		'initMsg':'Put the type Id here',
		'url':'GetData.php?type=TbWkWorkContribType',
		'logger':'#log',
		'submit':'#personwork_send',
	};
	jQuery('#personwork_typeid').jvalidfield(init);
});
