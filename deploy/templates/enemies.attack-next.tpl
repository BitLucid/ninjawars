{*
<script>
var enemy = 'enemy to array @json_encode'
</script>
*}
{* $smarty->register->templateClass('foo','name\name2\myclass'); *}
<style>
.attack-next {
    margin: 1rem 2rem;
    display:grid;
}
.attack-next .main {
    display:grid;
}
.attack-next .avatar {
    text-align: center;
    padding: 0 0.4rem;
    display: inline-block;
}
.attack-next .duel-area .svg-shuriken svg{
    height: 5rem;
    width: 5rem;
}
.attack-next .player-class .svg-shuriken svg{
    height: 3rem;
    width: 3rem;
}
.attack-next .duel-area, .attack-next .item-use-area{
    display: inline-block;
}
.attack-next .item-use-area{
    text-align: right;
}
.attack-next .use-item {
    border-top-left-radius:0;
    border-bottom-left-radius:0
}
.attack-next .player-level-category {
    display: inline-block;
    padding: 0.5rem 0.7rem;
}
.attack-next .player-class {
    display: inline-block;
    padding: 0.05rem 0.5rem;
}
.attack-next .health-bar-container {
    display: inline-block;
    min-width: 7.5rem;
}
.attack-next .item-select {
    color: #ffffff;
    background-color: #555555;
    display: inline-block;
    max-width: 40%;
    width: 17rem;
}
.attack-next .carousel {
    display:grid;
    grid-template-columns: 9% 80% 9%;
    grid-gap: 1%;
}
.attack-next .spin-enemy {
    font-size: 15rem;
}
.attack-next .spin-enemy a:hover{
    text-decoration: none;
}
.attack-next .spin-enemy.up {
    text-align: right;
}
.attack-next .c-box{
    display: block;
    text-align:center;
}
</style>

{if $enemy}
<section class="attack-next">
    <div class='carousel'>
        <div class='spin-enemy down'>
            {if $shift}
            <a href='?shift={math equation="max(0, x-1)" x=$shift}'>❮</a>
            {/if}
            &nbsp;
        </div>
        <div class='main'>
            {* Display the ninja *}
            <div class='ninja-area ninja-card'>
                <h2>{$enemy->name()|escape}</h2>
                <div class='avatar'>
                    {include file="gravatar.tpl" gurl=$enemy->avatarUrl()}
                </div>
                <span class='player-class class-name {$enemy->theme|escape}'>
                    <span class="svg-shuriken">
                    {include file="shuriken.svg.tpl"}
                    </span>
                    {$enemy->class_name|escape}
                </span><!-- no space 
                --><span class='player-level-category {$enemy->level|level_label|css_classify}'>
                    {$enemy->level|level_label} [{$enemy->level|escape}]
                </span>
                <span class='health-bar-container'>
                    {include file="health_bar.tpl" health=$enemy->health level=$enemy->level}
                </span>
                <div class='c-box'>
                    {include file="status_section.tpl" statuses=\NinjaWars\core\data\Player::getStatusList($enemy->id())}
                </div>
                {if $enemy->description}
                <blockquote>
                {$enemy->name()|escape} {$enemy->description|escape}
                </blockquote>
                {/if}
            </div>
            <div class='attack-area'>
                <div class='glassbox duel-area'>
                    <form id='attack_player' action='/attack' method='post' name='attack_player'>
                        <input id="duel" type="hidden" name="duel" value="1">
                        <div class='btn-group' role='group'>
                            {foreach from=$combat_skills item="skill"}<!-- No space
                                --><label class='btn btn-default' for='{$skill.skill_internal_name|escape}' title='{$skill.skill_internal_name|escape} while attacking for {getTurnCost skillName=$skill.skill_display_name} turns more'>
                                <input id="{$skill.skill_internal_name|escape}" type="checkbox" name="{$skill.skill_internal_name|escape}" value="1"> {$skill.skill_display_name|escape}
                                </label><!-- no space
                            -->{/foreach}<!-- no space
                            --><input id="target" type="hidden" value="{$enemy->id()|escape}" name="target">
                            <button type="submit" class='btn btn-vital'>
                                <span class='svg-shuriken'>
                                    {include file='shuriken.svg.tpl'}
                                </span>
                                Attack
                            </button>
                        </div>
                    </form>
                </div>

                {* Item-based attack options *}
                <div class='item-use-area'>
                    <form id="inventory_form" action="/player/use_item/" method="post" name="inventory_form">
                    <div class="form-group">
                        <input type="hidden" name="target_id" value="{$enemy->id()|escape}">
                        {foreach $items as $item}
                            {if $item@first}
                                <select id="item" name="item" class="form-control item-select">
                            {/if}
                            {if $item.other_usable && $item.count>0}
                                <option value="{$item.item_id|escape}">{$item.name|escape} ({$item.count|escape})</option>
                            {/if}
                            {if $item@last}
                                </select><!-- No space between --><input type="submit" value="Use Item" class="btn btn-default use-item">
                            {/if}
                        {foreachelse}
                        <div id='no-items' class='ninja-notice'>
                        You have no items.
                        </div>
                        {/foreach}
                    </div>
                    </form>
                </div>
                <div class='skill-use-area'>
                    <form id="skill_use" class="skill_use" action="/player/use_skill/" method="post" name="skill_use">
                        <div class='parent'>
                        <div class='child btn-group' id='skills-use-list'>
                        {foreach from=$targeted_skills item="skill"}
                            <input id="act-{$skill.skill_internal_name}" class="act btn btn-default" type="submit" value="{$skill.skill_display_name}" name="act" title='Use the {$skill.skill_display_name} skill for a cost of {getTurnCost skillName=$skill.skill_display_name} turns'>
                            <input id="target" class="target" type="hidden" value="{$enemy->name()|escape}" name="target">
                        {/foreach}
                        </div>
                        </div>
                    </form>
                </div>
                <div class='centered glassbox'>
                    <form id='attack_player' action='/attack' method='post' name='attack_player'>
                        <input id="target" type="hidden" value="{$enemy->id()|escape}" name="target">
                        <button type="submit" class='btn btn-default'>Strike</button>
                    </form>
                </div>
            </div><!-- end of attack-area -->
        </div><!-- End of main ninja and attacks display -->
        <div class='spin-enemy up'>
            <a href='?shift={math equation="min(300, x+1)" x=$shift}'>❯</a>
        </div>
    </div>
</section>
{/if}