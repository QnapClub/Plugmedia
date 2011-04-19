// JavaScript Document
function checkAllFieldsandGetSelect(ref, elem, master, delete_btn)
{
	var chkAll = document.getElementById(master); 
	var checks = document.getElementsByName(elem);
	var removeBtnDelete = document.getElementById('btn_delete_r');
	var delete_btn = document.getElementById(delete_btn);
	var boxLength = checks.length;
	var allChecked = false;
	var totalChecked = 0;
		if ( ref == 1 )
		{
			if ( chkAll.checked == true )
			{
				for ( i=0; i < boxLength; i++ )
				{
					if (checks[i].disabled != true)
						checks[i].checked = true;
				}
				
			}
			else
			{
				for ( i=0; i < boxLength; i++ )
				checks[i].checked = false;
			}
		}
		else
		{
		
			for ( i=0; i < boxLength; i++ )
			{
				if ( checks[i].checked == true  || checks[i].disabled == true )
				{
					allChecked = true;
					continue;
				}
				else
				{
					allChecked = false;
					break;
				}
			}
			if ( allChecked == true )
				chkAll.checked = true;
			else
				chkAll.checked = false;
		}
		for ( j=0; j < boxLength; j++ )
		{
			if ( checks[j].checked == true )
			totalChecked++;
		}
		
		if (totalChecked == 0)
		{
			//removeBtnDelete.style.visibility = 'hidden';
			delete_btn.innerHTML = pre_string;
		}
		else
		{
			removeBtnDelete.style.visibility = 'visible';
			
			if (totalChecked == 1)
				string_element = plural_string;	
			else
				string_element = singular_string;	
			
			delete_btn.innerHTML = pre_string+' '+totalChecked+' '+string_element;
			
		}
}