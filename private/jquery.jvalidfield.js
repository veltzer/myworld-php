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
				activeClass: 'focus',
				mustInput: true,
				rows: 10,
			};
			var o=$.extend(defaults, options);
			return this.each(function() {
				if(o.addLabel==true) {
					$('<label>',{
					}).html(o.name).appendTo(this);
				}
				var attrs={
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
						} else {
							if($(this).data('initState')) {
								$(this).data('initState',false);
							}
						}
					},
				}
				if(o.type=='input') {
					attrs.type=o.inputtype;
				}
				if(o.type=='textarea') {
					attrs.rows=o.rows;
				}
				var w_input=$('<'+o.type+'>',attrs);
				w_input.data('initState',true);
				w_input.appendTo(this);
			});
		}
	});
})(jQuery);
