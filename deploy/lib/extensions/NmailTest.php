<?php

require_once 'vendor/autoload.php';
require_once __DIR__ . '/Nmail.class.php';


class NmailTest extends NWTest
{

    public function testInstatiateNmail()
    {
        $nmail = new Nmail();
        $this->assertTrue($nmail instanceof Nmail);
    }

    public function testProvideMockClientFail()
    {
        $transport = new class
        {
            public function putEvents($params)
            {
                return false;
            }
        };

        $nmail = new Nmail(null, null, null, null, null, $transport);
        $outcome = $nmail->send();
        $this->assertFalse($outcome);
    }

    public function testProvideMockClientSuccess()
    {
        $transport = new class
        {
            public function putEvents($params)
            {
                return (object) [
                    'FailedEntryCount' => 0,
                    'Entries' => [
                        [
                            'EventId' => '1234',
                            'ErrorCode' => null,
                            'ErrorMessage' => null,
                        ]
                    ]
                ];
            }
        };

        $nmail = new Nmail('someone@example.com', 'Some subject', 'some body text', 'someoneelse@example.com', null, $transport);
        $outcome = $nmail->send($debug_override = true);
        $this->assertTrue($outcome);
    }

    public function testProvideArrayToFromAndMockClientSuccess()
    {
        $transport = new class
        {
            public function putEvents($params)
            {
                return (object) [
                    'FailedEntryCount' => 0,
                    'Entries' => [
                        [
                            'EventId' => '1234',
                            'ErrorCode' => null,
                            'ErrorMessage' => null,
                        ]
                    ]
                ];
            }
        };

        $nmail = new Nmail(['Jim' => 'someone@example.com'], 'Some subject', 'some body text', ['William B. Washington' => 'someoneelse@example.com'], null, $transport);
        $outcome = $nmail->send($debug_override = true);
        $this->assertTrue($outcome);
    }

    public function testValidationFails()
    {
        $transport = new class
        {
            public function putEvents($params)
            {
                return (object) [
                    'FailedEntryCount' => 0,
                    'Entries' => [
                        [
                            'EventId' => '1234',
                            'ErrorCode' => null,
                            'ErrorMessage' => null,
                        ]
                    ]
                ];
            }
        };

        $nmail = new Nmail(null, 'Some subject', 'some body text', 'someoneelse@example.com', null, $transport);
        $outcome = $nmail->send();
        $this->assertFalse($outcome);
    }
}
