jQuery(document).ready(function() {
	//jQuery(".accordion2 h3").eq(2).addClass("active");
	//jQuery(".accordion2 p").eq(2).show();
	jQuery(".accordion2 h3").click(function(){
		jQuery(this).next("p").slideToggle("fast")
		//siblings("p:visible").slideUp("slow");
		jQuery(this).toggleClass("active");
		jQuery(this).siblings("h3").removeClass("active");
	});
});
