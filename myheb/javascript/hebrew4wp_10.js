/**
 * hebrew4wp: incredibly simple Hebrew and Arabic support for bilingual WordPress installations
 *  
 * Copyright (c) 2008 Nadav Elyada <http://www.nadav.org/>
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * Takes two function pointers f1 and f2, and returns a single function executing both f1 and f2.
 * If either function is undefined, execution proceeds with the next defined function, if any.
 * Inspired by Robert Hahn <http://blog.roberthahn.ca/>
 */
function hebrew4wp_chain(f1, f2) {
    return function() {
        if(f1) {
            f1();
        }
        
        if(f2) {
            f2();
        }
    }
}

/**
 * Takes the Unicode code of any character, and returns true if this character is an Hebrew character
 * according to the Unicode standard; false otherwise.
 */
function hebrew4wp_is_hebrew_char(c) {
	//The range (0x05D0 thru 0x05EA) should be enough (?)
	if((c >= 0x0590 && c <= 0x05FF) || (c >= 0xFB1D && c <= 0xFB40)) {
		return true;
	} else {
		return false;
	}
}

/**
 * Takes the Unicode code of any character, and returns true if this character is an Arabic character
 * according to the Unicode standard; false otherwise.
 */
function hebrew4wp_is_arabic_char(c) {
	if((c >= 0x0600 && c <= 0x06FF) || (c >= 0x0750 && c <= 0x077F) ||
		(c >= 0xFB50 && c <= 0xFDFF) || (c >= 0xFE70 && c <= 0xFEFF)) {
		return true;
	} else {
		return false;
	}
}

/**
 * Takes a string, and returns true if this string contains RTL text; false otherwise.
 * This implementation treats any string containing one or more RTL characters as RTL text.
 * This implementation considers all Hebrew and Arabic characters (as defined by the Unicode standard),
 * and no other characters, to be RTL characters.
 */
function hebrew4wp_is_rtl_text(s) {
	for(var i = 0; i < s.length; i++) {
		var c = s.charCodeAt(i);
		
		if(hebrew4wp_is_hebrew_char(c) || hebrew4wp_is_arabic_char(c)) {
			return true;
		}
	}
	
	return false;
}

/**
 * The hebrew4wp window.onload handler.
 */
function hebrew4wp_onload() {
	//Get all the elements in the document and walk over them
	var elements = document.getElementsByTagName("*");
	for(var i = 0; i < elements.length; i++) {
		var el = elements[i];

		//If this particular element bears the CSS class name "entry", or if this is an H2 element
		if((el.className != null && el.className.indexOf("post-title") != -1) || el.tagName.toLowerCase() == "a") { // from h2
			var entryHTML = el.innerHTML;

			if(hebrew4wp_is_rtl_text(entryHTML)) {
				//If any RTL characters are found, set the element's DIR attribute to "right-to-left".	
				el.dir = "rtl";
				//el.childNodes[0].style.textAlign="right";
			}
		}
	}
}

/**
 * Chains the hebrew4wp window.onload handler to any existing window.onload handlers.
 */
window.onload = hebrew4wp_chain(window.onload, hebrew4wp_onload);
