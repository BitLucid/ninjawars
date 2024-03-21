<style>
#chat-and-switch{
  padding-left: 1rem;
  padding-top: 1.7rem;
}
#chat-and-switch #chat-input{
   padding-left: 2rem;
   width:55%;
   display:inline;
   margin-top:.5em;
   margin-bottom:.5em;
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

/* media query for small screens */
@media (max-width: 768px) {
  #chat-and-switch #chat-input{
    width: 100%;
    padding-left: 0;
  }
  #chat-and-switch #chat-button-box{
    width: 100%;
    padding-left: 0;
  }
  #chat-and-switch #chat-button{
    width: 100%;
    margin-top: 1rem;
    margin-right: 0;
    margin-left: 0;
  }
}

</style>

          <div id='index-chat'>
              <div id="village-chat" class="boxes active">
                <div class="box-title centered">
                  Ninja Chat
                </div>

                <div id="chat-and-switch">

                  <form id="post_msg_js" class='chat-submit' name="post_msg" {if isset($user_id) and $user_id}data-logged-in='true'{/if} action="/chat" method="post">
                  <!-- Check for post_msg_js in chat.js for the functionality -->

                    <div class='{if !isset($user_id) or !$user_id}hidden{/if}'>
                      <div id='chat-input'>
                          <input type="text" size="20" maxlength="250" name="message" autocomplete='off' class="textField">
                      </div>
                      <div id='chat-button-box'>
                          <input id='chat-button' type="submit" value="Chat" class="formButton btn btn-sm">
                      </div>
                    </div>

                  </form>

                  <div id="mini-chat-container" class='chat-collapsed dont-break-out'>


                    <!-- Chat section with template chat author and messages -->
                    <dl id="mini-chat-display" class="chat-messages" style='display:none'>
                       	<dt class='chat-author template'  style='display:none'>&lsaquo;<a href='/player?player_id=' target='main'></a>&rsaquo;</dt>
                       		<dd class='chat-message template' style='display:none'></dd>
                    </dl>


                  </div><!-- End of chat-collapsed -->
                </div>
              </div>

              <div class="chat-switch centered">
                <a id='full-chat-link' href="/village" target="main">
                	View ninja chat archive<img src="{cachebust file="/images/chat.png"}" class='tiny-speech-bubble' alt="">
                </a>
              </div>

          </div> <!-- End of index-chat -->
