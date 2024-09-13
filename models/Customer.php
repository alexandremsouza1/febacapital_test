<?php

namespace app\models;

use app\services\CpfValidator;
use Yii;

/**
 * This is the model class for table "customer".
 *
 * @property int $id
 * @property string $name
 * @property string $cpf CPF
 * @property string $cep ZIP code
 * @property string $street Street
 * @property string $number Number
 * @property string $city City
 * @property string $state State
 * @property string|null $complement Complement
 * @property string $gender
 * @property int $created_at Creation timestamp
 * @property int $updated_at Last update timestamp
 */
class Customer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customer';
    }


    /**
     * {@inheritdoc}
     */
   public function rules()
    {
        return [
            [['name', 'cpf', 'cep', 'street', 'number', 'city', 'state', 'gender'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['name', 'street', 'city', 'complement'], 'string', 'max' => 255],
            [['cpf'], 'string', 'max' => 14],
            [['cep', 'number'], 'string', 'max' => 10],
            [['state'], 'string', 'max' => 2],
            [['gender'], 'string', 'max' => 1],
            [['cpf'], 'unique'],
            [['cpf'], 'validateCpf'],
        ];
    }


    public function validateCpf($attribute, $params)
    {
        if (!CpfValidator::isCpfValid($this->$attribute)) {
            $this->addError($attribute, 'The CPF is invalid.');
        }
    }

    public function validateCep($attribute, $params)
    {
        $brasilApi = Yii::$app->brasilApi;
        $cepData = $brasilApi->cep()->search($this->$attribute);

        if (!$cepData || !isset($cepData['cep'])) {
            $this->addError($attribute, 'The CEP is invalid or does not exist.');
        }
    }
}
