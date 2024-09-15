<?php

use yii\db\Migration;

/**
 * Class m240913_155736_customers
 */
class m240913_155736_customers extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%customer}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'cpf' => $this->string(14)->notNull()->unique()->comment('CPF'),
            'cep' => $this->string(10)->notNull()->comment('ZIP code'),
            'street' => $this->string()->notNull()->comment('Street'),
            'number' => $this->string(10)->notNull()->comment('Number'),
            'city' => $this->string()->notNull()->comment('City'),
            'state' => $this->string(2)->notNull()->comment('State'),
            'complement' => $this->string()->comment('Complement'),
            'gender' => $this->char(1)->notNull()->check("gender IN ('M', 'F')"),
            'created_at' => $this->integer()->notNull()->defaultValue(time())->comment('Creation timestamp'),
            'updated_at' => $this->integer()->notNull()->defaultValue(time())->comment('Last update timestamp'),
        ]);

        $this->createIndex(
            'idx-customer-cpf',
            '{{%customer}}',
            'cpf'
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%customer}}');
    }
}
