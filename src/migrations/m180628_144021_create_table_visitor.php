<?php

use yii\db\Migration;

class m180628_144021_create_table_visitor extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%visitor}}', [
            'ip' => $this->string()->notNull()->append('PRIMARY KEY'),
            'banned' => $this->boolean()->notNull()->defaultValue(false),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
            'user_id' => $this->integer(),
            'name' => $this->string(),
            'message' => $this->text(),
            'visits' => $this->integer()->notNull()->defaultValue('0'),
            'city' => $this->string(),
            'region' => $this->string(),
            'country' => $this->string(),
            'latitude' => $this->double(),
            'longitude' => $this->double(),
            'organization' => $this->string(),
            'proxy' => $this->string(),
                ], $tableOptions);

        $this->createIndex('visitor_ip_idx', '{{%visitor}}', 'ip', true);
    }

    public function down() {
        $this->dropTable('{{%visitor}}');
    }

}
