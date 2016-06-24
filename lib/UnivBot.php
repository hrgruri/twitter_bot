<?php
namespace hrgruri\bot;

use Abraham\TwitterOAuth\TwitterOAuth;

class UnivBot
{
    private $config;
    private $file;
    private static $conn;

    public function __construct()
    {
        self::$conn   = null;
        $this->config = \hrgruri\bot\Config::getInstance();
        $this->file   = __DIR__.'/../data/calendar.json';
        if (!file_exists($this->file)) {
            $client = new Hrgruri\Ritsucal\Client();
            $calenders = $client->getCalenders();
            file_put_contents(
                $this->file,
                json_encode($calenders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );
        }
    }

    public function tweetTodayEvent()
    {
        $date = [
            'year'  =>  (int)date('Y'),
            'month' =>  (int)date('m'),
            'day'   =>  (int)date('d')
        ];
        $events = $this->getBachelorEvents($date['year'], $date['month'], $date['day']);
        foreach ($events as $event) {
            $this->tweet(
                "学部生の皆様へ. 今日は｢{$event->title}｣です."
            );
        }
        $events = $this->getMasterEvents($date['year'], $date['month'], $date['day']);
        foreach ($events as $event) {
            $this->tweet(
                "院生の皆様へ. 今日は｢{$event->title}｣です."
            );
        }
    }

    public function tweetTomorrowEvent()
    {
        $date = [
            'year'  =>  (int)date('Y'),
            'month' =>  (int)date('m'),
            'day'   =>  (int)date('d')+1
        ];
        $events = $this->getBachelorEvents($date['year'], $date['month'], $date['day']);
        foreach ($events as $event) {
            $this->tweet(
                "学部生の皆様へ. 明日は｢{$event->title}｣です."
            );
        }
        $events = $this->getMasterEvents($date['year'], $date['month'], $date['day']);
        foreach ($events as $event) {
            $this->tweet(
                "院生の皆様へ. 明日は｢{$event->title}｣です."
            );
        }
    }

    private function getBachelorEvents($year, $month, $day)
    {
        $events = [];
        $calendars = json_decode(file_get_contents($this->file));
        foreach ($calendars as $calendar) {
            if (preg_match('/大学\s/u', $calendar->title) == 1) {
                $events = $this->getEvents($calendar, $year, $month, $day);
                break;
            }
        }
        return $events;
    }

    private function getMasterEvents($year, $month, $day)
    {
        $events = [];
        $calendars = json_decode(file_get_contents($this->file));
        foreach ($calendars as $calendar) {
            if (preg_match('/情報理工学研究科/u', $calendar->title) == 1) {
                $events = $this->getEvents($calendar, $year, $month, $day);
                break;
            }
        }
        return $events;
    }

    private function getEvents($calendar, $year, $month, $day)
    {
        $events = [];
        foreach ($calendar->events as $event) {
            if (
                $event->year        === $year
                && $event->month    === $month
                && $event->day      === $day
            ) {
                $events[] = $event;
            }
        }
        return $events;
    }

    private function tweet($text)
    {
        if (!isset(self::$conn)) {
            $key = $this->config->get('twitter');
            self::$conn = new TwitterOAuth(
                $key->consumer_key       ?? '',
                $key->consumer_secret    ?? '',
                $key->oauth_token        ?? '',
                $key->oauth_token_secret ?? ''
            );
        }
        $text .= ' ('.date("Y-m-d H:i").')';
        self::$conn->post("statuses/update", array("status" => $text));
        // print "tweet:\n{$text}\n\n";
    }
}
