function hide(id) 
{
	if(document.getElementById(id).style.display=="none")
	{
		document.getElementById(id).style.display="";
	}
	else
	{ 
		document.getElementById(id).style.display="none"; 
	}
}
function confirmDelete(v, url) 
{ 
	if (confirm('Уверены, что хотите удалить ' + v + '?')) 
	{ 
		parent.location=url; 
	} 
}
function ShowDiv(selectedOption) {
	document.getElementById('inventory').style.display = "none";
	
	if(selectedOption == 'inventory') {document.getElementById('inventory').style.display = "";}
	
}
function tooltip_show(id,mess)
{
	var tid = document.getElementById(id);
	tid.innerHTML = mess;
	tid.style.visibility="visible";
}
function tooltip_hide(id)
{
	var tid = document.getElementById(id);
	tid.style.visibility="hidden";
}
function hideForm(){
	document.getElementById("form_item_edit").style.visibility = "hidden";
}
function check_uncheck_all(frm) {
    for (var i=0;i<frm.elements.length;i++) {
        var elmnt = frm.elements[i];
        if (elmnt.type=='checkbox') {
            if(frm.master_box.checked == true){ elmnt.checked=false; }
            else{ elmnt.checked=true; }
        }
    }
    if(frm.master_box.checked == true){ frm.master_box.checked = false; }
    else{ frm.master_box.checked = true; }
}