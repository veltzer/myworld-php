jQuery(document).ready(function() {
	// construct the logger
	var init={
	};
	jQuery('#log').jlogger(init);

	// construct two date pickers
	jQuery('#from_date').datepicker();
	jQuery('#to_date').datepicker();

	// the time pickers
	var init={ 'secView':false };
	jQuery('#from_time').jtimepicker(init);
	jQuery('#to_time').jtimepicker(init);

	// my own fields (from the database)
	var init={
		'type':'select',
		'name':'Calendar',
		'url':'GetData.php?type=TbClCalendar',
		'logger':'#log',
		'sname':'name',
	};
	jQuery('#calendar').jvalidfield(init);
	
	init.url='GetData.php?type=TbBsCompanies';
	init.name='Company';
	jQuery('#company').jvalidfield(init);
	
	init.url='GetData.php?type=TbBsCourses';
	init.name='Course';
	jQuery('#course').jvalidfield(init);
	
	init.url='GetData.php?type=TbLcNamed';
	init.name='Location';
	jQuery('#location').jvalidfield(init);
	
	init.url='GetData.php?type=TbIdPerson';
	init.name='Creator';
	jQuery('#creator').jvalidfield(init);
	
	var init={
		'name':'Send',
		'url':'NewEvent.php',
		'logger':'#log',
		'formid':'#myform',
	};
	jQuery('#send').jsubmit(init);
});
