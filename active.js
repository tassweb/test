function refreshIndex() {	
	xmlhttp=new XMLHttpRequest();
	xmlhttp.onreadystatechange=function()
  	{
  		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
    		document.getElementById("revenueStreamArea").innerHTML=xmlhttp.responseText;
    	}
  	}
	
	xmlhttp.open("GET","index.php?active=true",true);
	xmlhttp.send();
	
}