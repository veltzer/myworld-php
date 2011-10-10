jQuery(document).ready(function() {
	// create the tabs widget
	jQuery("#tabs").tabs();

	// order of creating is important here...
	// since widgets use the logger and submit then they must
	// be created BEFORE the widgets...

	jQuery('#log').cont_logger();

	var init={
		'name':'Send',
		'url':'NewMovie.php',
		'logger':'#log',
		'formid':'#movie_form',
	};
	jQuery('#movie_send').cont_submit(init);

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
	jQuery('#movie_personid').cont_valid(init);

	var init={
		'name':'Name',
		'sname':'name',
		'initState':true,
		'initMsg':'put the name of the movie here',
		'regex':/.+/, // because they can be in hebrew
		'logger':'#log',
		'submit':'#movie_send',
	};
	jQuery('#movie_name').cont_valid(init);

	var init={
		'name':'Imdbid',
		'sname':'imdbid',
		'initState':true,
		'initMsg':'put the imdbid here',
		'regex':/^\d{7}$/,
		'logger':'#log',
		'submit':'#movie_send',
	};
	jQuery('#movie_imdbid').cont_valid(init);

	var init={
		'name':'Date',
		'sname':'date',
		'logger':'#log',
		'submit':'#movie_send',
	};
	jQuery('#movie_date').cont_datetime(init);

	var init={
		'type':'select',
		'name':'Location',
		'sname':'locationId',
		'initState':false,
		'initMsg':'put the location where you saw the movie',
		'initVal':2, // 2 stands for home
		'url':'GetData.php?type=video_places',
		'logger':'#log',
		'submit':'#movie_send',
	};
	jQuery('#movie_locationid').cont_valid(init);

	var init={
		'type':'select',
		'name':'Device',
		'sname':'deviceId',
		'initState':false,
		'initMsg':'put the device on which you saw the movie',
		'url':'GetData.php?type=video_devices',
		'initVal':9, // 9 stands for dvd/recorder
		'logger':'#log',
		'submit':'#movie_send',
	};
	jQuery('#movie_deviceid').cont_valid(init);

	var init={
		'name':'Rating',
		'sname':'ratingId',
		'initState':true,
		'initMsg':'put the rating (1-10) here',
		'url':'GetData.php?type=TbRating',
		'type':'select',
	};
	jQuery('#movie_ratingid').cont_valid(init);

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
	jQuery('#movie_review').cont_valid(init);

	// event stuff starts here...

	// first create the event send button
	var init={
		'name':'Send',
		'url':'NewEvent.php',
		'logger':'#log',
		'formid':'#event_form',
	};
	jQuery('#event_send').cont_submit(init);

	// construct two date pickers
	init.sname='from_date';
	init.name='From Date';
	jQuery('#event_from_date').cont_datepicker(init);
	init.sname='to_date';
	init.name='To Date';
	jQuery('#event_to_date').cont_datepicker(init);

	// the time pickers
	//var init={ 'secView':false };
	init.sname='from_time';
	init.name='From time';
	jQuery('#event_from_time').cont_time(init);
	init.sname='to_time';
	init.name='To Time';
	jQuery('#event_to_time').cont_time(init);

	var init={
		'name':'Name',
		'sname':'name',
		'initState':false,
		'regex':/^\w*$/,
		'logger':'#log',
		'submit':'#event_send',
	};
	jQuery('#event_name').cont_valid(init);

	// my own fields (from the database)
	var init={
		'type':'select',
		'name':'Calendar',
		'sname':'name',
		'logger':'#log',
		'submit':'#event_send',
	};

	init.url='GetData.php?type=TbClCalendar';
	jQuery('#event_calendarid').cont_valid(init);

	init.url='GetData.php?type=TbOrganization';
	init.name='Company';
	jQuery('#event_companyid').cont_valid(init);

	init.url='GetData.php?type=TbBsCourses';
	init.name='Course';
	jQuery('#event_courseid').cont_valid(init);

	init.url='GetData.php?type=TbLocation';
	init.name='Location';
	jQuery('#event_locationid').cont_valid(init);

	init.url='GetData.php?type=TbIdPerson';
	init.name='Person';
	init.initVal=1;
	jQuery('#event_personid').cont_valid(init);
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
	jQuery('#event_remark').cont_valid(init);

	// person stuff starts here

	var init={
		'name':'Send',
		'url':'NewPerson.php',
		'logger':'#log',
		'formid':'#person_form',
	};
	jQuery('#person_send').cont_submit(init);

	var init={
		'type':'select',
		'name':'Honorific',
		'sname':'honorificId',
		'initVal':1, // this is the None value from the TbIdHonorific table (TODO)
		'url':'GetData.php?type=TbIdHonorific',
		'logger':'#log',
		'submit':'#person_send',
	};
	jQuery('#person_honorificId').cont_valid(init);

	var init={
		'logger':'#log',
		'submit':'#person_send',
		//'regex':/^[\w\.]*$/,
		'regex':/.*/, // because they can be in hebrew
	};
	init.name='First Name';
	init.sname='firstname';
	jQuery('#person_firstname').cont_valid(init);

	init.name='Surname';
	init.sname='surname';
	jQuery('#person_surname').cont_valid(init);

	init.name='Other Name';
	init.sname='othername';
	//init.regex=/^[\w\.]*$/;
	jQuery('#person_othername').cont_valid(init);

	init.name='Remark';
	init.sname='remark';
	//init.regex=/^[\w\. ]*$/;
	jQuery('#person_remark').cont_valid(init);

	init.name='Ordinal';
	init.sname='ordinal';
	init.regex=/^\d*$/;
	jQuery('#person_ordinal').cont_valid(init);

	// work stuff starts here

	var init={
		'name':'Send',
		'url':'NewWork.php',
		'logger':'#log',
		'formid':'#work_form',
	};
	jQuery('#work_send').cont_submit(init);

	var init={
		'name':'Name',
		'sname':'name',
		'initState':true,
		'initMsg':'Put the name of the work here',
		//'regex':/^[\w \-\:\,\']+$/,
		'regex':/.+/, // because they can be in hebrew
		'logger':'#log',
		'submit':'#work_send',
	};
	jQuery('#work_name').cont_valid(init);

	var init={
		'type':'select',
		'name':'Type',
		'sname':'typeId',
		'initState':true,
		'initMsg':'Put type of work here',
		'url':'GetData.php?type=TbWkWorkType',
		'logger':'#log',
		'submit':'#work_send',
	};
	jQuery('#work_typeid').cont_valid(init);

	var init={
		'type':'select',
		'name':'Language',
		'sname':'languageId',
		'initState':true,
		'initMsg':'Put language of work here',
		'initVal':144, // this is english from the languages table and is ugly (TODO)
		'url':'GetData.php?type=TbLanguage',
		'logger':'#log',
		'submit':'#work_send',
	};
	jQuery('#work_languageid').cont_valid(init);

	// work -> external stuff

	var init={
		'name':'Send',
		'url':'NewWorkExternal.php',
		'logger':'#log',
		'formid':'#workexternal_form',
	};
	jQuery('#workexternal_send').cont_submit(init);

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
	jQuery('#workexternal_workid').cont_valid(init);

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
	jQuery('#workexternal_externalid').cont_valid(init);

	var init={
		'name':'External Code',
		'sname':'externalCode',
		'initState':true,
		'initMsg':'Put the external code here',
		'regex':/^.*$/,
		'logger':'#log',
		'submit':'#workexternal_send',
	};
	jQuery('#workexternal_externalcode').cont_valid(init);

	// person -> external stuff

	var init={
		'name':'Send',
		'url':'NewPersonExternal.php',
		'logger':'#log',
		'formid':'#personexternal_form',
	};
	jQuery('#personexternal_send').cont_submit(init);

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
	jQuery('#personexternal_externalid').cont_valid(init);

	var init={
		'name':'External Code',
		'sname':'externalCode',
		'initState':true,
		'initMsg':'Put the external code here',
		'regex':/^.*$/,
		'logger':'#log',
		'submit':'#personexternal_send',
	};
	jQuery('#personexternal_externalcode').cont_valid(init);

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
	jQuery('#personexternal_personid').cont_valid(init);

	// person -> work starts here

	var init={
		'name':'Send',
		'url':'NewPersonWork.php',
		'logger':'#log',
		'formid':'#personwork_form',
	};
	jQuery('#personwork_send').cont_submit(init);

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
	jQuery('#personwork_workid').cont_valid(init);

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
	jQuery('#personwork_personid').cont_valid(init);

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
	jQuery('#personwork_typeid').cont_valid(init);

	// organization -> work stuff

	var init={
		'name':'Send',
		'url':'NewOrganizationWork.php',
		'logger':'#log',
		'formid':'#orgwork_form',
	};
	jQuery('#orgwork_send').cont_submit(init);

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
	jQuery('#orgwork_workid').cont_valid(init);

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
	jQuery('#orgwork_organizationid').cont_valid(init);

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
	jQuery('#orgwork_typeid').cont_valid(init);

	// workview stuff starts here

	var init={
		'name':'Send',
		'url':'NewWorkView.php',
		'logger':'#log',
		'formid':'#workview_form',
	};
	jQuery('#workview_send').cont_submit(init);

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
	jQuery('#workview_personid').cont_valid(init);

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
	jQuery('#workview_workid').cont_valid(init);

	var init={
		'name':'Date',
		'sname':'date',
		'logger':'#log',
		'submit':'#workview_send',
	};
	jQuery('#workview_date').cont_datetime(init);

	var init={
		'type':'select',
		'name':'Location',
		'sname':'locationId',
		'initState':false,
		'initMsg':'put the location where you saw the movie',
		'initVal':2, // 2 stands for home
		'url':'GetData.php?type=TbLocation',
		'logger':'#log',
		'submit':'#workview_send',
	};
	jQuery('#workview_locationid').cont_valid(init);

	var init={
		'type':'select',
		'name':'Device',
		'sname':'deviceId',
		'initState':false,
		'initMsg':'put the device on which you saw the movie',
		'initVal':1, // 1 stands for cowon player
		'url':'GetData.php?type=TbDevice',
		'logger':'#log',
		'submit':'#workview_send',
	};
	jQuery('#workview_deviceid').cont_valid(init);

	var init={
		'type':'select',
		'name':'Language',
		'sname':'langId',
		'initState':true,
		'initMsg':'Put language of work here',
		'initVal':144, // this is english from the languages table (ugly!!!)
		'url':'GetData.php?type=TbLanguage',
		'logger':'#log',
		'submit':'#workview_send',
	};
	jQuery('#workview_langid').cont_valid(init);

	var init={
		'type':'select',
		'name':'Rating',
		'sname':'ratingId',
		'initState':true,
		'initMsg':'put the rating (1-10) here',
		'url':'GetData.php?type=TbRating',
		'submit':'#workview_send',
	};
	jQuery('#workview_ratingid').cont_valid(init);

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
	jQuery('#workview_review').cont_valid(init);

	// done stuff starts here

	var init={
		'name':'Send',
		'url':'NewDone.php',
		'logger':'#log',
		'formid':'#done_form',
	};
	jQuery('#done_send').cont_submit(init);

	var init={
		'name':'End Date',
		'sname':'end',
		'logger':'#log',
		'submit':'#done_send',
	};
	jQuery('#done_end').cont_datetime(init);

	var init={
		'type':'select',
		'name':'Location',
		'sname':'locationId',
		'initState':false,
		'initMsg':'put the location where the activity took place',
		'initVal':2, // 2 stands for home
		'url':'GetData.php?type=TbLocation',
		'logger':'#log',
		'submit':'#done_send',
	};
	jQuery('#done_locationid').cont_valid(init);

	var init={
		'type':'select',
		'name':'Activity',
		'sname':'activityId',
		'initState':false,
		'initMsg':'what is the activity type',
		'initVal':1, // 1 stands for Drums training
		'url':'GetData.php?type=TbTdActivity',
		'logger':'#log',
		'submit':'#done_send',
	};
	jQuery('#done_activityid').cont_valid(init);

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
	jQuery('#done_remark').cont_valid(init);

	// workviewreview stuff starts here

	var init={
		'name':'Send',
		'url':'NewWorkViewReview.php',
		'logger':'#log',
		'formid':'#workviewreview_form',
	};
	jQuery('#workviewreview_send').cont_submit(init);

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
	jQuery('#workviewreview_name').cont_valid(init);

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
	jQuery('#workviewreview_typeid').cont_valid(init);

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
	jQuery('#workviewreview_externalid').cont_valid(init);

	var init={
		'name':'External Code',
		'sname':'externalCode',
		'initState':true,
		'initMsg':'Put the external code here',
		'regex':/^.*$/,
		'logger':'#log',
		'submit':'#workviewreview_send',
	};
	jQuery('#workviewreview_externalcode').cont_valid(init);

	var init={
		'name':'Start date',
		'sname':'start',
		'logger':'#log',
		'submit':'#workviewreview_send',
	};
	jQuery('#workviewreview_start').cont_datetime(init);

	var init={
		'name':'End date',
		'sname':'end',
		'logger':'#log',
		'submit':'#workviewreview_send',
	};
	jQuery('#workviewreview_end').cont_datetime(init);

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
	jQuery('#workviewreview_personid').cont_valid(init);

	var init={
		'type':'select',
		'name':'Location',
		'sname':'locationId',
		'initState':false,
		'initMsg':'put the location where you viewed the work',
		'initVal':2, // 2 stands for home
		'url':'GetData.php?type=TbLocation',
		'logger':'#log',
		'submit':'#workviewreview_send',
	};
	jQuery('#workviewreview_locationid').cont_valid(init);

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
	jQuery('#workviewreview_deviceid').cont_valid(init);

	var init={
		'name':'Rating',
		'sname':'ratingId',
		'initState':true,
		'initMsg':'put the rating (1-10) here',
		'url':'GetData.php?type=TbRating',
		'type':'select',
	};
	jQuery('#workviewreview_ratingid').cont_valid(init);

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
	jQuery('#workviewreview_review').cont_valid(init);
});
