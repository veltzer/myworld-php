<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
	<head>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
		<title>MyWorld</title>
		<?php
			echo '<link rel="shortcut icon" href="favicon.ico?t='.time().'" />';
		?>
		<link rel='stylesheet' href='https://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/themes/ui-lightness/jquery-ui.css'/>
		<script src='https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'></script>
		<script src='https://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js'></script>
		<script src='/public/myworld_utils.js'></script>
		<script src='/public/jquery.cont_valid.js'></script>
		<script src='/public/jquery.cont_datetime.js'></script>
		<script src='/public/jquery.cont_datepicker.js'></script>
		<script src='/public/jquery.cont_time.js'></script>
		<script src='/public/jquery.cont_url.js'></script>
		<script src='/public/jquery.cont_combobox.js'></script>
		<script src='/public/jquery.cont_submit.js'></script>
		<script src='/public/jquery.cont_logger.js'></script>
		<script src='/public/forms.js'></script>
		<link rel='stylesheet' type='text/css' href='MyWorld.css'>
	</head>
	<body>
		<div id="tabs">
			<ul>
				<li><a href="#tabs-movie">New Movie</a></li>
				<li><a href="#tabs-movienr">New Movie (no review)</a></li>
				<li><a href="#tabs-event">New Event</a></li>
				<li><a href="#tabs-person">New Person</a></li>
				<li><a href="#tabs-work">New Work</a></li>
				<li><a href="#tabs-workexternal">Work->External</a></li>
				<li><a href="#tabs-personexternal">Person->External</a></li>
				<li><a href="#tabs-personwork">Person->Work</a></li>
				<li><a href="#tabs-orgwork">Org->Work</a></li>
				<li><a href="#tabs-workview">New WorkView</a></li>
				<li><a href="#tabs-done">New Done</a></li>
				<li><a href="#tabs-workviewreview">Work View Review</a></li>
			</ul>
			<div id="tabs-movie">
				<form id='movie_form'>
					<div id='movie_personid'></div>
					<div id='movie_name'></div>
					<div id='movie_imdbid'></div>
					<div id='movie_date'></div>
					<div id='movie_locationid'></div>
					<div id='movie_deviceid'></div>
					<div id='movie_remark'></div>
					<div id='movie_ratingid'></div>
					<div id='movie_review'></div>
					<div id='movie_send'></div>
				</form>
			</div>
			<div id="tabs-movienr">
				<form id='movienr_form'>
					<div id='movienr_personid'></div>
					<div id='movienr_name'></div>
					<div id='movienr_imdbid'></div>
					<div id='movienr_date'></div>
					<div id='movienr_locationid'></div>
					<div id='movienr_deviceid'></div>
					<div id='movienr_remark'></div>
					<div id='movienr_send'></div>
				</form>
			</div>
			<div id="tabs-event">
				<form id='event_form'>
					<div id='event_name'></div>
					<div id='event_calendarid'></div>
					<div id='event_locationid'></div>
					<div id='event_personid'></div>
					<div id='event_companyid'></div>
					<div id='event_courseid'></div>
					<div id='event_remark'></div>
					<div id='event_from_date'></div>
					<div id='event_from_time'></div>
					<div id='event_to_date'></div>
					<div id='event_to_time'></div>
					<div id='event_add_time'></div>
					<div id='event_times_box'></div>
					<div id='event_send'></div>
				</form>
			</div>
			<div id="tabs-person">
				<form id='person_form'>
					<div id='person_honorificId'></div>
					<div id='person_firstname'></div>
					<div id='person_surname'></div>
					<div id='person_othername'></div>
					<div id='person_ordinal'></div>
					<div id='person_remark'></div>
					<div id='person_send'></div>
				</form>
			</div>
			<div id="tabs-work">
				<form id='work_form'>
					<div id='work_name'></div>
					<div id='work_typeid'></div>
					<div id='work_languageid'></div>
					<div id='work_send'></div>
				</form>
			</div>
			<div id="tabs-workexternal">
				<form id='workexternal_form'>
					<div id='workexternal_workid'></div>
					<div id='workexternal_externalid'></div>
					<div id='workexternal_externalcode'></div>
					<div id='workexternal_send'></div>
				</form>
			</div>
			<div id="tabs-personexternal">
				<form id='personexternal_form'>
					<div id='personexternal_personid'></div>
					<div id='personexternal_externalid'></div>
					<div id='personexternal_externalcode'></div>
					<div id='personexternal_send'></div>
				</form>
			</div>
			<div id="tabs-personwork">
				<form id='personwork_form'>
					<div id='personwork_workid'></div>
					<div id='personwork_personid'></div>
					<div id='personwork_typeid'></div>
					<div id='personwork_send'></div>
				</form>
			</div>
			<div id="tabs-orgwork">
				<form id='orgwork_form'>
					<div id='orgwork_workid'></div>
					<div id='orgwork_organizationid'></div>
					<div id='orgwork_typeid'></div>
					<div id='orgwork_send'></div>
				</form>
			</div>
			<div id="tabs-workview">
				<form id='workview_form'>
					<div id='workview_personid'></div>
					<div id='workview_workid'></div>
					<div id='workview_date'></div>
					<div id='workview_locationid'></div>
					<div id='workview_deviceid'></div>
					<div id='workview_langid'></div>
					<div id='workview_ratingid'></div>
					<div id='workview_review'></div>
					<div id='workview_send'></div>
				</form>
			</div>
			<div id="tabs-done">
				<form id='done_form'>
					<!--
					<div id='done_start'></div>
					-->
					<div id='done_end'></div>
					<!--
					<div id='done_personid'></div>
					-->
					<div id='done_locationid'></div>
					<div id='done_activityid'></div>
					<div id='done_remark'></div>
					<div id='done_send'></div>
				</form>
			</div>
			<div id="tabs-workviewreview">
				<form id='workviewreview_form'>
					<div id='workviewreview_name'></div>
					<div id='workviewreview_typeid'></div>
					<div id='workviewreview_externalid'></div>
					<div id='workviewreview_externalcode'></div>
					<div id='workviewreview_end'></div>
					<div id='workviewreview_personid'></div>
					<div id='workviewreview_locationid'></div>
					<div id='workviewreview_deviceid'></div>
					<div id='workviewreview_remark'></div>
					<div id='workviewreview_ratingid'></div>
					<div id='workviewreview_review'></div>
					<div id='workviewreview_send'></div>
				</form>
			</div>
		</div>
		<legend>Log</legend>
		<div id='log' style='height:200px; width:100%; overflow:auto;'></div>
	</body>
</html>
