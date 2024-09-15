<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\User;
use kaabar\jwt\JwtHttpBearerAuth;

class AuthController extends Controller
{
    public function behaviors() {
        $behaviors = parent::behaviors();
    
        $behaviors['authenticator'] = [
            'class' => \kaabar\jwt\JwtHttpBearerAuth::class,
            'except' => [
                'login',
                'options',
            ],
        ];
    
        return $behaviors;
    }

    public function actionLogin()
    {
        $request = Yii::$app->request;
        $login = $request->post('login');
        $password = $request->post('password');
    
        if (empty($login) || empty($password)) {
            return $this->asJson([
                'success' => false,
                'message' => 'Login e senha são obrigatórios.',
            ]);
        }
    
        $user = User::findOne(['login' => $login]);
    
        if (!$user || !$user->validatePassword($password)) {
            return $this->asJson([
                'success' => false,
                'message' => 'Login ou senha inválidos.',
            ]);
        }
    
        // Gerar o token JWT
        $token = $this->generateJwt($user);

        return $this->asJson([
            'success' => true,
            'token' => $token, 
        ]);
    }

    private function generateJwt(\app\models\User $user) {
        $jwt = Yii::$app->jwt;
        $signer = $jwt->getSigner('HS256');
        $key = $jwt->getKey();
    
        $now = new \DateTimeImmutable();
    
        $jwtParams = Yii::$app->params['jwt'];
    
        $token = $jwt->getBuilder()
            ->issuedBy($jwtParams['issuer'])  
            ->permittedFor($jwtParams['audience']) 
            ->identifiedBy($jwtParams['id'], true)
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now->modify($jwtParams['request_time']))
            ->expiresAt($now->modify($jwtParams['expire']))
            ->withClaim('uid', $user->id)
            ->getToken($signer, $key);
    
        $token = $token->toString();
        $this->generateRefreshToken($user,$token);

        return $token;
    }

    private function generateRefreshToken(\app\models\User $user, string $token)
    {
        $expiresAt = (new \DateTimeImmutable())->modify('+1 hour')->getTimestamp();
        $userRefreshToken = new \app\models\UserRefreshToken([
            'urf_userID' => $user->id,
            'urf_token' => $token,
            'urf_ip' => Yii::$app->request->userIP,
            'urf_user_agent' => Yii::$app->request->userAgent,
            'urf_expires_at' => $expiresAt, 
            'urf_created' => gmdate('Y-m-d H:i:s'),
        ]);
        if (!$userRefreshToken->save()) {
            throw new \yii\web\ServerErrorHttpException('Failed to save the refresh token: '. $userRefreshToken->getErrorSummary(true));
        }
    }
}
