<?php

namespace app\controllers;

use Throwable;
use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use app\models\PromoCode;
use app\models\User;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="Books API", version="1.0.0")
 * @OA\Server(url="http://localhost:8000")
 */
class DefaultController extends ActiveController
{
    
}