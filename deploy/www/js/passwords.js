/* Simply check that submitted passwords match. */
/* jshint browser: true, white: true, plusplus: true */
/* global $ */
$(() => {
  $('form[name=new_password_form]').submit(() => {
    const newPassword = $('input[name=new_password]').val();
    const passwordConfirmation = $('input[name=password_confirmation]').val();
    if (!passwordConfirmation || newPassword !== passwordConfirmation) {
      $('<div class="error">Passwords do not match</div>').appendTo(
        $('input[name=password_confirmation]').parent(),
      );
      return false;
    }
  });
});
