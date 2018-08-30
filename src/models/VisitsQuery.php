<?php

namespace johnsnook\visitors\models;

/**
 * This is the ActiveQuery class for [[Visits]].
 *
 * @see Visits
 */
class VisitsQuery extends \yii\db\ActiveQuery {
    /* public function active()
      {
      return $this->andWhere('[[status]]=1');
      } */

    /**
     * {@inheritdoc}
     * @return Visits[]|array
     */
    public function all($db = null) {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Visits|array|null
     */
    public function one($db = null) {
        return parent::one($db);
    }

}
