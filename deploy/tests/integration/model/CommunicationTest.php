<?php

// Note that the file has to have a file ending of ...test.php to be run by phpunit

use NinjaWars\core\data\Communication;
use NinjaWars\core\data\Player;

class CommunicationTest extends \NWTest
{
    private $char_id;
    private $char_id_2;
    private $messageData;
    private $message_id;

    public function setUp(): void
    {
        parent::setUp();
        $this->char_id = TestAccountCreateAndDestroy::char_id(true);
        $this->char_id_2 = TestAccountCreateAndDestroy::char_id_2(true);

        $this->messageData = [
            'message'   => 'Random phpunit Test Message',
            'send_from' => $this->char_id,
            'send_to'   => $this->char_id_2,
            'unread'    => 1,
            'type'      => 0
        ];

        $this->message_id = null;
    }

    public function tearDown(): void
    {
        TestAccountCreateAndDestroy::destroy();
        if ($this->message_id !== null) {
            query('delete from messages where message_id = :id', [':id' => $this->message_id]);
        }
        parent::tearDown();
    }

    public function testMessageCanBeSent()
    {
        $mess = Communication::createMessage($this->messageData);
        $this->assertTrue($mess);
    }

    public function testMessageCanBeSentToGroup()
    {
        $this->messageData['type'] = 1;
        $target_id_list = [$this->char_id, $this->char_id_2];
        $worked = Communication::sendToGroup(Player::find($this->char_id), $target_id_list, 'Random phpunit Test Message', 1);
        $this->assertTrue($worked);
    }

    public function testMessageHasARobustSender()
    {
        $mess = Communication::createMessage($this->messageData);
        $messages = Communication::getMessages($this->char_id_2, 1000, 0, 0);
        $first_message = reset($messages);

        $this->assertGreaterThan(0, count($messages), 'Collection has no results found');
        $this->assertNotEmpty($first_message['sender']);
        $this->assertGreaterThan(0, strlen($first_message['sender']));
    }
}
