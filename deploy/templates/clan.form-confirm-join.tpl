<form action="/clan/accept?joiner={$joiner->id()|escape:'url'}" method="post">
  <div>
    <input id="agree" type="hidden" name="agree" value="1">
    <input name="confirmation" type="hidden" value="{$confirmation|escape}">
    <input type="submit" value="Accept request from {$joiner->name()|escape}">
  </div>
</form>
