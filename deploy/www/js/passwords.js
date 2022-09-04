/* Simply check that submitted passwords match. */
/* jquery vars are globally available */

// eslint-disable-next-line no-var
var presence = window.presence || {};
presence.passwords = true;

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
    return true;
  });
});
