<script type='text/javascript'>

function formValidator(){
	// Make quick references to our fields
	var firstname = document.getElementById('firstname');
	var addr = document.getElementById('addr');
	var zip = document.getElementById('zip');
	var state = document.getElementById('state');
	var username = document.getElementById('username');
	var email = document.getElementById('email');
	
	// Check each input in the order that it appears in the form!
	if(isAlphabet(firstname, "Please enter only letters for your name")){
		if(isAlphanumeric(addr, "Numbers and Letters Only for Address")){
			if(isNumeric(zip, "Please enter a valid zip code")){
				if(madeSelection(state, "Please Choose a State")){
					if(lengthRestriction(username, 6, 8)){
						if(emailValidator(email, "Please enter a valid email address")){
							return true;
						}
					}
				}
			}
		}
	}
	
	
	return false;
	
}

function notEmpty(elem, helperMsg){
	if(elem.value.length == 0){
		alert(helperMsg);
		elem.focus(); // set the focus to this input
		return false;
	}
	return true;
}

function isNumeric(elem, helperMsg){
	var numericExpression = /^[0-9]+$/;
	if(elem.value.match(numericExpression)){
		return true;
	}else{
		alert(helperMsg);
		elem.focus();
		return false;
	}
}

function isAlphabet(elem, helperMsg){
	var alphaExp = /^[a-zA-Z]+$/;
	if(elem.value.match(alphaExp)){
		return true;
	}else{
		alert(helperMsg);
		elem.focus();
		return false;
	}
}

function isAlphanumeric(elem, helperMsg){
	var alphaExp = /^[0-9a-zA-Z]+$/;
	if(elem.value.match(alphaExp)){
		return true;
	}else{
		alert(helperMsg);
		elem.focus();
		return false;
	}
}

function lengthRestriction(elem, min, max){
	var uInput = elem.value;
	if(uInput.length >= min && uInput.length <= max){
		return true;
	}else{
		alert("Please enter between " +min+ " and " +max+ " characters");
		elem.focus();
		return false;
	}
}

function madeSelection(elem, helperMsg){
	if(elem.value == "Please Choose"){
		alert(helperMsg);
		elem.focus();
		return false;
	}else{
		return true;
	}
}

function emailValidator(elem, helperMsg){
	var emailExp = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
	if(elem.value.match(emailExp)){
		return true;
	}else{
		alert(helperMsg);
		elem.focus();
		return false;
	}
}
// use <form onsubmit='return formValidator()' >
</script>
