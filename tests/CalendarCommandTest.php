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

    public function testTweet()
    {
        $faker      = $this->faker();
        $instance   = new CalendarCommand();
        $text = "test tweet ". date("Y-m-d H:i:s");
        $this->assertTrue($this->callMethod($instance, "tweet", [$text]));
        while(true) {
            if (strlen($text = $faker->realText(200)) > 140) {
                break;
            }
        }
        $this->assertFalse($this->callMethod($instance, "tweet", [$text]));
    }
}
