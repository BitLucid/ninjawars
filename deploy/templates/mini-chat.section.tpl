<style>
#chat-and-switch #chat-input{
   width:55%;display:inline;margin-top:.5em;margin-bottom:.5em;
}
#chat-and-switch #chat-button-box{
  width:40%;display:inline;margin-top:.5em;margin-bottom:.5em;
}
#chat-and-switch #chat-button{
  display:inline;margin-right:.3em;margin-left:.3em;
}
.chat-author.template, .chat-message.template{
  display:none;
}
.chat-switch .tiny-speech-bubble{
   width:10px;height:9px;
}

</style>

          <div id='index-chat'>
              <div id="village-chat" class="boxes active">
                <div class="box-title centered">
                  Ninja Chat
                </div>
                
                <div class='active-members-count'>
                  Ninjas:
                  <span id="active-members-display">{$members|default:'-'}</span> Active
                  /
                  <span id="total-members-display">{$membersTotal|default:'-'}</span> Total
                </div>
                
                <div id="chat-and-switch">
{if isset($user_id) and $user_id}
                  <form class='chat-submit' id="post_msg_js" action="chat.php" method="post" name="post_msg">
                  <!-- Check for post_msg_js in nw.js for the functionality -->
                  
                    <div id='chat-input'>
                        <input type="text" size="20" maxlength="250" name="message" class="textField">
                    </div>
                    <div id='chat-button-box'>
                        <input id='chat-button' type="submit" value="Chat" class="formButton">
                    </div>
                  </form>
{/if}
                  <div id="mini-chat-container" class='chat-collapsed'>
                  
                  
                    <!-- Chat section with template chat author and messages --> 
                    <dl id="mini-chat-display" class="chat-messages" style='display:none'>
                       	<dt class='chat-author template'  style='display:none'>&lsaquo;<a href='player.php?player_id=' target='main'></a>&rsaquo;</dt>
                       		<dd class='chat-message template' style='display:none'></dd>
                    </dl>


                  </div><!-- End of chat-collapsed -->
                </div>
              </div>
              
              <div class="chat-switch centered">
                <a id='full-chat-link' href="village.php" target="main">
                	View ninja chat archive<img src="images/chat.png" class='tiny-speech-bubble' alt="">
                </a>
              </div>
              
          </div> <!-- End of index-chat --> 
