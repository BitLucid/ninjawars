<style>
{literal}
.nearby-characters .gridded{
	display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
}
.nearby-characters .gridded>* {
	min-height: 10vh;
	background: teal;
	border: thin solid black;
	display: flex;
	justify-content: center;
	align-items: center;
}
.nearby-characters .gridded a {
	color: white;
	text-decoration: none;
}
.nearby-characters .available-target{
	white-space: nowrap;
}
{/literal}
</style>

<template name='available-target'>
	<li class='available-target'>
		<a href='#' class='nearby-character' data-character-id='0'>
			<img class='char-image' src='' />
			<span class='char-name'></span>
		</a>	
	</li>
</template>

	<section class='nearby-characters'>
		<div class='title'>Nearby</div>
		<div>
	    	<ul class='gridded'>
				<li id='remove-item' class='available-target'>
					<a href='#' class='nearby-character' data-character-id='0'>
						<img class='char-image' src='' />
						<span class='char-name'></span>
					</a>	
				</li>
	    	</ul>
		</div>
	</section>

	<template name='available-target'>
		<li class='available-target'>
			<a href='#' class='nearby-character' data-character-id='0'>
				<img class='char-image' src='' />
				<span class='char-name'></span>
			</a>	
		</li>
	</template>

<script src='/js/api.js' type='module'></script>
<script src='/js/map.js' type='module'></script>

<script>
{literal}
	function ready(callback){
    // in case the document is already rendered
    if (document.readyState!='loading') callback();
    // modern browsers
    else if (document.addEventListener) document.addEventListener('DOMContentLoaded', callback);
    // IE <= 8
    else document.attachEvent('onreadystatechange', function(){
        if (document.readyState=='complete') callback();
    });
}

ready(function(){
	// Gets targets from the api, put them in the template, then clone to the grid
    getTargets();
});
{/literal}
</script>
