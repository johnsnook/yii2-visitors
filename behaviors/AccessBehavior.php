<?php

/**
 * @author John Snook
 * @date Jun 20, 2018
 * @license https://snooky.biz/site/license
 * @copyright 2018 John Snook Consulting
 * Description of AccessBehavior
 */

namespace frontend\behaviors;

use common\models\Access;
use common\components\RemoteAddress;
use common\models\Blacklist;
use common\models\Whitelist;
use common\models\Ipinfo;
use yii\base\Behavior;
use yii\web\Controller;
use yii\base\ActionEvent;

class AccessBehavior extends Behavior {

    public $userInfo;

    public function events() {
        return [
            Controller::EVENT_BEFORE_ACTION => 'validateAccess'
        ];
    }

    public function validateAccess(ActionEvent $event) {
        $ip = Access::log();
        if (!Whitelist::isCleared($ip)) {
            if (Blacklist::isBanned($ip) && $event->action->id != 'fuckoff') {
                $event->result = $this->owner->redirect(['site/fuckoff']);
                $event->isValid = FALSE;
            }
        }
        $this->userInfo = Ipinfo::findOne($ip);
    }

}
