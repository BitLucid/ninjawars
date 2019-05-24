    <div id='player-tags'>
      <h4 id='player-tags-title'>All Players</h4>
      <p>Ranked by size and score</p>
      <ul>
{foreach from=$player_size key="player" item="info"}
        <li class='player-tag size{$info.size}'>
          <a title='{$info.score|escape}' href='/player?player_id={$info.player_id|escape:url}'>{$player|escape} [{$info.score|escape}]</a>
        </li>
{/foreach}
      </ul>
    </div>
