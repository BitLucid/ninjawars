{*
<script>
var enemy = 'enemy to array @json_encode'
</script>
*}
{* $smarty->register->templateClass('foo','name\name2\myclass'); *}

<style>
.attack-next {
    max-width: 50%;
    margin: 1rem auto;
}
.attack-next .avatar {
    text-align: center;
}
.attack-next .svg-shuriken svg{
    height: 5rem;
    width: 5rem;
}
</style>

<section class="attack-next">
    <form action="/attack" method="POST" name="attack-next">
        {* Display the ninja *}
        <div>
            <h2>{$enemy->name()|escape}</h2>
            <div class='avatar'>
                {include file="gravatar.tpl" gurl=$enemy->avatarUrl()}
            </div>
            <span class='health-bar-container'>
                {include file="health_bar.tpl" health=$enemy->health level=$enemy->level}
            </span>
            <div class='c-box'>
                {include file="status_section.tpl" statuses=\NinjaWars\core\data\Player::getStatusList($enemy->id())}
            </div>
        </div>
        {* Display attack with additional settings *}
        <button type="submit" class='btn btn-default'>
            <span class='svg-shuriken'>
                {include file='shuriken.svg.tpl'}
            </span>
            attack
        </button>
        <label>Blaze<input type='checkbox' name='attack-modifiers' ></label>
        <label>Deflect<input type='checkbox' name='attack-modifiers' ></label>
        <label>Evation<input type='checkbox' name='attack-modifiers' ></label>
        <label>Strike<input type='checkbox' name='attack-modifiers' ></label>
        <button type="submit" class='btn btn-default'>Strike <em class='char-name'>{$enemy->name()|escape}</em></button>

        {* Item-based attack options *}
        <div>
            Attack with: <select><option value='shuriken' />Shuriken</option><select>
        </div>
        <p>My Name: {$char->name()|escape}
    </form>
</section>