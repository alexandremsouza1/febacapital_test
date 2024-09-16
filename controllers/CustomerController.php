<?php

namespace app\controllers;

use Yii;
use app\models\Customer;
use kaabar\jwt\JwtHttpBearerAuth;
use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;

class CustomerController extends ActiveController
{
    /**
     * @OA\Schema(
     *     schema="Customer",
     *     type="object",
     *     required={"name", "cpf", "cep", "street", "number", "city", "state", "gender"},
     *     @OA\Property(property="id", type="integer", description="ID do cliente", example=1),
     *     @OA\Property(property="name", type="string", description="Nome do cliente", example="João da Silva"),
     *     @OA\Property(property="cpf", type="string", description="CPF do cliente", example="123.456.789-00"),
     *     @OA\Property(property="cep", type="string", description="CEP do cliente", example="12345-678"),
     *     @OA\Property(property="street", type="string", description="Rua do cliente", example="Rua das Flores"),
     *     @OA\Property(property="number", type="string", description="Número da residência", example="123"),
     *     @OA\Property(property="city", type="string", description="Cidade do cliente", example="São Paulo"),
     *     @OA\Property(property="state", type="string", description="Estado do cliente", example="SP"),
     *     @OA\Property(property="gender", type="string", description="Gênero do cliente", example="M"),
     *     @OA\Property(property="complement", type="string", description="Complemento do endereço", example="Apartamento 45"),
     *     @OA\Property(property="created_at", type="integer", description="Timestamp de criação", example=1632960000),
     *     @OA\Property(property="updated_at", type="integer", description="Timestamp de atualização", example=1632960000)
     * )
     */
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
    /**
     * @OA\Get(
     *     path="/customers",
     *     summary="Obter a lista de clientes",
     *     description="Retorna uma lista de clientes, com filtros opcionais por nome e CPF.",
     *     tags={"Customers"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Filtra clientes pelo nome",
     *         required=false,
     *         @OA\Schema(type="string", example="João")
     *     ),
     *     @OA\Parameter(
     *         name="cpf",
     *         in="query",
     *         description="Filtra clientes pelo CPF",
     *         required=false,
     *         @OA\Schema(type="string", example="12345678900")
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Ordena o resultado por um campo, exemplo: '-id' para ordenar decrescentemente",
     *         required=false,
     *         @OA\Schema(type="string", example="-id")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Número de registros por página",
     *         required=false,
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *     @OA\Parameter(
     *         name="offset",
     *         in="query",
     *         description="Deslocamento para paginação (baseado no limite)",
     *         required=false,
     *         @OA\Schema(type="integer", example=0)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de clientes obtida com sucesso",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Customer")
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
    /**
     * @OA\Post(
     *     path="/customers",
     *     summary="Criar um novo cliente",
     *     description="Adiciona um novo cliente ao banco de dados.",
     *     tags={"Customers"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name", "cpf", "cep", "street", "number", "city", "state", "gender"},
     *                 @OA\Property(property="name", type="string", description="Nome do cliente", example="João da Silva"),
     *                 @OA\Property(property="cpf", type="string", description="CPF do cliente", example="123.456.789-00"),
     *                 @OA\Property(property="cep", type="string", description="CEP do cliente", example="12345-678"),
     *                 @OA\Property(property="street", type="string", description="Rua do cliente", example="Rua das Flores"),
     *                 @OA\Property(property="number", type="string", description="Número da residência", example="123"),
     *                 @OA\Property(property="city", type="string", description="Cidade do cliente", example="São Paulo"),
     *                 @OA\Property(property="state", type="string", description="Estado do cliente", example="SP"),
     *                 @OA\Property(property="gender", type="string", description="Gênero do cliente", example="M"),
     *                 @OA\Property(property="complement", type="string", description="Complemento do endereço", example="Apartamento 45")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Cliente criado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Customer")
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