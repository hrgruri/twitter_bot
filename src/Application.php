<?php
namespace Hrgruri\Bot;

class Application extends \Symfony\Component\Console\Application
{
    public function __construct()
    {
        parent::__construct();
        $this->add(new \Hrgruri\Bot\Console\CalendarCommand);
    }
}
