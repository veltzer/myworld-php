jQuery(document).ready(function() {
	jQuery('#rating').reload('TbRating');
	jQuery('#location').reload('TbLcNamed');
	jQuery('#reload_rating').click(function() { jQuery('#rating').reload('TbRating'); } );
	jQuery('#reload_location').click(function() { jQuery('#location').reload('TbLcNamed'); } );
});
