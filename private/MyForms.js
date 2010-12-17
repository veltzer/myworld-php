jQuery(document).ready(function() {
	// create the tabs widget
	jQuery("#tabs").tabs();

	// order of creating is important here...
	// since widgets use the logger and submit then they must
	// be created BEFORE the widgets...

	jQuery('#log').logger();

	var init={
		'name':'Send',
		'url':'NewMovie.php',
		'logger':'#log',
		'formid':'#movie_form',
	};
	jQuery('#movie_send').jsubmit(init);
	
	var init={
		'type':'select',
		'name':'Person',
		'sname':'personId',
		'initState':false,
		'initMsg':'Put the person id here',
		'initVal':1,
		'url':'GetData.php?type=TbIdPerson',
		'logger':'#log',
		'submit':'#movie_send',
	};
	jQuery('#movie_personid').valid(init);

	var init={
		'name':'Name',
		'sname':'name',
		'initState':true,
		'initMsg':'put the name of the movie here',
		'regex':/.+/,
		'logger':'#log',
		'submit':'#movie_send',
	};
	jQuery('#movie_name').valid(init);

	var init={
		'name':'Imdbid',
		'sname':'imdbid',
		'initState':true,
		'initMsg':'put the imdbid here',
		'regex':/^\d{7}$/,
		'logger':'#log',
		'submit':'#movie_send',
	};
	jQuery('#movie_imdbid').valid(init);

	var init={
		'name':'Date',
		'sname':'date',
		'logger':'#log',
		'submit':'#movie_send',
	};
	jQuery('#movie_date').datetime(init);

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
	jQuery('#movie_locationid').valid(init);

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
	jQuery('#movie_deviceid').valid(init);

	var init={
		'name':'Rating',
		'sname':'ratingId',
		'initState':true,
		'initMsg':'put the rating (1-10) here',
		'url':'GetData.php?type=TbRating',
		'type':'select',
	};
	jQuery('#movie_ratingid').valid(init);

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
	jQuery('#movie_review').valid(init);

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
	//jQuery('#event_from_date').datepicker();
	//jQuery('#event_to_date').datepicker();

	// the time pickers
	//var init={ 'secView':false };
	//jQuery('#event_from_time').jtimepicker(init);
	//jQuery('#event_to_time').jtimepicker(init);

	var init={
		'name':'Name',
		'sname':'name',
		'initState':false,
		'regex':/^\w*$/,
		'logger':'#log',
		'submit':'#event_send',
	};
	jQuery('#event_name').valid(init);

	// my own fields (from the database)
	var init={
		'type':'select',
		'name':'Calendar',
		'sname':'name',
		'logger':'#log',
		'submit':'#event_send',
	};

	init.url='GetData.php?type=TbClCalendar';
	jQuery('#event_calendarid').valid(init);

	init.url='GetData.php?type=TbBsCompanies';
	init.name='Company';
	jQuery('#event_companyid').valid(init);

	init.url='GetData.php?type=TbBsCourses';
	init.name='Course';
	jQuery('#event_courseid').valid(init);

	init.url='GetData.php?type=TbLocation';
	init.name='Location';
	jQuery('#event_locationid').valid(init);

	init.url='GetData.php?type=TbIdPerson';
	init.name='Person';
	init.initVal=1;
	jQuery('#event_personid').valid(init);
	delete init.initVal;

	var init={
		'name':'Remark',
		'sname':'remark',
		'initState':false,
		'initMsg':'Put your remark here',
		'type':'textarea',
		'regex':/^.*$/,
		'logger':'#log',
		'submit':'#event_send',
	};
	jQuery('#event_remark').valid(init);

	// person stuff starts here

	var init={
		'name':'Send',
		'url':'NewPerson.php',
		'logger':'#log',
		'formid':'#person_form',
	};
	jQuery('#person_send').jsubmit(init);

	var init={
		'name':'Honorific',
		'sname':'honorific',
		'initState':false,
		'regex':/^\w*$/,
		'logger':'#log',
		'submit':'#person_send',
	};
	jQuery('#person_honorific').valid(init);

	init.name='First Name';
	init.sname='firstname';
	jQuery('#person_firstname').valid(init);

	init.name='Surname';
	init.sname='surname';
	jQuery('#person_surname').valid(init);

	init.name='Other Name';
	init.sname='othername';
	init.regex=/^[\w\.]*$/;
	jQuery('#person_othername').valid(init);

	init.name='Remark';
	init.sname='remark';
	init.regex=/^[\w\. ]*$/;
	jQuery('#person_remark').valid(init);

	init.name='Ordinal';
	init.sname='ordinal';
	init.regex=/^\d*$/;
	jQuery('#person_ordinal').valid(init);

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
		'regex':/^[\w \-\:\,\']+$/,
		'logger':'#log',
		'submit':'#work_send',
	};
	jQuery('#work_name').valid(init);

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
	jQuery('#work_typeid').valid(init);

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
		'name':'Work',
		'sname':'workId',
		'initState':true,
		'initMsg':'Put the work id here',
		'url':'GetData.php?type=TbWkWork',
		'logger':'#log',
		'submit':'#workexternal_send',
	};
	jQuery('#workexternal_workid').valid(init);

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
	jQuery('#workexternal_externalid').valid(init);

	var init={
		'name':'External Code',
		'sname':'externalCode',
		'initState':true,
		'initMsg':'Put the external code here',
		'regex':/^.*$/,
		'logger':'#log',
		'submit':'#workexternal_send',
	};
	jQuery('#workexternal_externalcode').valid(init);

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
	jQuery('#personexternal_externalid').valid(init);

	var init={
		'name':'External Code',
		'sname':'externalCode',
		'initState':true,
		'initMsg':'Put the external code here',
		'regex':/^.*$/,
		'logger':'#log',
		'submit':'#personexternal_send',
	};
	jQuery('#personexternal_externalcode').valid(init);

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
	jQuery('#personexternal_personid').valid(init);

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
	jQuery('#personwork_workid').valid(init);

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
	jQuery('#personwork_personid').valid(init);

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
	jQuery('#personwork_typeid').valid(init);

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
	jQuery('#orgwork_workid').valid(init);

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
	jQuery('#orgwork_organizationid').valid(init);

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
	jQuery('#orgwork_typeid').valid(init);

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
		'name':'Viewing person',
		'sname':'personId',
		'initState':false,
		'initMsg':'Put the person id here',
		'initVal':1,
		'url':'GetData.php?type=TbIdPerson',
		'logger':'#log',
		'submit':'#workview_send',
	};
	jQuery('#workview_personid').valid(init);

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
	jQuery('#workview_workid').valid(init);

	var init={
		'name':'Date',
		'sname':'date',
		'logger':'#log',
		'submit':'#workview_send',
	};
	jQuery('#workview_date').datetime(init);

	var init={
		'type':'select',
		'name':'Location',
		'sname':'locationId',
		'initState':true,
		'initMsg':'put the location where you saw the movie',
		'url':'GetData.php?type=TbLocation',
		'logger':'#log',
		'submit':'#workview_send',
	};
	jQuery('#workview_locationid').valid(init);

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
	jQuery('#workview_deviceid').valid(init);

	var init={
		'type':'select',
		'name':'Rating',
		'sname':'ratingId',
		'initState':true,
		'initMsg':'put the rating (1-10) here',
		'url':'GetData.php?type=TbRating',
		'submit':'#workview_send',
	};
	jQuery('#workview_ratingid').valid(init);

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
	jQuery('#workview_review').valid(init);

	// done stuff starts here

	var init={
		'name':'Send',
		'url':'NewDone.php',
		'logger':'#log',
		'formid':'#done_form',
	};
	jQuery('#done_send').jsubmit(init);

	var init={
		'name':'End Date',
		'sname':'end',
		'logger':'#log',
		'submit':'#done_send',
	};
	jQuery('#done_end').datetime(init);

	var init={
		'type':'select',
		'name':'Location',
		'sname':'locationId',
		'initState':true,
		'initMsg':'put the location where the activity took place',
		'url':'GetData.php?type=TbLocation',
		'logger':'#log',
		'submit':'#done_send',
	};
	jQuery('#done_locationid').valid(init);

	var init={
		'type':'select',
		'name':'Activity',
		'sname':'activityId',
		'initState':true,
		'initMsg':'what is the activity type',
		'url':'GetData.php?type=TbTdActivity',
		'logger':'#log',
		'submit':'#done_send',
	};
	jQuery('#done_activityid').valid(init);

	var init={
		'name':'Remark',
		'sname':'remark',
		'initState':false,
		'initMsg':'Put your remark here',
		'type':'textarea',
		'regex':/^.*$/,
		'logger':'#log',
		'submit':'#done_send',
	};
	jQuery('#done_remark').valid(init);

	// workviewreview stuff starts here

	var init={
		'name':'Send',
		'url':'NewWorkViewReview.php',
		'logger':'#log',
		'formid':'#workviewreview_form',
	};
	jQuery('#workviewreview_send').jsubmit(init);

	var init={
		'name':'Name',
		'sname':'name',
		'initState':true,
		'initMsg':'Put the name of the work here',
		//'regex':/^[\w \-\:\,\(\)_\?]+$/,
		'regex':/^.+$/,
		'logger':'#log',
		'submit':'#workviewreview_send',
	};
	jQuery('#workviewreview_name').valid(init);

	var init={
		'type':'select',
		'name':'Type',
		'sname':'typeId',
		'initState':true,
		'initMsg':'Put type type of work here',
		'url':'GetData.php?type=TbWkWorkType',
		'logger':'#log',
		'submit':'#workviewreview_send',
	};
	jQuery('#workviewreview_typeid').valid(init);

	var init={
		'type':'select',
		'name':'External Id',
		'sname':'externalId',
		'initState':true,
		'initMsg':'Put the type of external id here',
		'url':'GetData.php?type=TbExternalType',
		'logger':'#log',
		'submit':'#workviewreview_send',
	};
	jQuery('#workviewreview_externalid').valid(init);

	var init={
		'name':'External Code',
		'sname':'externalCode',
		'initState':true,
		'initMsg':'Put the external code here',
		'regex':/^.*$/,
		'logger':'#log',
		'submit':'#workviewreview_send',
	};
	jQuery('#workviewreview_externalcode').valid(init);

	var init={
		'name':'Start date',
		'sname':'start',
		'logger':'#log',
		'submit':'#workviewreview_send',
	};
	jQuery('#workviewreview_start').datetime(init);

	var init={
		'name':'End date',
		'sname':'end',
		'logger':'#log',
		'submit':'#workviewreview_send',
	};
	jQuery('#workviewreview_end').datetime(init);

	var init={
		'type':'select',
		'name':'Viewing person',
		'sname':'personId',
		'initState':false,
		'initMsg':'Put the person id here',
		'initVal':1,
		'url':'GetData.php?type=TbIdPerson',
		'logger':'#log',
		'submit':'#workviewreview_send',
	};
	jQuery('#workviewreview_personid').valid(init);

	var init={
		'type':'select',
		'name':'Location',
		'sname':'locationId',
		'initState':true,
		'initMsg':'put the location where you viewed the work',
		'url':'GetData.php?type=TbLocation',
		'logger':'#log',
		'submit':'#workviewreview_send',
	};
	jQuery('#workviewreview_locationid').valid(init);

	var init={
		'type':'select',
		'name':'Device',
		'sname':'deviceId',
		'initState':true,
		'initMsg':'put the device on which you saw the movie',
		'url':'GetData.php?type=TbDevice',
		'logger':'#log',
		'submit':'#workviewreview_send',
	};
	jQuery('#workviewreview_deviceid').valid(init);

	var init={
		'name':'Rating',
		'sname':'ratingId',
		'initState':true,
		'initMsg':'put the rating (1-10) here',
		'url':'GetData.php?type=TbRating',
		'type':'select',
	};
	jQuery('#workviewreview_ratingid').valid(init);

	var init={
		'name':'Review',
		'sname':'review',
		'initState':true,
		'initMsg':'Put your review here',
		'type':'textarea',
		'regex':/.+/,
		'logger':'#log',
		'submit':'#workviewreview_send',
	};
	jQuery('#workviewreview_review').valid(init);
});
