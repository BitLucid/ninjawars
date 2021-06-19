/* Simply check that submitted passwords match. */
/*jshint browser: true, white: true, plusplus: true*/
/*global $ */
$(function () {
    $('form[name=new_password_form]').submit(function () {
        var newPassword = $('input[name=new_password]').val();
        var passwordConfirmation = $('input[name=password_confirmation]').val();
        if (!passwordConfirmation || newPassword !== passwordConfirmation) {
            $('<div class="error">Passwords do not match</div>').appendTo(
                $('input[name=password_confirmation]').parent()
            );
            return false;
        }
    });
});
