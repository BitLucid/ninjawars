          <div id='index-chat'>
              <div id="village-chat" class="boxes active">
                <div class="box-title centered">
                  <a id='show-hide-chat' class="show-hide-link">
                    Chat
                  </a>
                </div>
                <div id="chat-and-switch">
                  <div class="chat-switch centered">
                    <a id='full-chat-link' href="village.php" target="main">Full Chat <img src="images/chat.png" alt="" style='width:10px;height:9px'> </a>
                  </div>
{if isset($user_id) and $user_id}
                  <form class='chat-submit' id="post_msg_js" action="mini_chat.php" method="post" name="post_msg">
                    <div>
                      <div><input type="text" size="20" maxlength="250" name="message" class="textField"></div>
                      <div style="height: 40px;">
                        <input type="submit" value="Chat" class="formButton" style="display: block;float: left;margin-top: 8px;">
                        <img src="images/refresh.gif" alt="()" onclick="NW.chatRefreshClicked(this);" height="24" width="24" style="cursor: pointer;margin-top: 6px;margin-left: 4px;">
                      </div>
                    </div>
                  </form>
{/if}
                  <div id="mini-chat-frame-container" class='chat-collapsed'>
                    <dl id="mini-chat-display" class="chat-messages">
                    </dl>
                    <noscript>
{if isset($user_id) and $user_id}
                  <form class='chat-submit' id="post_msg" action="mini_chat.php" method="post" name="post_msg" target='mini_chat'>
                    <input id="message" type="text" size="20" maxlength="250" name="message" class="textField">
                    <input id="command" type="hidden" value="postnow" name="command">
                    <input name='chat_submit' type='hidden' value='1'>
                    <button type="submit" value="1" class="formButton">Chat</button>
                  </form>
{/if}
                        <iframe frameBorder='0' id="mini_chat" name="mini_chat" src="mini_chat.php">
                        <!-- Note the the frameBorder attribute is apparently case sensitive in some versions of ie -->
                          <a href='mini_chat.php' target='_blank'>Mini Chat</a> unavailable inside this browser window.
                        </iframe>
                    </noscript>
                  </div>
                  <div id="expand-chat">
                    <a href="mini_chat.php?chatlength=360" target="mini_chat" onclick="main.location.href = 'village.php';return false;">
                      View more chat messages <!-- <img class="show-hide-icon" src="images/show_and_hide.png" alt="+/-"> -->
                    </a>
                  </div>
                </div>
              </div>
          </div> <!-- End of index-chat --> 


