<h1>Chat Board</h1>
<p><a href="village.php?chatlength=50">Refresh</a><p>

{$chat_refresh}
<div id='full-chat'>
{$input_form}
    <div class='active-members-count'>
        Ninjas: {$active_chars} Active / {$chars_online} Online / {$total_chars} Total
    </div>
{$chat_messages}
</div>
