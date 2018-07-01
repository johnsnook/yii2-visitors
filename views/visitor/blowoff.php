<?php
/**
 * This file is part of the Yii2 extension module, yii2-ip-filter
 *
 * @author John Snook
 * @date 2018-06-28
 * @license https://github.com/johnsnook/yii2-ip-filter/LICENSE
 * @copyright 2018 John Snook Consulting
 */
/* @var $this yii\web\View */
/* @var $visitor johnsnook\ipFilter\models\Visitor */
$visitor = \Yii::$app->getModule('ipFilter')->visitor;

$name = (!empty($visitor->name) ? $visitor->name : 'Visitor');
if (!empty($visitor->message)) {
    $message = $visitor->message;
} else {
    if ($visitor->proxy === 'no') {
        $message = 'An adminstrator has banned your IP address.';
    } else {
        $message = "Your IP address has been automatically banned for using a proxy of type $visitor->proxy.";
    }
}

$this->title = 'Don\'t go away mad, just go away.';
?>

<div class="visitor-log-view">
    <div class="jumbotron">
        <h1>Dear <?= $name ?>,</h1>
        <p><?= $message ?></p>
        <p>If you feel this is in error, you can contact an <a href="mailto:<?= \Yii::$app->params['adminEmail'] ?>">administrator</a> </p>
    </div>>

</div>
