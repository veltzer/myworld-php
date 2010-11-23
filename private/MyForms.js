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
		'sname':'name',
		'initState':true,
		'initMsg':'put the name of the movie here',
		'regex':/.+/,
		'logger':'#log',
		'submit':'#movie_send',
	};
	jQuery('#movie_name').jvalidfield(init);

	var init={
		'name':'Imdbid',
		'sname':'imdbid',
		'initState':true,
		'initMsg':'put the imdbid here',
		'regex':/^\d{7}$/,
		'logger':'#log',
		'submit':'#movie_send',
	};
	jQuery('#movie_imdbid').jvalidfield(init);

	var init={
		'name':'Date',
		'sname':'date',
		'initState':false,
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
		'logger':'#log',
		'submit':'#movie_send',
	};
	jQuery('#movie_date').jvalidfield(init);

	var init={
		'name':'Rating',
		'sname':'rating',
		'initState':true,
		'initMsg':'put the rating (1-10) here',
		'url':'GetData.php?type=TbRating',
		'type':'select',
	};
	jQuery('#movie_rating').jvalidfield(init);

	var init={
		'type':'select',
		'name':'Location',
		'sname':'locationId',
		'initState':true,
		'initMsg':'put the location where you saw the movie',
		'url':'GetData.php?type=video_places',
		'logger':'#log',
		'submit':'#movie_send',
	};
	jQuery('#movie_location').jvalidfield(init);

	var init={
		'type':'select',
		'name':'Device',
		'sname':'deviceId',
		'initState':true,
		'initMsg':'put the device on which you saw the movie',
		'url':'GetData.php?type=video_devices',
		'logger':'#log',
		'submit':'#movie_send',
	};
	jQuery('#movie_device').jvalidfield(init);

	var init={
		'name':'Review',
		'sname':'review',
		'initState':true,
		'initMsg':'Put your review here',
		'type':'textarea',
		'regex':/.+/,
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
		'sname':'name',
		'logger':'#log',
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
		'regex':/^[\w \-\:]+$/,
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

	// work -> external stuff

	var init={
		'name':'Send',
		'url':'NewWorkExternal.php',
		'logger':'#log',
		'formid':'#workexternal_form',
	};
	jQuery('#workexternal_send').jsubmit(init);

	var init={
		'type':'select',
		'name':'External Id',
		'sname':'externalId',
		'initState':true,
		'initMsg':'Put the type of external id here',
		'url':'GetData.php?type=TbExternalType',
		'logger':'#log',
		'submit':'#workexternal_send',
	};
	jQuery('#workexternal_externalid').jvalidfield(init);

	var init={
		'name':'External Code',
		'sname':'externalCode',
		'initState':true,
		'initMsg':'Put the external code here',
		'regex':/^.*$/,
		'logger':'#log',
		'submit':'#workexternal_send',
	};
	jQuery('#workexternal_externalcode').jvalidfield(init);

	var init={
		'type':'select',
		'name':'Work',
		'sname':'workId',
		'initState':true,
		'initMsg':'Put the work id here',
		'url':'GetData.php?type=TbWkWork',
		'logger':'#log',
		'submit':'#workexternal_send',
	};
	jQuery('#workexternal_workid').jvalidfield(init);

	// person -> external stuff

	var init={
		'name':'Send',
		'url':'NewPersonExternal.php',
		'logger':'#log',
		'formid':'#personexternal_form',
	};
	jQuery('#personexternal_send').jsubmit(init);

	var init={
		'type':'select',
		'name':'External Id',
		'sname':'externalId',
		'initState':true,
		'initMsg':'Put the type of external id here',
		'url':'GetData.php?type=TbExternalType',
		'logger':'#log',
		'submit':'#personexternal_send',
	};
	jQuery('#personexternal_externalid').jvalidfield(init);

	var init={
		'name':'External Code',
		'sname':'externalCode',
		'initState':true,
		'initMsg':'Put the external code here',
		'regex':/^.*$/,
		'logger':'#log',
		'submit':'#personexternal_send',
	};
	jQuery('#personexternal_externalcode').jvalidfield(init);

	var init={
		'type':'select',
		'name':'Person',
		'sname':'personId',
		'initState':true,
		'initMsg':'Put the person id here',
		'url':'GetData.php?type=TbIdPerson',
		'logger':'#log',
		'submit':'#personexternal_send',
	};
	jQuery('#personexternal_personid').jvalidfield(init);

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
		'initMsg':'Put the work id here',
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
		'initMsg':'Put the person id here',
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
		'initMsg':'Put the type id here',
		'url':'GetData.php?type=TbWkWorkContribType',
		'logger':'#log',
		'submit':'#personwork_send',
	};
	jQuery('#personwork_typeid').jvalidfield(init);

	// organization -> work stuff

	var init={
		'name':'Send',
		'url':'NewOrganizationWork.php',
		'logger':'#log',
		'formid':'#orgwork_form',
	};
	jQuery('#orgwork_send').jsubmit(init);

	var init={
		'type':'select',
		'name':'Work',
		'sname':'workId',
		'initState':true,
		'initMsg':'Put the work id here',
		'url':'GetData.php?type=TbWkWork',
		'logger':'#log',
		'submit':'#orgwork_send',
	};
	jQuery('#orgwork_workid').jvalidfield(init);

	var init={
		'type':'select',
		'name':'Organization',
		'sname':'organizationId',
		'initState':true,
		'initMsg':'Put the organization id here',
		'url':'GetData.php?type=TbOrganization',
		'logger':'#log',
		'submit':'#orgwork_send',
	};
	jQuery('#orgwork_organizationid').jvalidfield(init);

	var init={
		'type':'select',
		'name':'Type',
		'sname':'typeId',
		'initState':true,
		'initMsg':'Put the type id here',
		'url':'GetData.php?type=TbWkWorkContribType',
		'logger':'#log',
		'submit':'#orgwork_send',
	};
	jQuery('#orgwork_typeid').jvalidfield(init);

	// workview stuff starts here

	var init={
		'name':'Send',
		'url':'NewWorkView.php',
		'logger':'#log',
		'formid':'#workview_form',
	};
	jQuery('#workview_send').jsubmit(init);

	var init={
		'type':'select',
		'name':'Work',
		'sname':'workId',
		'initState':true,
		'initMsg':'Put the work id here',
		'url':'GetData.php?type=TbWkWork',
		'logger':'#log',
		'submit':'#workview_send',
	};
	jQuery('#workview_workid').jvalidfield(init);

	var init={
		'name':'Date',
		'sname':'date',
		'initState':false,
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
		'logger':'#log',
		'submit':'#workview_send',
	};
	jQuery('#workview_date').jvalidfield(init);

	var init={
		'type':'select',
		'name':'Location',
		'sname':'locationId',
		'initState':true,
		'initMsg':'put the location where you saw the movie',
		'url':'GetData.php?type=TbLcNamed',
		'logger':'#log',
		'submit':'#workview_send',
	};
	jQuery('#workview_locationid').jvalidfield(init);

	var init={
		'type':'select',
		'name':'Device',
		'sname':'deviceId',
		'initState':true,
		'initMsg':'put the device on which you saw the movie',
		'url':'GetData.php?type=TbDevice',
		'logger':'#log',
		'submit':'#workview_send',
	};
	jQuery('#workview_deviceid').jvalidfield(init);

	var init={
		'type':'select',
		'name':'Rating',
		'sname':'rating',
		'initState':true,
		'initMsg':'put the rating (1-10) here',
		'url':'GetData.php?type=TbRating',
		'submit':'#workview_send',
	};
	jQuery('#workview_rating').jvalidfield(init);

	var init={
		'name':'Review',
		'sname':'review',
		'initState':true,
		'initMsg':'Put your review here',
		'type':'textarea',
		'regex':/.+/,
		'logger':'#log',
		'submit':'#workview_send',
	};
	jQuery('#workview_review').jvalidfield(init);
});
