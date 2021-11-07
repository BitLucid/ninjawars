<h1>Staging</h1>

<style>
{literal}
.target-container{
    display:flex;
    justify-content: space-around;
    align-items: stretch;
}
.target-container > *{
    height: 100%;
    min-height: 30vh;
    flex:1;
}
.target-container .previous{
    background-color: #f0f0f0;

}
.target-container .next{
    background-color: #f0f0f0;
}
.target-container .preview{
    flex: 8;
    background-color: #b0a89d;
    display:flex;
    justify-content: center;
}
.action-area{
    display:flex;
    justify-content: space-around;
    align-items: center;
    flex: 1;
    background-color: #3d1818;
    min-height: 20vh;
}
.enemies{
    display:grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
}
.enemies > *{
    min-height:10vh;
    background:teal;
    border:thin solid black;
    display:flex;
    justify-content: center;
    align-items: center;
}
{/literal}
</style>
<section class='target-container'>
<div class='previous'>
    &lt;
</div>
<div class='preview'>
    Some ninja
</div>
<div class='next'>
    &gt;
</div>

</section>

<div class='action-area'>
    <button class='btn btn-primary'>ATTACK</button>
</div>

<div class='enemies'>
<div>Enemy</div>
<div>Enemy</div>    <div>Enemy</div>
<div>Enemy</div>    <div>Enemy</div>
<div>Enemy</div>    <div>Enemy</div>
<div>Enemy</div>    <div>Enemy</div>
<div>Enemy</div>    <div>Enemy</div>
<div><i class='fa fa-plus'></i></div>
<div>Enemy</div>    <div>Enemy</div>
<div>Enemy</div>    <div>Enemy</div>
<div>Enemy</div>    <div>Enemy</div>
<div>Enemy</div>    <div>Enemy</div>
<div>Enemy</div>    <div>Enemy</div>
<div>Enemy</div>
</div>

<nav>
  <a href="/map" class="return-to-location block">Return to the Map</a>
</nav>