<?php
//use yii\helpers\Html;
//use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $visitor johnsnook\ipFilter\models\Visitor */
$name = (!empty($visitor->name) ? $visitor->name : 'Stranger');
$message = (!empty($visitor->message) ? $visitor->message : "What's this you've said to me, my good friend? Ill have you know I graduated top of my class in conflict resolution, and Ive been involved in numerous friendly discussions, and I have over 300 confirmed friends. I am trained in polite discussions and I'm the top mediator in the entire neighborhood. You are worth more to me than just another target. I hope we will come to have a friendship never before seen on this Earth. Don't you think you might be hurting someone's feelings saying that over the internet? Think about it, my friend. As we speak I am contacting my good friends across the USA and your P.O. box is being traced right now so you better prepare for the greeting cards, friend. The greeting cards that help you with your hate. You should look forward to it, friend. I can be anywhere, anytime for you, and I can calm you in over seven hundred ways, and that's just with my chess set. Not only am I extensively trained in conflict resolution, but I have access to the entire group of my friends and I will use them to their full extent to start our new friendship. If only you could have known what kindness and love your little comment was about to bring you, maybe you would have reached out sooner. But you couldn't, you didn't, and now we get to start a new friendship, you unique person. I will give you gifts and you might have a hard time keeping up. You're finally living, friend.");

$this->title = 'Do\'t go away mad, just go away.';
?>

<div class="visitor-log-view">


    <div class="jumbotron">
        <h1>Dear <?= $name ?>,</h1>
        <p><?= $message ?></p>
    </div>>

</div>
