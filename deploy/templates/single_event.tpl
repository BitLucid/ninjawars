<li>
    <a target='main' href='/player?player_id={$event.send_from|escape:'url'}'>{$event.from|escape}</a>
    <span class='user-event{if $event.unread} event-unread{/if}'>{$event.message|escape|replace_urls|markdown}</span>
</li>
