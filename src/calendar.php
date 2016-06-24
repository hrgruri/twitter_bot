<?php
require __DIR__.'/../vendor/autoload.php';
$bot = new hrgruri\bot\UnivBot();
if (($argv[1] ?? '') === 'today') {
    $bot->tweetTodayEvent();
} elseif (($argv[1] ?? '') === 'tomorrow') {
    $bot->tweetTomorrowEvent();
}
