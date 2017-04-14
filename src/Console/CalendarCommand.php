<?php
namespace Hrgruri\Bot\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CalendarCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('tweet:calendar')
            ->setDescription('Tweet Ritumei calendar')
            ->addOption(
                'today',
                null,
                InputOption::VALUE_NONE,
                'Tweet today`s calendar'
            )
            ->addOption(
                'tomorrow',
                null,
                InputOption::VALUE_NONE,
                'Tweet tomorrow`s calendar'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            if ($input->getOption('today')) {
                $date = date('Y-m-d');
                $when = "今日";
            } elseif ($input->getOption('tomorrow')) {
                $date = date('Y-m-d', strtotime("+ 1 day"));
                $when = "明日";
            } else {
                throw new \Exception("UNDEFINED OPTION");
            }
            $this->tweetEvent(
                $this->getEvents('master', $date),
                "院生",
                $when
            );
            $this->tweetEvent(
                $this->getEvents('bachelor', $date),
                "学部生",
                $when
            );
        } catch (\Exception $e) {
            $this->notification($e->getFile(). ':'. $e->getLine());
        }
    }

    private function tweetEvent(array $events, String $target, String $when)
    {
        foreach ($events as $event) {
            $this->tweet($this->appendInfo(
                "{$target}の皆様へ. {$when}は｢{$event->title}｣です."
            ));
        }
    }

    /**
     *
     * イベントを配列で取得する
     *
     * @param  string $type master|bachelor
     * @param  string $date yyyy-mm-dd
     * @return array
     */
    private function getEvents(string $type, string $date) : array
    {
        $client = new \GuzzleHttp\Client();
        $result = [];
        try {
            $res = $client->request('GET', "https://ritsucal.hrgruri.com/api/{$type}/search", [
                'query' => ['date' => $date]
            ]);
            if ($res->getStatusCode() !== 200) {
                throw new \Exception("status is not 200");
            }
            $data = json_decode($res->getBody());
            $result = $data->events;
        } catch (\Exception $e) {
            self::$log->critical($e->getMessage());
        }
        return  $result;
    }

    /**
     *
     * 現在の時刻とURLを追加する
     *
     * @param  string $text
     * @return string
     */
    private function appendInfo(string $text) : string
    {
        return $text. ' ('.date("Y-m-d H:i").') http://www.ritsumei.ac.jp/profile/info/calender/';
    }
}
