		<div id="login-bar">
          <form id="login-form" action="index.php#" method="post">
            <span class="text">
              <input type="hidden" name="ref" value="{$referrer|escape}">
                <label>
                  <!-- Username -->
                  <input name="user" type="text" value='{$stored_username|escape}' class="itext">
                </label>
                <label>
                  <!-- Password -->
                  <input name="pass" type="password" class="itext">
                </label>
                <input id='login-button' name="action" type="submit" value="Login" class="ibutton formButton">
              </span>
            </form>
          </div>
