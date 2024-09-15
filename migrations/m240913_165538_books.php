<?php

use yii\db\Migration;

/**
 * Class m240913_165538_books
 */
class m240913_165538_books extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%books}}', [
            'id' => $this->primaryKey(),
            'isbn' => $this->string()->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'author' => $this->string()->notNull(),
            'price' => $this->decimal(10, 2)->notNull(),
            'stock' => $this->integer()->notNull(),
            'image' => $this->string()->null(),
            'created_at' => $this->integer()->notNull()->defaultValue(time()),
            'updated_at' => $this->integer()->notNull()->defaultValue(time()),
        ]);

        $this->createIndex(
            'idx-books-isbn',
            '{{%books}}',
            'isbn',
            true // unique index
        );

        // Create index for created_at and updated_at
        $this->createIndex(
            'idx-books-created_at',
            '{{%books}}',
            'created_at'
        );

        $this->createIndex(
            'idx-books-updated_at',
            '{{%books}}',
            'updated_at'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropIndex('idx-books-isbn', '{{%books}}');
        $this->dropIndex('idx-books-created_at', '{{%books}}');
        $this->dropIndex('idx-books-updated_at', '{{%books}}');

        $this->dropTable('{{%books}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240913_165538_books cannot be reverted.\n";

        return false;
    }
    */
}
