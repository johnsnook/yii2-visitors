<?php

/**
 * @author John Snook
 * @date Jun 7, 2018
 * @license https://snooky.biz/site/license
 * @copyright 2018 John Snook Consulting
 * Description of parser
 */
#$parser->setFormat('%h %l %u %t "%r" %>s %b "%{Referer}i" "%{User-Agent}i" %I %O');

use common\models\Access;

$dir = '/var/www/yii2/access_logs/';
$files = scandir($dir);
foreach ($files as $file) {
    echo $file . '<br>';
    parseFile($dir . $file);
}

function parseFile($file) {
#    $requests = [];
    $parser = new \Kassner\LogParser\LogParser();
    $parser->setFormat('%h %l %u %t "%r" %>s %b "%{Referer}i" "%{User-Agent}i"');

    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        try {
            $entry = $parser->parse($line);
            if (strstr($entry->request, 'GET /assets') || strstr($entry->request, 'GET /css') || strstr($entry->request, 'GET /favicon.png')) {
                continue;
            }
            //$requests[$entry->host][] = $entry;
            $request = split(' ', $entry->request)[1];

            $access = new Access([
                'ip_address' => $entry->host,
                'request' => $request,
                'referer' => $entry->HeaderReferer !== '-' ? $entry->HeaderReferer : null,
                'user_agent' => $entry->HeaderUserAgent,
//                'created_at' => DateTime::
            ]);
            $dt = new DateTime;
            $access->created_at = $dt->setTimestamp($entry->stamp);
            $access->save();
//        $requests[$entry->host][] = [
//            'time' => $entry->time,
//            'request' => $entry->request,
//        ];
        } catch (\Exception $e) {
            echo $e->getMessage();
            continue;
        }
    }
}
?>
<pre>
    <?php # json_encode($requests, 224)  ?>
</pre>