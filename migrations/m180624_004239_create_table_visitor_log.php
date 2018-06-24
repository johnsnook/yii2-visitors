<?php

use yii\db\Migration;

class m180624_004239_create_table_visitor_log extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%visitor_log}}', [
            'id' => $this->primaryKey(),
            'ip' => $this->string()->notNull(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
            'request' => $this->string()->notNull(),
            'referer' => $this->string(),
            'user_agent' => $this->string(),
        ], $tableOptions);

        $this->createIndex('visits_ip_idx', '{{%visitor_log}}', 'ip');
        $this->createIndex('visits_timestamp_idx', '{{%visitor_log}}', 'created_at');
        $this->addForeignKey('visits_visitor_fkey', '{{%visitor_log}}', 'ip', '{{%visitor}}', 'ip', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%visitor_log}}');
    }
}
