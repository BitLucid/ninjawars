{if isset($error) && $error}
  <div class="parent">
    <div class="error child">{$error|escape}</div>
  </div>
{elseif isset($action_message) && $action_message}
  <div class="ninja-notice">
    {$action_message|escape}
  </div>
{/if}
