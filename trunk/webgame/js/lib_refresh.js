function refreshMinichat(){
  parent.mini_chat.location="mini_chat.php";
}


function refreshQuickstats(quickView){
	if (!quickView){
		parent.quickstats.location="quickstats.php";
	} else {
		parent.quickstats.location='quickstats.php?command='+quickView;
	}
}

/* Need to parse the 'this' php file/page so that refreshing to login auto-passes the appropriate page after */
function refreshToLogin(failed){
	if (top.location!= self.location) {
		top.location = 'index.php'
	}
	if(!failed){
		parent.location.href=parent.location.href;
	} else {
		parent.main.location="index.php?action=login";
	}
	return false;
}
