    <dt class='user {if isset($last_user_event) && $last_user_event eq $event.send_from}repeated{/if}'>
{if $event.send_from gt 0}
<a target='main' class='event-sender' href='/player?player_id={$event.send_from|escape:'url'}'>{$event.from|escape}{if !$event.from}•{/if}</a>
{else}
        <strong class='generic-event'><i class="fa fa-star" aria-hidden="true"></i></strong>
{/if}
    </dt>
    <dd class='user-message{if $event.unread} message-unread{/if}'>
    	{$event.message|escape|replace_urls|markdown}<time class="event-time timeago" datetime="{$event.date}" title="{$event.date}">{$event.date}</time>
    </dd>

    {assign var='last_user_event' value=$event.send_from}
