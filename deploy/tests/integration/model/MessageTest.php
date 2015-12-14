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

    public function testMessageCanBeSent(){
        $text = 'This is some kind of random message';
        $char = new Player($this->char_id);
        $mess = Message::create(['send_from'=>$char->id(), 'send_to'=>$this->char_id_2, 'message'=>$text, 'type'=>0]);
        $this->assertEquals($text, $mess->message);
    }

    public function testMessageCanBeReceived(){
        $text = 'This is some kind of random message';
        $char = new Player($this->char_id);
        $mess = Message::create(['send_from'=>$char->id(), 'send_to'=>$this->char_id_2, 'message'=>$text, 'type'=>0]);
        $messages = Message::findByReceiver(new Player($this->char_id_2))->all();
        $first_message = Message::find($mess->id());
        $this->assertEquals($text, $first_message->message);
    }

    public function testMessageCanBeSentToGroup(){
        $text = 'Text of a group message to clan or whatever';
        $char = new Player($this->char_id);
        $mess = Message::create(['send_from'=>$char->id(), 'send_to'=>$this->char_id_2, 'message'=>$text, 'type'=>1]);
        $this->assertTrue($mess instanceof Message);
        $messages = Message::findByReceiver(new Player($this->char_id_2), $type=1);
        $this->assertGreaterThan(0, count($messages), 'Message array should have some elements');
        $first_message = $messages->first();
        $this->assertTrue($first_message instanceof Message);
        $this->assertEquals($text, $first_message->message);
    }

    public function testMessageHasARobustSender(){
        $rec = new Player($this->char_id);
        Message::create(['send_from'=>$rec->id(), 'send_to'=>$this->char_id_2, 'message'=>'Random phpunit test message of some content', 'type'=>0]);
        $messages = Message::findByReceiver(new Player($this->char_id_2), $type=0, $limit=1000, $offset=0);
        $this->assertGreaterThan(0, count($messages), 'Collection has no results found');
        $first_message = $messages->first();
        $this->assertTrue($first_message instanceof Message, 'First message not a valid message model');
        $this->assertNotEmpty($first_message->sender);
        $this->assertGreaterThan(0, strlen($first_message->sender));
    }

    public function testCreateMessageViaMassAssignment(){
    	$mess = Message::create(['message'=>'Random phpunit test message', 'send_to'=>$this->char_id, 
                'send_from'=>$this->char_id_2, 'unread'=>1]);
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
        $messages = Message::findByReceiver($char)->all();
        $this->assertEquals(4, count($messages));
        Message::deleteByReceiver($char, $type=0);
        $this->assertEquals(0, Message::countByReceiver($char));
    }

}

