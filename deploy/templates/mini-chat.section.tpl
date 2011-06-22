          <div id='index-chat'>
              <div id="village-chat" class="boxes active">
                <div class="box-title centered">
                  Chat
                </div>
                
                <div class='active-members-count'>
                  Ninjas:
                  <span id="active-members-display">{$members|default:'???'}</span> Active
                  /
                  <span id="total-members-display">{$membersTotal|default:'???'}</span> Total
                </div>
                
                <div id="chat-and-switch">
{if isset($user_id) and $user_id}
                  <form class='chat-submit' id="post_msg_js" action="chat.php" method="post" name="post_msg">
                  
                    <div style='width:55%;display:inline;margin-top:.5em;margin-bottom:.5em;'>
                        <input type="text" size="20" maxlength="250" name="message" class="textField">
                    </div>
                    <div style='width:40%;display:inline;margin-top:.5em;margin-bottom:.5em;'>
                        <input type="submit" value="Chat" class="formButton" style="display:inline;margin-right:.3em;margin-left:.3em;">
                        <img src="images/refresh.gif" id='chat-refresh-image' alt="Refresh" style="max-height:24px;max-width:24px;cursor: pointer;display:none;margin-top:.2em">
                    </div>
                  </form>
{/if}
                  <div id="mini-chat-container" class='chat-collapsed'>
                  
                  
                  
                  
                  
                  
                  
                    <!-- Chat section with template chat author and messages --> 
                    <dl id="mini-chat-display" class="chat-messages" style='display:hidden'>
                       	<dt class='chat-author'>&lt;<a href='player.php?player_id=' target='main'></a>&gt;</dt>
                       		<dd class='chat-message' title=''></dd>
                    </dl>
                    
                    <noscript>
                          <a href='chat.php' target='_blank'>View the chat</a>
                    </noscript>


                  </div><!-- End of chat-collapsed -->
                </div>
              </div>
              
              <div class="chat-switch centered">
                <a id='full-chat-link' href="village.php" target="main">
                	View full chat archive<img src="images/chat.png" alt="" style='width:10px;height:9px'>
                </a>
              </div>
              
          </div> <!-- End of index-chat --> 
