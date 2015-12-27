<?php
// Note that the file has to have a file ending of ...test.php to be run by phpunit
require_once(CORE.'data/Message.php');

use app\data\Message;

class TestMessage extends PHPUnit_Framework_TestCase {
    private $char_id;
    private $char_id_2;
    private $messageData;

    function setUp() {
        $this->char_id = TestAccountCreateAndDestroy::char_id(true);
        $this->char_id_2 = TestAccountCreateAndDestroy::char_id_2(true);

        $this->messageData = [
            'message'   => 'Random phpunit Test Message',
            'send_from' => $this->char_id,
            'send_to'   => $this->char_id_2,
            'unread'    => 1,
            'type'      => 0
        ];
    }

    function tearDown() {
        TestAccountCreateAndDestroy::destroy();
    }

    public function testMessageCanInstantiate() {
        $mess = new Message();
        $this->assertTrue($mess instanceof Message);
    }

    public function testMessageCanBeSent() {
        $mess = Message::create($this->messageData);
        $this->assertEquals($this->messageData['message'], $mess->message);
    }

    public function testMessageCanBeReceived() {
        $mess = Message::create($this->messageData);
        $first_message = Message::find($mess->id());
        $this->assertEquals($this->messageData['message'], $first_message->message);
    }

    public function testMessageCanBeSentToGroup() {
        $this->messageData['type'] = 1;

        $mess = Message::create($this->messageData);
        $messages = Message::findByReceiver(new Player($this->char_id_2), 1);
        $first_message = $messages->first();

        $this->assertTrue($mess instanceof Message);
        $this->assertGreaterThan(0, count($messages), 'Message array should have some elements');
        $this->assertTrue($first_message instanceof Message);
        $this->assertEquals($this->messageData['message'], $first_message->message);
    }

    public function testMessageHasARobustSender() {
        Message::create($this->messageData);
        $messages = Message::findByReceiver(new Player($this->char_id_2), 0, 1000, 0);
        $first_message = $messages->first();

        $this->assertGreaterThan(0, count($messages), 'Collection has no results found');
        $this->assertTrue($first_message instanceof Message, 'First message not a valid message model');
        $this->assertNotEmpty($first_message->sender);
        $this->assertGreaterThan(0, strlen($first_message->sender));
    }

    public function testCreateMessageViaMassAssignment() {
        $this->messageData['send_to']   = $this->char_id;
        $this->messageData['send_from'] = $this->char_id_2;

        $mess = Message::create($this->messageData);

        $text = 'Updated phpunit test message';
        $mess->message = $text;
        $mess->save(); // Save the newly updated message

        $retrieved_message = Message::find($mess->id());
        $retrieved_text = $retrieved_message->message;
        $retrieved_message->delete();
        $this->assertEquals($text, $retrieved_text);
    }

    public function testFindPrivateMessagesForACertainChar() {
        $messageCount = 4;

        $this->messageData['send_to']   = $this->char_id;
        $this->messageData['send_from'] = $this->char_id_2;

        for ($count = 0; $count < $messageCount; $count++) {
            $this->messageData['message'] = 'Random phpunit test message'.$count;
            Message::create($this->messageData);
        }

        $char = new Player($this->char_id);

        $messages = Message::findByReceiver($char)->all();
        $this->assertEquals($messageCount, count($messages));

        Message::deleteByReceiver($char, 0);
        $this->assertEquals(0, Message::countByReceiver($char));
    }
}
