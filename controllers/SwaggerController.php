<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

class SwaggerController extends Controller
{
    public function actions()
    {
        return [
            // Ação para visualizar a documentação Swagger (ex.: /swagger/doc)
            'doc' => [
                'class' => 'light\swagger\SwaggerAction',
                // URL para o JSON gerado da API (ajuste conforme necessário)
                'restUrl' => \yii\helpers\Url::to(['/swagger/api'], true),
            ],
            // Ação para gerar o JSON da API (ex.: /swagger/api)
            'api' => [
                'class' => 'light\swagger\SwaggerApiAction',
                // Diretórios onde os controladores e modelos serão escaneados para gerar a documentação
                'scanDir' => [
                    Yii::getAlias('@app/controllers'),
                    Yii::getAlias('@app/models'),
                ],
                // Chave de segurança para acessar o JSON da API (se precisar)
                'api_key' => 'balbalbal', // Pode ser deixado em branco ou configurado conforme necessário
            ],
        ];
    }
}
