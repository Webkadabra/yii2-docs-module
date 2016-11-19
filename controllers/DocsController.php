<?php
/**
 * Copyright (C) 2015-present Sergii Gamaiunov <hello@webkadabra.com>
 * All rights reserved.
 */

namespace webkadabra\yii\modules\docs\controllers;

use yii;
use yii\helpers\Markdown;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use dektrium\user\filters\AccessRule;

/**
 * Class DocsController
 * @package webkadabra\yii\modules\docs\controllers
 * @author sergii gamaiunov <hello@webkadabra.com>
 */
class DocsController  extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $rules = [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];
        if (class_exists('AccessRule')) {
            $rules['access']['ruleConfig'] = [
                'class' => AccessRule::className(),
            ];
        }
        return $rules;
    }

    /**
     * Lists all Customer models.
     * @return mixed
     */
    public function actionIndex($page=null)
    {
        if (!$page) {
            throw new NotFoundHttpException();
        }
        $page = trim($page, '/');
        $viewPath = \Yii::getAlias('@app/../docs');
        $fileLists = \yii\helpers\FileHelper::findFiles($viewPath,['only'=>[
            '*.md',
            '*.txt'
        ]]);
        foreach ($fileLists as $value) {
            $view_id = str_ireplace($viewPath, '', $value);
            $view_id = str_ireplace(DIRECTORY_SEPARATOR, '/', $view_id);
            $view_id = trim($view_id, DIRECTORY_SEPARATOR);
            $view_id = str_replace(['.php', '.md', '.txt'], ['','',''], $view_id);
            $view_id = ltrim($view_id, '/');
            if ($view_id && $view_id == $page) {
                // For each file we are trying to read first comments block for template configuration
                $file_get_contents = file_get_contents($value);
                $content = Markdown::process($file_get_contents, 'extra');
                return $this->render('page', [
                    'content' => $content,
                ]);
            }
        }
        throw new NotFoundHttpException();
    }
}
