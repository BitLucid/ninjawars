$(function () {
	'use strict';
	function leaveClan() {
		if (confirm("Do you really want to exit the clan?")) {
			window.location = "/clan/leave";
		}

		return false;
	}
	
	$("#leave-clan").click(function() {
		leaveClan();
		return false;
	});
});
