<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use app\models\User;

class CreateUserController extends Controller
{
    public $login;
    public $name;
    public $password;

    public function options($actionID)
    {
        return ['login', 'name' , 'password'];
    }

    public function actionIndex()
    {
        if (empty($this->login) || empty($this->name) | empty($this->password)) {
            $this->stdout("Por favor, forneça um login e senha.\n");
            return ExitCode::USAGE;
        }

        if (User::findByLogin($this->login)) {
            $this->stdout("Usuário com o login '{$this->login}' já existe.\n");
            return ExitCode::DATAERR;
        }

        $user = new User();
        $user->login = $this->login;
        $user->name = $this->name;
        $user->password_hash = Yii::$app->security->generatePasswordHash($this->password);

        if ($user->save()) {
            $this->stdout("Usuário '{$this->login}' criado com sucesso.\n");
            return ExitCode::OK;
        } else {
            $this->stdout("Falha ao criar o usuário.\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
}
