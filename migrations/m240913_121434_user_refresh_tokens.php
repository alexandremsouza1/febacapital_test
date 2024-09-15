<?php

use yii\db\Migration;

/**
 * Class m240913_121434_user_refresh_tokens
 */
class m240913_121434_user_refresh_tokens extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_refresh_tokens}}', [
            'user_refresh_tokenID' => $this->primaryKey(),
            'urf_userID' => $this->integer()->notNull(),
            'urf_token' => $this->string(1000)->notNull(),
            'urf_ip' => $this->string(50)->notNull(),
            'urf_user_agent' => $this->string(1000)->notNull(),
            'urf_created' => $this->dateTime()->notNull()->comment('UTC'),
            'urf_expires_at' => $this->integer()->notNull()->comment('Timestamp de expiração'),
        ]);

        // Adiciona a chave estrangeira
        $this->addForeignKey(
            'fk-user_refresh_tokens-user',
            '{{%user_refresh_tokens}}',
            'urf_userID',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Adiciona um comentário à tabela
        $this->addCommentOnTable('{{%user_refresh_tokens}}', 'For JWT authentication process');
    }

    public function safeDown()
    {
        // Remove a chave estrangeira
        $this->dropForeignKey('fk-user_refresh_tokens-user', '{{%user_refresh_tokens}}');
        
        $this->dropTable('{{%user_refresh_tokens}}');
    }
}
