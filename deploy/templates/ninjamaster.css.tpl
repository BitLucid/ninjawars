<style>
.float-right{
	float:right;clear:both;
}
.headed{
	border-top:1px brown solid;border-left:1px brown solid;
}
.char-inventory{
	height:1.3em;
}
.char-info-header{
	border-bottom:1px brown solid;color:#ADD8E6;
	padding-left: 0.25rem;
	padding-right: 0.25rem;
}
#char-info-scroll{
	max-width: 100vw;
	overflow-x: auto;
}

.carded-area{
	display:flex;
	justify-content: space-evenly;
}

.card {
  /* Add shadows to create the "card" effect */
  box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
  border: 1px solid #424954;
  border-radius: 0.25rem;
  display:inline-block;
  max-width: 25vw;
  height: 20rem;
  overflow-y: auto;
}

/* On mouse-over, add a deeper shadow */
.card:hover {
  box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
}

/* Add some padding inside the card container */
.card .card-container {
  padding: 2px 16px;
}

#admin-actions{
	background: #292828;
}

#admin-actions table caption{
	text-align:left;padding-left:10%;
}
.npc-box.tiled{
	display:inline-block; max-width:50em;vertical-align:top;
}
.npc-box.tiled h2{
	width:100%;margin:0;padding:0;transform:none;
}
.npc-box .npc-icon{
	max-width:48em;height:5em;
}
.npc-box figcaption{
	color:gray;text-overflow:ellipsis;overflow:hidden;white-space:nowrap;max-width:10em;width:100%;
}
.npc-box .char-profile{
	text-overflow:ellipsis;overflow:hidden;white-space:nowrap;max-width:10em;width:100%;
}
.npc-box dl strong{
	color:teal;
}
nav.admin-nav > div{
	background-color:rgba(129, 45, 12, 0.5);padding:0.5em 2em;
}
nav.admin-nav a{
	display:inline-block;margin-left:2em;
}
#duplicate-ips .ip{
	font-family:monospace;color:#C2E;
}
#admin-actions .account-info time{
	color:gray;
}
#admin-actions .half-width{
	width:49%;vertical-align:top;
}
button.show-hide-next{
	border-radius: 0.9rem;
}
details {
    border: 1px solid #aaa;
    border-radius: 4px;
    padding: .5em .5em 0;
}

summary {
    font-weight: bold;
    margin: -.5em -.5em 0;
    padding: .5em;
	cursor:pointer;
}
summary::before{
	content: '▶ ';
}

details[open] {
    padding: .5em;
}

details[open] summary {
    border-bottom: 1px solid #aaa;
    margin-bottom: .5em;
}
details[open] summary::before{
	content: '▼ ';
}
.constrained{
	display:grid;
	justify-items: center;
	max-width: 30%;
	min-width: 50rem;
	margin-left: auto;
	margin-right: auto;
}
</style>