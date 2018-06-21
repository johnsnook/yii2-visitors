<?php

use yii\db\Migration;

class m180621_015503_create_table_visitor extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName !== 'pgsql') {
            die("If you're not using using postgresql, I can't help you.  Maybe god can't even help you.  Sorry, not sorry.");
        }


        $sql = "CREATE TABLE public.visitor (
    ip_address cidr NOT NULL,
    access_type access_list_type NOT NULL DEFAULT 'None'::access_list_type,
    created_at timestamp without time zone NOT NULL DEFAULT now(),
    updated_at timestamp without time zone NOT NULL DEFAULT now(),
    user_id integer,
    name character varying COLLATE pg_catalog.\"default\",
    message text COLLATE pg_catalog.\"default\",
    ip_info json,
    access_log json,
    proxy_check json,
    CONSTRAINT guestlist_pkey PRIMARY KEY (ip_address)) WITH (OIDS = FALSE) TABLESPACE pg_default";
        $this->execute($sql);
        $sql = "ALTER TABLE public.visitor OWNER to apache";
        $this->execute($sql);
        $sql = "CREATE UNIQUE INDEX guest_ip_idx ON public.visitor USING btree";
        $sql .= "(ip_address cidr_ops) TABLESPACE pg_default";
        $this->execute($sql);
//        if ($this->db->driverName === 'mysql') {
//            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
//        }
//        $this->createTable('{{%visitor}}', [
//            'ip_address' => $this->string()->notNull()->append('PRIMARY KEY'),
//            'access_type' => $this->string()->notNull()->defaultValue('None'),
//            'created_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
//            'updated_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
//            'user_id' => $this->integer(),
//            'name' => $this->string(),
//            'message' => $this->text(),
//            'ip_info' => $this->json(),
//            'access_log' => $this->json(),
//            'proxy_check' => $this->json(),
//                ], $tableOptions);
//
//        $this->createIndex('guest_ip_idx', '{{%visitor}}', 'ip_address', true);
    }

    public function down() {
        $this->dropTable('{{%visitor}}');
    }

}
