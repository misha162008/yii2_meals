<?php

use yii\db\Migration;

class m161003_103407_meals extends Migration
{
    public function up()
    {
         $this->createTable('meals', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'category' => $this->string(),
            'body' => $this->text(),
            'image' => $this->string(),
            'publish_date' => $this->timestamp() . ' NOT NULL',
        ]);
    }

    public function down()
    {
        $this->dropTable('meals');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
