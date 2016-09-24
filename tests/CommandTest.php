<?php
use Hrgruri\Bot\Console\CalendarCommand;

class CommandTest extends TestCase
{
    public function testTweet()
    {
        $faker      = $this->faker();
        $instance   = new CalendarCommand();
        while(true) {
            if (strlen($text = $faker->realText(200)) > 140) {
                break;
            }
        }
        $this->assertFalse($this->callMethod($instance, "tweet", [$text]));
    }
}
