{if isset($error) && $error}
  <div class="parent">
    <div class="error child fade-in">{$error|escape}</div>
  </div>
{elseif isset($action_message) && $action_message}
  <div class="ninja-notice fade-in">
    <!-- flash message -->
    {$action_message|escape}
  </div>
{/if}
