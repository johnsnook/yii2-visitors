<?php

use yii\db\Migration;

class m180630_142222_create_table_visitor_service_error extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%visitor_service_error}}', [
            'id' => $this->primaryKey(),
            'service' => $this->string()->notNull(),
            'url' => $this->string()->notNull(),
            'params' => $this->json(),
            'message' => $this->text()->notNull(),
            'is_resolved' => $this->boolean()->notNull()->defaultValue(false),
                ], $tableOptions);
    }

    public function down() {
        $this->dropTable('{{%visitor_service_error}}');
    }

}
