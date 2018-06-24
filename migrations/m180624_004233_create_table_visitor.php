<?php

use yii\db\Migration;

class m180624_004233_create_table_visitor extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%visitor}}', [
            'ip' => $this->string()->notNull()->append('PRIMARY KEY'),
            'access_type' => $this->string()->notNull()->defaultValue('None'),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
            'user_id' => $this->integer(),
            'name' => $this->string(),
            'message' => $this->text(),
            'info' => $this->json(),
        ], $tableOptions);

        $this->createIndex('visitor_ip_idx', '{{%visitor}}', 'ip', true);
    }

    public function down()
    {
        $this->dropTable('{{%visitor}}');
    }
}
