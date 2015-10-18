$(document).ready(function () {
	$('#delete-messages a').click(function() {
		return confirm('Delete all messages?'); // *** boolean return value ***
	}).hover(
		function () {
			$(this).css({'background-color':'red'});
		},
		function () {
			$(this).css({'background-color':'inherit'});
		}
	);
});
