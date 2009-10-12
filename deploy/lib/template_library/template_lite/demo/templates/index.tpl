{ config_load file="test.conf" }
{ assign var="title" value="foo" }
{ include file="header.tpl"}
<PRE>

{ assign var="foo" value="up" }
{ assign var="bar" value=0 }
01: { $lala } (// Array)
02: { $lala.up } (// first entry)
03: { $lala[$foo] } (// first entry)
04: { $lala[#blah#] } (// first entry)
05: { #snah# } (// 0)
06: { #bold# } (// Array)
07: { #bold[0]# } (// up)
08: { #bold[$bar]# } (// up)
09: { #bold[#snah#]# } (// up)
10: {* a comment {* inside a comment *} *}

{* bold and title are read from the config file *}
{ if #bold# }<b>{ /if }
{* capitalize the first letters of each word of the title *}
Title: { #title#|capitalize }
{ if #bold# }</b>{ /if }

{ literal }this is a block of literal text{ /literal }

The current date and time is { $templatelite[NOW]|date:"Y-m-d H:i:s" }

The value of global assigned variable $SCRIPT_NAME is { $templatelite.SERVER.SCRIPT_NAME }

Example of accessing server environment variable SERVER_NAME: { $templatelite.SERVER.SERVER_NAME }

The value of { ldelim } $Name { rdelim } is <b>{ $Name }</b>

variable modifier example of { ldelim } $Name|upper { rdelim }

<b>{ $Name|upper }</b>


An example of a foreach loop:

{ foreach value=value from=$FirstName }
	{ $value }
{ foreachelse }
	none
{ /foreach }

An example of foreach looped key values:

{ foreach value=value from=$contacts }
	phone: { $value.phone }<br>
	fax: { $value.fax }<br>
	cell: { $value.cell }<br>
{ /foreach }
<p>

testing strip tags
{ strip }
<table border=0>
	<tr>
		<td>
			<A HREF="{ $templatelite.SERVER.SCRIPT_NAME }">
			<font color="red">This is a  test     </font>
			</A>
		</td>
	</tr>
</table>
{ /strip }

</PRE>

{ assign var="var" value=2 }
{if $var is even}
	Yes it's even.<br><br>
{/if}
{ assign var="var" value=3 }
{if $var is odd}
	Yes it's odd.<br><br>
{/if}
{ assign var="var" value=2 }
{if $var is not odd}
	No not odd.<br><br>
{/if}


{ assign var="var" value=8 }
{if $var is div by 4}
	Yes it is divisible by 4.<br><br>
{/if}

{ assign var="var" value=8 }
{if $var is even by 2}
	Yes it is even by 2.<br><br>
{/if}

{ assign var="var" value=6 }
{if $var is even by 3}
 Yes it is even by 3.<br><br>
{/if}
{ include file="footer.tpl" }
