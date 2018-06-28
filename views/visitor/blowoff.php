<?php
//use yii\helpers\Html;
//use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $visitor johnsnook\ipFilter\models\Visitor */
$name = ($visitor->name ? $visitor->name : 'Stranger');

$this->title = 'Do\'t go away mad, just go away.';
?>

<div class="visitor-log-view">


    <div class="jumbotron">
        <h1>Dear <?= $name ?>,</h1>
        <p><?= $visitor->message ?></p>
    </div>>

</div>
