<?php
use Hrgruri\Bot\Console\CalendarCommand;

class CalendarCommandTest extends TestCase
{
    public function testAppendInfo()
    {
        $text = "";
        $instance = new CalendarCommand();
        $result = $this->callMethod($instance, "appendInfo", [$text]);
        $this->assertTrue(mb_strlen($text) < mb_strlen($result));
    }

    public function testGetEvents()
    {
        $instance   = new CalendarCommand();
        $result = $this->callMethod($instance, 'getEvents', ['bachelor', '2016-4-1']);
        $this->assertInternalType('array', $result);
    }
}
