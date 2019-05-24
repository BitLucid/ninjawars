    <div id='player-tags'>
      <h4 id='player-tags-title'>All Players</h4>
      <ul>
{foreach from=$player_size key="player" item="info"}
        <li class='player-tag size{$info.size}'>
          <a href='/player?player_id={$info.player_id|escape:url}'>{$player|escape}</a>
        </li>
{/foreach}
      </ul>
    </div>
