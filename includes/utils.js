function changetab(menunum) { // i must have been high when i wrote this because it took me a minute to figure out what is going on
	var currTabElem = document.getElementById(menunum); 
	currTabElem.setAttribute("class", "current");
	currTabElem.setAttribute("className", "current");

	switch(menunum) {
		case "menu0":
			var otherTabElem0 = document.getElementById("menu1");
			var otherTabElem1 = document.getElementById("menu2");
			var otherTabElem2 = document.getElementById("menu3");
			var otherTabElem3 = document.getElementById("menu4");
			var otherTabElem4 = document.getElementById("menu5");
			var toshow0	  = document.getElementById("toscour");
			var tohide0	  = document.getElementById("totest");
			var tohide1	  = document.getElementById("toadmin");	
			var tohide2	  = document.getElementById("tohelp");		
			var tohide3	  = document.getElementById("todatabase");	
			var tohide4	  = document.getElementById("toban");
			break;
		case "menu1":
			var otherTabElem0 = document.getElementById("menu0");
			var otherTabElem1 = document.getElementById("menu2");
			var otherTabElem2 = document.getElementById("menu3");
			var otherTabElem3 = document.getElementById("menu4");
			var otherTabElem4 = document.getElementById("menu5");
			var toshow0	  = document.getElementById("totest");
			var tohide0	  = document.getElementById("toscour");
			var tohide1	  = document.getElementById("toadmin");
			var tohide2	  = document.getElementById("tohelp");
			var tohide3	  = document.getElementById("todatabase");
			var tohide4	  = document.getElementById("toban");
			break;
		case "menu2":
			var otherTabElem0 = document.getElementById("menu0");
			var otherTabElem1 = document.getElementById("menu1");
			var otherTabElem2 = document.getElementById("menu3");
			var otherTabElem3 = document.getElementById("menu4");
			var otherTabElem4 = document.getElementById("menu5");
			var toshow0	  = document.getElementById("toadmin");
			var tohide0	  = document.getElementById("toscour");
			var tohide1	  = document.getElementById("totest");
			var tohide2	  = document.getElementById("tohelp");
			var tohide3	  = document.getElementById("todatabase");
			var tohide4	  = document.getElementById("toban");
			break;
		case "menu3":
			var otherTabElem0 = document.getElementById("menu0");
			var otherTabElem1 = document.getElementById("menu1");
			var otherTabElem2 = document.getElementById("menu2");
			var otherTabElem3 = document.getElementById("menu4");
			var otherTabElem4 = document.getElementById("menu5");
			var toshow0	  = document.getElementById("todatabase");
			var tohide0	  = document.getElementById("toscour");
			var tohide1	  = document.getElementById("toadmin");
			var tohide2	  = document.getElementById("totest");
			var tohide3	  = document.getElementById("tohelp");
			var tohide4	  = document.getElementById("toban");
			break;
		case "menu4":
			var otherTabElem0 = document.getElementById("menu0");
			var otherTabElem1 = document.getElementById("menu1");
			var otherTabElem2 = document.getElementById("menu2");
			var otherTabElem3 = document.getElementById("menu3");
			var otherTabElem4 = document.getElementById("menu5");
			var toshow0	  = document.getElementById("toban");
			var tohide0	  = document.getElementById("toscour");
			var tohide1	  = document.getElementById("toadmin");
			var tohide2	  = document.getElementById("totest");
			var tohide3	  = document.getElementById("todatabase");
			var tohide4	  = document.getElementById("tohelp");

			break;
		case "menu5":
			var otherTabElem0 = document.getElementById("menu0");
			var otherTabElem1 = document.getElementById("menu1");
			var otherTabElem2 = document.getElementById("menu2");
			var otherTabElem3 = document.getElementById("menu3");
			var otherTabElem4 = document.getElementById("menu4");
			var toshow0	  = document.getElementById("tohelp");
			var tohide0	  = document.getElementById("toscour");
			var tohide1	  = document.getElementById("toadmin");
			var tohide2	  = document.getElementById("totest");
			var tohide3	  = document.getElementById("todatabase");
			var tohide4	  = document.getElementById("toban");

			break;
		
  	}
	otherTabElem0.setAttribute("class", " ");
	otherTabElem0.setAttribute("className", " ");
	otherTabElem1.setAttribute("class", " ");
	otherTabElem1.setAttribute("className", " ");
	otherTabElem2.setAttribute("class", " ");
	otherTabElem2.setAttribute("className", " ");
	otherTabElem3.setAttribute("class", " ");
	otherTabElem3.setAttribute("className", " ");
	otherTabElem4.setAttribute("class", " ");
	otherTabElem4.setAttribute("className", " ");
	toshow0.setAttribute("class", "content");
	toshow0.setAttribute("className", "content");
	toshow0.style.width="400px";
	tohide0.setAttribute("class", "tehhidden");
	tohide0.setAttribute("className", "tehhidden");
	tohide1.setAttribute("class", "tehhidden");
	tohide1.setAttribute("className", "tehhidden");
	tohide2.setAttribute("class", "tehhidden");
	tohide2.setAttribute("className", "tehhidden");
	tohide3.setAttribute("class", "tehhidden");
	tohide3.setAttribute("className", "tehhidden");
	tohide4.setAttribute("class", "tehhidden");
	tohide4.setAttribute("className", "tehhidden");
	
	var anonhide = document.getElementById("anonhiddenfun");
	anonhide.innerHTML = '';
	anonhide.setAttribute("class", "tehhidden");
	var warnid = document.getElementById("warnid");
	warnid.innerHTML = '';
	warnid.setAttribute("class", "tehhidden");
}

function highlight(field) {
	field.focus();
	field.select();
}

function masterClick( num ) {
	if(num == 0) {
		for (i=0;i<document.proxybotstart.length;i++) {
			if (document.proxybotstart[i].type=="checkbox") {
				document.proxybotstart[i].checked = true;
			}
		}
		document.getElementById('mclick').innerHTML = '<a href="#" name="checkall" onClick="masterClick(1)">select: none</a>';
	}
	else {
		for (i=0;i<document.proxybotstart.length;i++) {
			if (document.proxybotstart[i].type=="checkbox") {
				document.proxybotstart[i].checked = false;
			}
		}
		document.getElementById('mclick').innerHTML = '<a href="#" name="checkall" onClick="masterClick(0)">select: all</a>';
	}   
}

function formfun(num) {
	if(num == 3){	// anonprox, pop down the other form options
		var anonhide = document.getElementById("anonhiddenfun");
		anonhide.innerHTML = '<table><tr><td>- internal anon check:</td><td><input type="radio" name="anonchecktype" value="internal" checked /></td></tr>'+
							 '<tr><td>- external anon check:</td><td><input type="radio" name="anonchecktype" value="external" /></td></tr></table>';
		anonhide.setAttribute("class", "anonfun");
		anonhide.setAttribute("className", "anonfun");
	} else {	// anything but anonprox, hide
		var anonhide = document.getElementById("anonhiddenfun");
		anonhide.innerHTML = '';
		anonhide.setAttribute("class", "tehhidden");
	}
}
