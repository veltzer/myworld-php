jQuery(document).ready(function() {
	var init={ 'name':'Name' };
	jQuery('#name').jvalidfield(init);
	var init={ 'name':'Imdbid' };
	jQuery('#imdbid').jvalidfield(init);

	jQuery('#rating').reload('TbRating');
	jQuery('#location').reload('TbLcNamed');
	jQuery('#reload_rating').click(function() { jQuery('#rating').reload('TbRating'); } );
	jQuery('#reload_location').click(function() { jQuery('#location').reload('TbLcNamed'); } );
});
