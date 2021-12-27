/* Show current character limits on profile text */
/* jshint browser: true, white: true, plusplus: true */
/* global $ */
function charSuggest(textareaid, limit, infoid) {
  $(`#${textareaid}`).keyup(() => {
    const textlength = $(`#${textareaid}`).val().length;
    const newText = `${limit} character limit, ${textlength} characters used.`;
    $(`#${infoid}`).text(newText);
  });
}
$(() => {
  charSuggest('player-profile-area', 500, 'characters-left');
});
