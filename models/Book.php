<?php

namespace app\models;

use yii\httpclient\Client;

/**
 * This is the model class for table "books".
 *
 * @property int $id
 * @property string $isbn
 * @property string $title
 * @property string $author
 * @property float $price
 * @property int $stock
 * @property string|null $image
 * @property int $created_at
 * @property int $updated_at
 */
class Book extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'books';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['isbn', 'title', 'author', 'price', 'stock'], 'required'],
            [['price'], 'number'],
            [['stock', 'created_at', 'updated_at'], 'integer'],
            [['isbn', 'title', 'author', 'image'], 'string', 'max' => 255],
            [['isbn'], 'unique'],
            [['isbn'], 'validateIsbn'],
        ];
    }


    public function validateIsbn($attribute, $params)
    {
        $isbn = $this->$attribute;
    
        try {
            $client = new Client();
            $response = $client->createRequest()
                ->setMethod('GET')
                ->setUrl("https://brasilapi.com.br/api/isbn/v1/{$isbn}")
                ->send();
    
            if ($response->isOk) {
                $data = $response->data;
    
                if (empty($data['isbn'])) {
                    $this->addError($attribute, 'The ISBN provided is invalid.');
                }
            } else {
                $this->addError($attribute, 'The ISBN provided is invalid.');
            }
        } catch (\Exception $e) {
            $this->addError($attribute, 'Error validating ISBN. Please try again later.');
        }
    }
}
