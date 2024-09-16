<?php

namespace app\controllers;

use Yii;
use app\models\Book;
use kaabar\jwt\JwtHttpBearerAuth;
use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;



class BookController extends ActiveController
{
    /**
     * @OA\Schema(
     *     schema="Book",
     *     type="object",
     *     required={"isbn", "title", "author", "price", "stock"},
     *     @OA\Property(property="isbn", type="string", description="ISBN do livro", example="978-3-16-148410-0"),
     *     @OA\Property(property="title", type="string", description="Título do livro", example="Exemplo de Livro"),
     *     @OA\Property(property="author", type="string", description="Autor do livro", example="Autor Exemplo"),
     *     @OA\Property(property="price", type="number", format="float", description="Preço do livro", example=29.99),
     *     @OA\Property(property="stock", type="integer", description="Quantidade em estoque", example=100),
     *     @OA\Property(property="image", type="string", description="URL da imagem do livro", example="https://example.com/livro.jpg"),
     *     @OA\Property(property="created_at", type="integer", description="Data de criação (timestamp)", example=1609459200),
     *     @OA\Property(property="updated_at", type="integer", description="Data de atualização (timestamp)", example=1609459200)
     * )
     */
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
    /**
     * @OA\Get(
     *     path="/books",
     *     summary="Lista de livros",
     *     description="Retorna uma lista de livros com suporte a filtros por título, autor e ISBN, além de ordenação e paginação.",
     *     tags={"Books"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="title",
     *         in="query",
     *         description="Filtrar livros pelo título",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="author",
     *         in="query",
     *         description="Filtrar livros pelo autor",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="isbn",
     *         in="query",
     *         description="Filtrar livros pelo ISBN",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Ordenar os resultados por campo (ex: id, title, -id para ordem decrescente)",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             example="id"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Limite de resultados por página",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example=20
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="offset",
     *         in="query",
     *         description="Offset dos resultados (para paginação)",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example=0
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de livros",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Book")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/books",
     *     summary="Criar um novo livro",
     *     description="Adiciona um novo livro ao banco de dados.",
     *     tags={"Books"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"isbn", "title", "author", "price", "stock"},
     *                 @OA\Property(property="isbn", type="string", description="ISBN do livro", example="978-3-16-148410-0"),
     *                 @OA\Property(property="title", type="string", description="Título do livro", example="Exemplo de Livro"),
     *                 @OA\Property(property="author", type="string", description="Autor do livro", example="Autor Exemplo"),
     *                 @OA\Property(property="price", type="number", format="float", description="Preço do livro", example=29.99),
     *                 @OA\Property(property="stock", type="integer", description="Quantidade em estoque", example=100),
     *                 @OA\Property(property="image", type="string", format="binary", description="Imagem do livro (arquivo de imagem)")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Livro criado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Book")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Dados inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erro ao validar os dados.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     )
     * )
     */
    public function actionCreate()
    {
        
    }
}