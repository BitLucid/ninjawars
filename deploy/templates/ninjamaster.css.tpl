<style>
.float-right{
	float:right;clear:right;
}
.headed{
	border-top:1px brown solid;
}
.special-info{
	margin-bottom:3rem;
}
.char-actions{
	display:flex;
	justify-content: space-evenly;
	font-size: larger;
}
.char-actions > a{
	padding: 0.3rem 1rem;
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

.highlight-box{
	color: gray;
	border:thin solid gray;
	display:inline-block;
	padding: 1rem;
	font-weight: bolder;
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

.npc-raw-info-list-area{
	display:flex;
	flex-wrap: wrap;
	justify-content: space-between;
	padding: 1rem;
}
.npc-box.tiled{
	/*display:inline-block;*/
	padding: 0 0.3rem 0;
	margin: 0 0.3rem 2rem; /* l/r the gutter */
	border-radius: 0.5rem;
	max-width:50rem;
	width: calc(20% - 1rem);
	min-width: 20rem;
	vertical-align:top;
	box-sizing: border-box;
}
.npc-box.tiled h2{
	width:100%;margin:0;padding:0;transform:none;
}
.npc-box .npc-icon{
	max-width:100%;
	max-height:20em;
}
.npc-box figure{
	text-align: center;
}
.npc-box figure .npc-icon{
	display:inline-block;
	margin: 0 auto;
}
.npc-box .npc-traits li{
	display:inline-block;
	margin-right: 2rem;
}
.npc-box figcaption{
	color:gray;text-overflow:ellipsis;overflow:hidden;white-space:nowrap;max-width:10em;width:100%;
}
.npc-box .char-profile{
	text-overflow:ellipsis;overflow:hidden;white-space:nowrap;max-width:10em;width:100%;
}


/* Style for the npc-details area */
.npc-details {
  padding: 1rem;
  border: 1px solid #ddd;
  border-radius: 0.5rem;
  margin-top: 1rem;
}

/* Style for the definition list */
.npc-details dl {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 0.5rem;
}

/* Style for definition terms */
.npc-details dt {
  font-weight: bold;
}

/* Style for definition descriptions */
.npc-details dd {
  margin: 0;
}

/* Additional styling for the traits list */
.npc-traits {
  margin-top: 1rem;
}

.npc-traits ul {
  list-style-type: none;
  display: inline-block;
  padding: 0;
  margin: 0;
}

.npc-traits li {
  margin-bottom: 0.25rem;
}

/* End of npc-details styling */

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
	border-radius: 3rem;
	padding: 0.2rem 0.2rem 0.2rem 2rem;
	background: black;
}
button.show-hide-next .dot{
	display:inline-block;
	border-radius:50%;
	padding:0.5rem;
	background: white;
	height:2rem;
	width:2rem;
	vertical-align:bottom;
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
