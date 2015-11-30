<style>
{literal}

#map table {
	margin: 0 auto;
}

#map td{
	background-color:black;
}

#map td.doshin {
	background-image:url(/images/nodes/node-tiles.png);
	background-position:0px 0px;
	background-repeat:no-repeat;
}

#map td.dojo {
	background-image:url(/images/nodes/node-tiles.png);
	background-position:0px -111px;
	background-repeat:no-repeat;
}

#map td.shrine {
	background-image:url(/images/nodes/node-tiles.png);
	background-position:0px -227px;
	background-repeat:no-repeat;
}

#map td.rice-field {
	/*background-image:url(/images/nodes/node-tiles.png);
	background-position:0px -342px;
	background-repeat:no-repeat;*/
    background-color:rgb(12,44,44);
}

#map td.casino {
	background-image:url(/images/nodes/node-tiles.png);
	background-position:0px -444px;
	background-repeat:no-repeat;
}

#map td.weapons-shop {
	background-image:url(/images/nodes/node-tiles.png);
	background-position:0px -558px;
	background-repeat:no-repeat;
}

#map td.grass{
	background-color:rgb(30,60,0);
}

#map td.north-south-road{
    background-color:rgb(55, 55, 55);
}

#map td.west-east-road, #map td.east-west-road{
    background-color:rgb(65, 65, 65);
}

#map td.bath-house{
    background-color:rgb(203,66,60);
}

#map td.village-square{
    background-color:teal;
}
#map .area-name{
    background-color:rgb(55, 10, 10);font-size:1.3em;text-align:center;text-transform:uppercase;padding:.7em;
}
#map .map-grid{
    width:510px;background-color:black;color:rgb(155, 155, 155);
}
#map .details{
    width:66%;text-align:center;min-height:1.2em;font-size:1.1em;line-height:1.1;background-color:black;background-color:rgba(0,0,0,.8);margin-left:17%;margin-top:25%;border-radius:1em;padding:.3em 0 .3em;
}
#map .point{
    width:100px;height:100px;margin:0;padding:0;
}

{/literal}
</style>
    
    
    <div id='map'>
    <table class='map-grid'>
    	<thead>
    	<tr>
    		<td colspan='5' class='area-name'>The Village</td>
    	</tr>
    	</thead>
    	<!-- <tfoot></tfoot> -->
    	<tbody>

{foreach name='foreach-row' from=$nodes item='row' key='ycoord'}
    	<tr>
{foreach name='foreach-over-row-of-nodes' from=$row item='node' key='xcoord'}
    		<td class='node-id-{$node.id} {$node.type} point'>	
    			<form action='map.php' method='post'>
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

    		
    		</td>
{/foreach}
    	</tr>
{/foreach}
	</tbody>
	</table>

	</div> <!-- End of map div -->
