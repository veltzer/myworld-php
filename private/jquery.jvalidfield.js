/*
 */
jQuery(document).ready(function() {
	jQuery.fn.extend({
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
			var o=jQuery.extend(defaults, options);
			return this.each(function() {
				if(o.addLabel==true) {
					jQuery('<label>',{
					}).html(o.name).appendTo(this);
				}
				var attrs={
					val: o.initMsg,
					focusin: function() {
						jQuery(this).addClass(o.activeClass);
						if(jQuery(this).data('initState')) {
							// reset the value
							jQuery(this).val('');
						}
					},
					focusout: function() {
						jQuery(this).removeClass(o.activeClass);
						if(jQuery(this).val()=='') {
							if(o.mustInput) {
								jQuery(this).data('initState',true);
								jQuery(this).val(o.initMsg);
							}
						} else {
							if(jQuery(this).data('initState')) {
								jQuery(this).data('initState',false);
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
				var w_input=jQuery('<'+o.type+'>',attrs);
				w_input.data('initState',true);
				w_input.appendTo(this);
			});
		}
	});
});
