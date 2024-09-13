<?php

namespace app\controllers;

use Yii;
use app\models\Customer;
use kaabar\jwt\JwtHttpBearerAuth;
use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;

class CustomerController extends ActiveController
{
    public $modelClass = 'app\models\Customer';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => JwtHttpBearerAuth::class,
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);

        return $actions;
    }

    public function actionIndex()
    {
        $queryParams = Yii::$app->request->queryParams;

        $query = Customer::find();

        if (!empty($queryParams['name'])) {
            $query->andFilterWhere(['like', 'name', $queryParams['name']]);
        }
        if (!empty($queryParams['cpf'])) {
            $query->andFilterWhere(['cpf' => $queryParams['cpf']]);
        }

        $sort = isset($queryParams['sort']) ? $queryParams['sort'] : 'id'; 
        $sortOrder = SORT_ASC;
        if (strpos($sort, '-') === 0) {
            $sort = ltrim($sort, '-');
            $sortOrder = SORT_DESC;
        }
        $query->orderBy([$sort => $sortOrder]);

        $pageSize = isset($queryParams['limit']) ? (int)$queryParams['limit'] : 20;
        $page = isset($queryParams['offset']) ? (int)($queryParams['offset'] / $pageSize) : 0;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
                'page' => $page,
            ],
        ]);

        return $dataProvider->getModels();
    }
}