// this is the accordion support function
jQuery(document).ready(function() {
	jQuery("div.myacc_header").click(function(){
		jQuery(this).next("div").slideToggle();
		jQuery(this).toggleClass("active");
	});
});
