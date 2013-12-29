var formblock;
var forminputs;

function prepare() {
	formblock= document.getElementById("headerlist");
	forminputs = formblock.getElementsByTagName("input");
}

function select_all(name, value) {
	for (i = 1; i < forminputs.length; i++) {
		// regex here to check name attribute
		var regex = new RegExp(name, "i");
		if (regex.test(forminputs[i].getAttribute('name'))) {
			if (value == '1') {
			forminputs[i].checked = true;
			} else {
			forminputs[i].checked = false;
			}
		}
	}
}

if (window.addEventListener) {
window.addEventListener("load", prepare, false);
} else if (window.attachEvent) {
window.attachEvent("onload", prepare)
} else if (document.getElementById) {
window.onload = prepare;
}

function SubmitForm(form, params)
{
	if (params)
		form.action = "index.php?" + params;
	else
		form.action = "index.php";
	form.submit();
	return true;
}

function showLayer(element) {
	obj = document.getElementById(element);
	mybutton = document.getElementById("options");
	mybutton.className == "btn" ? mybutton.className = "btnclicked" : mybutton.className = "btn";
	obj.style.display == "block" ? obj.style.display = "none" : obj.style.display = "block";
}