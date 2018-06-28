<?php

use yii\db\Migration;

class m180628_144113_create_table_visitor_agent extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%visitor_agent}}', [
            'user_agent' => $this->string()->notNull()->append('PRIMARY KEY'),
            'name' => $this->string(),
            'info' => $this->json(),
        ], $tableOptions);

        $this->createIndex('va_ua_vl_fkey', '{{%visitor_agent}}', 'user_agent');
    }

    public function down()
    {
        $this->dropTable('{{%visitor_agent}}');
    }
}
