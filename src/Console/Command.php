<?php
namespace Hrgruri\Bot\Console;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

abstract class Command extends \Symfony\Component\Console\Command\Command
{
    protected static $log;
    protected static $conn;
    protected static $config;

    public function __construct()
    {
        parent::__construct();
        self::$config   = \Hrgruri\Bot\Config::getInstance();
        self::$log      = new Logger('bot_log');
        self::$log->pushHandler(new StreamHandler(__DIR__.'/../../data/bot.log', Logger::DEBUG));
    }
    /**
     *
     * Twitterに投稿
     * @param  string $text
     * @return boolean
     */
    protected function tweet(string $text)
    {
        if (!isset(self::$conn)) {
            $key = self::$config->get('twitter');
            self::$conn = new \Abraham\TwitterOAuth\TwitterOAuth(
                $key->consumer_key       ?? '',
                $key->consumer_secret    ?? '',
                $key->oauth_token        ?? '',
                $key->oauth_token_secret ?? ''
            );
        }
        if (mb_strlen($text) <= 140) {
            try {
                $res = self::$conn->post('statuses/update', ['status' => $text]);
                if (count($res->errors ?? []) > 0) {
                    throw new \Exception('tweet error');
                }
                self::$log->info('tweeted', ['text' => $text]);
                $result = true;
            } catch (\Exception $e) {
                self::$log->error($e->getMessage(), [
                    'msg'       => $e->getMessage(),
                    'text'      => $text,
                    'errors'    => json_decode(json_encode($res->errors), 1)
                ]);
                $result = false;
            }
        } else {
            self::$log->debug('over 140 characters', ['text' => $text]);
            $result = false;
        }
        return $result;
    }

    /**
     *
     * Slackに通知
     * @param  string $text
     */
    protected function notification(string $text)
    {

    }
}
