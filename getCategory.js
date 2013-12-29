function loadCategories(revenue_stream,type,origin) {
	xmlhttp=new XMLHttpRequest();
	xmlhttp.onreadystatechange=function()
  	{
  		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
    		document.getElementById("revenueStreamArea").innerHTML=xmlhttp.responseText;
    	}
  	}
	
	xmlhttp.open("GET","category.php?preset=true&revenue_stream="+revenue_stream,true);
	xmlhttp.send();
	
	xmlhttp2=new XMLHttpRequest();
	xmlhttp2.onreadystatechange=function()
  	{
  		if (xmlhttp2.readyState==4 && xmlhttp2.status==200) {
    		document.getElementById("typeArea").innerHTML=xmlhttp2.responseText;
    	}
  	}
	
	xmlhttp2.open("GET","category.php?preset=true&revenue_stream="+revenue_stream+"&type="+type,true);
	xmlhttp2.send();
	
	xmlhttp3=new XMLHttpRequest();
	xmlhttp3.onreadystatechange=function()
  	{
  		if (xmlhttp3.readyState==4 && xmlhttp3.status==200) {
    		document.getElementById("originArea").innerHTML=xmlhttp3.responseText;
    	}
  	}
	
	xmlhttp3.open("GET","category.php?preset=true&revenue_stream="+revenue_stream+"&type="+type+"&origin="+origin,true);
	xmlhttp3.send();
}

function getRevenueStream() {	
	document.getElementById("typeArea").innerHTML="";
	document.getElementById("originArea").innerHTML="";
	xmlhttp=new XMLHttpRequest();
	xmlhttp.onreadystatechange=function()
  	{
  		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
    		document.getElementById("revenueStreamArea").innerHTML=xmlhttp.responseText;
    	}
  	}
	
	xmlhttp.open("GET","category.php",true);
	xmlhttp.send();
	
}

function getType(revenue_stream) {	
	document.getElementById("originArea").innerHTML="";
	if (revenue_stream=="") {
  		document.getElementById("typeArea").innerHTML="";
  		return;
  	}
	
	xmlhttp=new XMLHttpRequest();
	xmlhttp.onreadystatechange=function()
  	{
  		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
    		document.getElementById("typeArea").innerHTML=xmlhttp.responseText;
    	}
  	}
	
	xmlhttp.open("GET","category.php?revenue_stream="+revenue_stream,true);
	xmlhttp.send();
}

function getOrigin(revenue_stream,type) {
	if (type=="") {
  		document.getElementById("originArea").innerHTML="";
  		return;
  	}
	
	xmlhttp=new XMLHttpRequest();
	xmlhttp.onreadystatechange=function()
  	{
  		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
    		document.getElementById("originArea").innerHTML=xmlhttp.responseText;
    	}
  	}
	
	xmlhttp.open("GET","category.php?revenue_stream="+revenue_stream+"&type="+type,true);
	xmlhttp.send();
}

function saveSelected(donor_id,username) {
	xmlhttp = new XMLHttpRequest();
	if (document.getElementById("selected_"+donor_id).checked) {
		xmlhttp.open("POST","updatedselected.php?donor_id="+donor_id+"&username="+username);	
	}
	else {
		xmlhttp.open("POST","updatedselected.php?donor_id="+donor_id+"&username="+username+"&delete=true");
	}
	xmlhttp.send();
}

function clearSelected(username) {
	xmlhttp = new XMLHttpRequest();
	xmlhttp.open("POST","clearSelected.php?username="+username);
	xmlhttp.send();
	return true;
}

function reloadPage(getString) {
	window.location = "index.php?"+getString;
}

function clearAndReload(username,getString) {
	clearSelected(username);
	reloadPage(getString);
}

function csvReport(report_id) {
	var sqltext = document.getElementById("sqltext").value;
	var from_date = document.getElementById("from_date").value;
	if (!from_date) {
		from_date = "current_date";
		sqltext = sqltext.replace(/@FROM_DATE@/g,from_date);
	}
	else {
		var to_date = document.getElementById("to_date").value;
		sqltext = sqltext.replace(/@FROM_DATE@/g,"'"+from_date+"'");
		sqltext = sqltext.replace(/@TO_DATE@/g,"'"+to_date+"'");
	}
	document.getElementById("csvButton").value = "Exporting...";
	document.getElementById("reportGrid").innerHTML = "<p style=\"color:red;\">Exporting...</p>";
	xmlhttp = new XMLHttpRequest();
	var params = "sql="+sqltext+"&report_id="+report_id;
	xmlhttp.onreadystatechange=function()
  	{
  		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
    		//window.location=xmlhttp.responseText;
  			document.getElementById("reportGrid").innerHTML = xmlhttp.responseText;
    	}
  	}	
	xmlhttp.open("POST","csvReport.php",false);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	
	xmlhttp.send(params);
	document.getElementById("csvButton").value = "Export to CSV";
}

function runReport(report_id) 
{
	var sqltext = document.getElementById("sqltext").value;
	var from_date = document.getElementById("from_date").value;
	if (!from_date) {
		from_date = "current_date";
		sqltext = sqltext.replace(/@FROM_DATE@/g,from_date);
	}
	else {
		var to_date = document.getElementById("to_date").value;
		sqltext = sqltext.replace(/@FROM_DATE@/g,"'"+from_date+"'");
		sqltext = sqltext.replace(/@TO_DATE@/g,"'"+to_date+"'");
	}
	document.getElementById("runButton").value = "Running...";
	document.getElementById("reportGrid").innerHTML = "<p style=\"color:red;\">Running...</p>";
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange=function()
  	{
  		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
    		document.getElementById("reportGrid").innerHTML=xmlhttp.responseText;
    	}
  	}	
	
	var params = "sql="+sqltext+"&report_id="+report_id;
	xmlhttp.open("POST","runReport.php",false);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	
	xmlhttp.send(params);
	document.getElementById("runButton").value = "Run";
}

function loadReport(reportid, reportname, reportdesc, reportpath) 
{
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange=function()
  	{
  		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
    		document.getElementById("sqltext").value=xmlhttp.responseText;
    		document.getElementById("reportCaption").innerHTML=reportname;
    		//document.getElementById("reportCaption2").innerHTML=reportdesc;
    		document.getElementById("runButton").setAttribute("onClick","runReport("+reportid+")");
    		document.getElementById("pdfButton").setAttribute("onClick","pdfReport("+reportid+")");
    		document.getElementById("csvButton").setAttribute("onClick","csvReport("+reportid+")");
    		document.getElementById("reportGrid").innerHTML = "";
    		document.getElementById("report_id").setAttribute("value",reportid);
    	}
  	}	
	xmlhttp.open("GET","load_report.php?report_id="+reportid+"&path="+reportpath,false);
	xmlhttp.send();
}

function toggleDiv(divid)
{
	if(document.getElementById(divid).style.display == 'none'){
		document.getElementById(divid).style.display = 'block';
	}
	else {
		document.getElementById(divid).style.display = 'none';
	}
}

function updateYear() {
	document.getElementById("year").value = document.getElementById("date").value.substring(0,4);
}