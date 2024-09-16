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
        $refreshToken = UserRefreshToken::find()
            ->where(['urf_token' => (string)$token])
            ->andWhere(['>', 'urf_expires_at', time()]) 
            ->one();
    
        if (!$refreshToken) {
            return null;  
        }
    
        return \app\models\User::find()
            ->where(['id' => $refreshToken->urf_userID])
            ->one();
    }

    public function afterSave($isInsert, $changedOldAttributes) {
		if (array_key_exists('usr_password', $changedOldAttributes)) {
			\app\models\UserRefreshToken::deleteAll(['urf_userID' => $this->userID]);
		}

		return parent::afterSave($isInsert, $changedOldAttributes);
	}

    /**
     * @param $username
     * @return User|null
     */
    public static function findByUsername($username): ?User
    {
        return static::findOne(['username' => $username]);
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
