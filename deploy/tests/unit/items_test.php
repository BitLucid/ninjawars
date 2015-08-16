<?php
// Note that the file has to have a file ending of ...test.php to be run by phpunit
require_once(ROOT.'lib/data/Item.php');

class TestItems extends PHPUnit_Framework_TestCase {


	function setUp(){
	}
	
	function tearDown(){
    }

    public function testThatTheShurikenCanBeMadeAnItem(){
        $item = new Item('shuriken');
        $this->assertTrue($item instanceof Item);
        $this->assertNotEmpty($item->identity());
        $this->assertEquals('shuriken', strtolower($item->identity()));
    }

    public function testThatAShurikenHasSliceEffect(){
        $item = new Item('shuriken');
        $this->assertGreaterThan(0, $item->hasEffect('slice'));
    }

    public function testThatAShurikenIsUsableOnOthers(){
        $item = new Item('shuriken');
        $this->assertTrue($item->isOtherUsable());
    }

    public function testThatShurikenDoesNotIgnoreStealth(){
        $item = new Item('shuriken');
        $this->assertFalse($item->ignoresStealth());
    }

    public function testThatShurikensArentSelfUsable(){
        $item = new Item('shuriken');
        $this->assertFalse($item->isSelfUsable());
    }

    public function testThatCaltropsIdeallyCauseNegativeTurnChange(){
        $item = new Item('caltrops');
        $this->assertLessThan(0, $item->getMaxTurnChange());
    }

    public function testBuffItemsIgnoreStealth(){
        foreach(['mirror', 'prayerwheel', 'shell', 'lantern'] as $ident){
            $item = new Item($ident);
            $this->assertTrue($item->ignoresStealth());
        }
    }

    public function testMeitoNamedKatanaIgnoresStealth(){
        $item = new Item('meito');
        $this->assertTrue($item->ignoresStealth());
    }

}

