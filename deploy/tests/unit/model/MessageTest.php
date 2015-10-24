<?php
// Note that the file has to have a file ending of ...test.php to be run by phpunit
require_once(CORE.'data/Message.php');

use app\data\Message;


class TestMessage extends PHPUnit_Framework_TestCase {


	function setUp(){
		$this->char_id = TestAccountCreateAndDestroy::char_id($confirm=true);
		$this->char_id_2 = TestAccountCreateAndDestroy::char_id_2($confirm=true);
		$this->message = Message::create(['message'=>'Random phpunit Test Message', 'send_to'=>$this->char_id, 'send_from'=>$this->char_id_2, 'unread'=>1, 'type'=>null]);
		$this->delete_this_message = null;
	}
	
	function tearDown(){
		TestAccountCreateAndDestroy::destroy();
		$this->message->delete();
    }

    public function testMessageCanInstantiate(){
        $mess = new Message();
        $this->assertTrue($mess instanceof Message);
    }

    public function testCreateMessageViaMassAssignment(){
    	$mess = Message::create(['message'=>'Random phpunit test message', 'send_to'=>$this->char_id, 'send_from'=>$this->char_id_2, 'unread'=>1]);
    	$text = 'Updated phpunit test message';
    	$mess->message = $text;
    	$mess->save(); // Save the newly updated message
    	$id = $mess->id();
    	$retrieved_message = Message::find($id);
    	$retrieved_text = $retrieved_message->message;
    	$retrieved_message->delete();
    	$this->assertEquals($text, $retrieved_text);
    }

    public function testFindPrivateMessagesForACertainChar(){
        $i = 4;
        while($i--){
            Message::create(['message'=>'Random phpunit test message'.$i, 'send_to'=>$this->char_id, 
                'send_from'=>$this->char_id_2, 'unread'=>1]);
        }
        $char = new Player($this->char_id);
        $messages = Message::findByReceiver($char);
        $this->assertEquals(4, count($messages));
        Message::deleteByReceiver($char, $type=0);
        $this->assertEquals(0, Message::countByReceiver($char));
    }

    public function testMessageHasASender(){
        $rec = new Player($this->char_id);
        Message::create(['message'=>'Random phpunit test message', 'send_to'=>$rec->id(), 
                'send_from'=>$this->char_id_2, 'unread'=>1]);
        $messages = Message::findByReceiver($rec);
        debug($messages);
        $first_message = reset($messages);
        $this->assertNotEmpty($first_message->sender);
    }


}

