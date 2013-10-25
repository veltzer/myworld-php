/*
 * General javascript utitlities for the myworld plugin
 */
function get_last_elem() {
	// start at the root element
	var target = document.documentElement;
	// find last HTMLElement child node
	while (target.childNodes.length && target.lastChild.nodeType == 1) {
		target = target.lastChild;
	}
	// target is now the script element
	// target.parentNode is the parent of the script element
	return target;
}
function get_my_location() {
	return get_last_elem().parentNode;
}
function add_content_at_loc(loc, content) {
	//var target=get_last_elem()
	//target.parentNode.insertBefore(button, target)
	loc.appendChild(content);
}
function create_div_at_location(loc) {
	var mydiv=document.createElement('div');
	add_content_at_loc(loc, mydiv);
	return mydiv;
}
function create_button_at_location(loc) {
	var mybutton=document.createElement('button');
	var mytext=document.createTextNode('this is some content');
	mybutton.appendChild(mytext);
	add_content_at_loc(loc, mybutton);
	return mybutton;
}
function fake_use(x) {
	if(x) {
		window.fakevar='fakevalue';
	}
}
function fake_do() {
	window.fakevar='fakevalue';
}
