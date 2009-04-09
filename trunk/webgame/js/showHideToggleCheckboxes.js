<script type="text/javascript">
/**
 * Set up the initial events, hide the necessary fields, etc.
**/
ZD.onReady(function() {

	// *** Get checkbox and div objects.
	var personal_info_check_box = document.getElementById('zd-field-same_personal_info_as_inquirer');

	// *** Get second checkbox and div objects.
	var contact_info_check_box = document.getElementById('zd-field-same_address_as_above');
	
	// *** If the page loads with the checkboxes checked, set display for the appropriate sections to display:none;
	if (personal_info_check_box.checked==true){
		togglePersonalSecs();
	}

	if (contact_info_check_box.checked==true){
		toggleAddressSecs();
	}


	ZD.on(personal_info_check_box, 'click', togglePersonalSecs);
	ZD.on(contact_info_check_box, 'click', toggleAddressSecs);

});

/* When this checkbox gets checked or unchecked, toggle displaying the appropriate section ids. */
function togglePersonalSecs() {
	// *** Refactor these into an array at some point.
	var name_sec = document.getElementById('zd-field-name-tr');
	var inquirer_relationship_sec = document.getElementById('zd-field-inquirer_relationship-tr');
	if (name_sec.style.display == 'none') {
		name_sec.style.display = '';
		inquirer_relationship_sec.style.display = '';
	} else {
		name_sec.style.display = 'none';
		inquirer_relationship_sec.style.display = 'none';
	}
}


/* When this checkbox gets checked or unchecked, toggle displaying the appropriate section ids. */
function toggleAddressSecs() {
	// *** Refactor these into an array at some point.	
	var current_address_sec = document.getElementById('zd-field-current_address-tr');
	var current_city_sec = document.getElementById('zd-field-current_city-tr');
	var current_state_sec = document.getElementById('zd-field-current_state-tr');
	var current_zip_sec = document.getElementById('zd-field-current_zip-tr');
	var current_phone_number_sec = document.getElementById('zd-field-current_phone_number-tr');
	
	if (current_address_sec.style.display == 'none') {
		current_address_sec.style.display = '';
		current_city_sec.style.display = '';
		current_state_sec.style.display = '';
		current_zip_sec.style.display = '';
		current_phone_number_sec.style.display = '';
	} else {
		current_address_sec.style.display = 'none';
		current_city_sec.style.display = 'none';
		current_state_sec.style.display = 'none';
		current_zip_sec.style.display = 'none';
		current_phone_number_sec.style.display = 'none';
	}
}

</script>
