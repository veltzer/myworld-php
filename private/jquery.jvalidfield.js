/*
 */
(function($) {
	$.fn.extend({
		jvalidfield: function(options) {
			var defaults = {
				addLabel: true,
				type: 'input',
				inputtype: 'text',
				name: 'No name',
				initMsg: 'put some text here',
				activeClass: 'active',
				mustInput: true,
			};
			var o=$.extend(defaults, options);
			return this.each(function() {
				if(o.addLabel==true) {
					$('<label>',{
					}).html(o.name).appendTo(this);
				}
				var w_input=$('<'+o.type+'>',{
					type: o.inputtype,
					val: o.initMsg,
					focusin: function() {
						$(this).addClass(o.activeClass);
						if($(this).data('initState')) {
							// reset the value
							$(this).val('');
						}
					},
					focusout: function() {
						$(this).removeClass(o.activeClass);
						if($(this).val()=='') {
							if(o.mustInput) {
								$(this).data('initState',true);
								$(this).val(o.initMsg);
							}
						}		
						if($(this).data('initState')) {
							if($(this).val()!='') {
								$(this).data('initState',false);
							} else {
								$(this).data('initState',true);
								$(this).val(o.initMsg);
							}
						}
					}
				});
				w_input.data('initState',true);
				w_input.appendTo(this);
			});
		}
	});
})(jQuery);
