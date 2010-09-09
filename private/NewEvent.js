jQuery(document).ready(function() {
	// construct two date pickers
	jQuery('#from_date').datepicker();
	jQuery('#to_date').datepicker();
	var init={ 'secView':false };
	jQuery('#from_time').jtimepicker(init);
	jQuery('#to_time').jtimepicker(init);

	jQuery("#calendar").reload('TbClCalendar');
	jQuery('#company').reload('TbBsCompanies');
	jQuery('#course').reload('TbBsCourses');
	jQuery('#location').reload('TbLcNamed');
	jQuery('#creator').reload('TbIdPerson');
	jQuery('#reload_calendar').click(function() {
		jQuery("#calendar").reload('TbClCalendar');
	});
	jQuery('#reload_company').click(function() {
		jQuery('#company').reload('TbBsCompanies');
	});
	jQuery('#reload_course').click(function() {
		jQuery('#course').reload('TbBsCourses');
	});
	jQuery('#reload_location').click(function() {
		jQuery('#location').reload('TbLcNamed');
	});
	jQuery('#reload_creator').click(function() {
		jQuery('#creator').reload('TbIdPerson');
	});
});
