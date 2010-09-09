jQuery(document).ready(function() {
	var init={
		'name':'Name',
		'initMsg': 'put the name of the movie here',
	};
	jQuery('#name').jvalidfield(init);

	var init={
		'name':'Imdbid',
		'initMsg': 'put the imdbid here',
	};
	jQuery('#imdbid').jvalidfield(init);
	
	var init={
		'name': 'Rating',
		'initMsg': 'put the rating (1-10) here',
		'table':'TbRating',
	};
	jQuery('#rating').jsqlfield(init);

	var init={
		'name': 'Location',
		'initMsg': 'put the location where you saw the movie',
		'table':'TbLcNamed',
	};
	jQuery('#location').jsqlfield(init);

	var init={
		'name':'Review',
		'initMsg': 'Put your review here',
		'type': 'textarea',
	};
	jQuery('#review').jvalidfield(init);
});
