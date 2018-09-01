<style>
{literal}

#map .map-grid{
    width:55rem;
	height:55rem;
	margin:auto;
	background-color:#807F6F;
	color:rgb(155, 155, 155);
	display: grid;
	grid-template-columns: repeat(5, 1fr);
	grid-template-rows: repeat(5, 1fr);
	grid-gap: 0;
}
#map .details{
    width:66%;
	text-align:center;
	min-height:1.2rem;
	font-size:2.1rem;
	line-height:1.1;
	background-color:black;
	background-color:rgba(0,0,0,.8);
	margin-left:17%;
	margin-top:25%;
	border-radius:0.6rem;
	padding:0.1rem 0.1rem;
}
#map .point{
	width:20%;
	height:20%;
	margin:0;padding:0;
	background-color:#807F6F;
}

#map .node.doshin {
	background-repeat:no-repeat;
    background-color:#009497;
}

#map .node.dojo {
	background-repeat:no-repeat;
    background-color:teal;
}

#map .node.shrine {
	background-repeat:no-repeat;
    background-color:#800040;
}

#map .node.rice-field {
    background-color:rgb(27, 43, 31);
    background-image:url(/images/nodes/lightdirt.jpg);
    background-size:100% 100%;
}

#map .node.casino {
	background-repeat:no-repeat;
    background-color:rgb(181, 157, 85);
}

#map .node.weapons-shop {
	background-repeat:no-repeat;
    background-color:#753700;
}

#map .node.grass{
	background-color:rgb(30,60,0);
    background-image:url(/images/nodes/grass.jpg);
    background-size:100% 100%;
}

#map .node.north-south-road{
    background-color:rgb(55, 55, 55);
    background-image:url(/images/nodes/darkgranite.jpg);
    background-size:100% 100%;
}

#map .node.west-east-road, #map .node.east-west-road{
    background-color:rgb(65, 65, 65);
    background-image:url(/images/nodes/darkgranite.jpg);
    background-size:100% 100%;
}

#map .node.bath-house{
    background-color:#800040;
}

#map .node.village-square{
    background-color:#dd164f;
}
#map .area-name{
    background-color:rgb(55, 10, 10);font-size:1.3em;text-align:center;text-transform:uppercase;padding:.7em;
}


{/literal}
</style>

    <div id='map'>
		<section class='map-grid'>

{foreach name='foreach_row' from=$nodes item='row' key='ycoord'}
    		<!-- Row start -->
{foreach name='foreach_over_row_of_nodes' from=$row item='node' key='xcoord'}
				<div class='node-id-{$node.id} {$node.type} node'>
					<form action='/map' method='post'>
						<input type='hidden' name='ycoord' value='{$ycoord}>
						<input type='hidden' name='xcoord' value='{$xcoord}>
						<input type='submit' name='move' value='{if isset($node.name)}{$node.name}{else}Go{/if}'>
					</form>

					<div class='details'>

						{if isset($node.url) && $node.url}<a href='{$node.url|escape:'url'|escape}' target='main'>{/if}
						{if isset($node.image)}
						<img src='/images/{$node.image|escape:'url'|escape}' alt='' style='width:8px;height:8px'>
						{/if}
						{if $node.url}Enter {/if}
						{$node.name|escape}
						{if isset($node.url) && $node.url}</a>{/if}

					</div>

				</div>
{/foreach}
    		<!-- Row end -->
{/foreach}
		</section>
	</div> <!-- End of map div -->
