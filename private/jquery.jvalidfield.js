/*
 */
(function($) {
	$.fn.extend({
		jvalidfield: function(options) {
			var defaults = {
				name: 'No name',
				initMsg: 'put some text here',
			};
			var o=$.extend(defaults, options);
			return this.each(function() {
				var html='';
				html+='<label>'+o.name+'</label>';
//html+='<input class="inputfield" type="text" name="'+o.name+'"/>';
				html+='<input name="'+o.name+'" value="'+o.initMsg+'"/>';
				$(this).html(html);
			});
		}
	});
})(jQuery);
