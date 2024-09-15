<?php

namespace app\controllers;

use Yii;
use app\models\Book;
use kaabar\jwt\JwtHttpBearerAuth;
use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;

class BookController extends ActiveController
{
    public $modelClass = 'app\models\Book';

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

        $query = Book::find();

        if (!empty($queryParams['title'])) {
            $query->andFilterWhere(['like', 'title', $queryParams['title']]);
        }
        if (!empty($queryParams['author'])) {
            $query->andFilterWhere(['author' => $queryParams['author']]);
        }
        if (!empty($queryParams['isbn'])) {
            $query->andFilterWhere(['isbn' => $queryParams['isbn']]);
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