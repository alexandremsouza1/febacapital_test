<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    public static function tableName()
    {
        return 'user';
    }

    public function rules()
    {
        return [
            [['login', 'password_hash'], 'required'],
            ['login', 'unique'],
            ['password_hash', 'string'],
        ];
    }

    public static function findByLogin($login)
    {
        return static::findOne(['login' => $login]);
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        // Primeiro, procure o refresh token na tabela user_refresh_tokens
        $refreshToken = UserRefreshToken::find()
            ->where(['urf_token' => (string)$token])
            ->andWhere(['>', 'urf_expires_at', time()])  // Verifica se o token não expirou
            ->one();
    
        if (!$refreshToken) {
            return null;  // Token não encontrado ou expirado
        }
    
        // Encontre o usuário associado ao refresh token
        return \app\models\User::find()
            ->where(['id' => $refreshToken->urf_userID])
            ->one();
    }

    public function afterSave($isInsert, $changedOldAttributes) {
		// Purge the user tokens when the password is changed
		if (array_key_exists('usr_password', $changedOldAttributes)) {
			\app\models\UserRefreshToken::deleteAll(['urf_userID' => $this->userID]);
		}

		return parent::afterSave($isInsert, $changedOldAttributes);
	}

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return '';
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
}
