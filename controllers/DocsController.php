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
                        'roles' => $this->module->allowedRoles,
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
        $viewPath = \Yii::getAlias($this->module->docsPath);

        // show simple global index page
        if (!$page || $page == 'index')
        {
            $dirsLists = \yii\helpers\FileHelper::findDirectories($viewPath,['recursive'=>false]);
            $links = [];
            $content = [];
            foreach ($dirsLists as $value) {
                $view_id = str_ireplace($viewPath, '', $value);
                $view_id = str_ireplace(DIRECTORY_SEPARATOR, '/', $view_id);
                $view_id = trim($view_id, DIRECTORY_SEPARATOR);
                $view_id = str_replace(['.php', '.md', '.txt'], ['','',''], $view_id);
                $view_id = ltrim($view_id, '/');
                if ($view_id) {
                    $fileLists = \yii\helpers\FileHelper::findFiles($value,['only'=>[
                        '*.md',
                        '*.txt'
                    ],
                    ]);
                    if ($fileLists) {
                        $content[] = '## [' . mb_ucfirst($view_id).']('.urldecode(Yii::$app->urlManager->createAbsoluteUrl(['/docs/docs/index', 'page' => $view_id], 1)).')';
                        $links = [];
                        foreach ($fileLists as $fileName) {
                            $view_id2 = str_ireplace($viewPath, '', $fileName);
                            $view_id2 = str_ireplace(DIRECTORY_SEPARATOR, '/', $view_id2);
                            $view_id2 = trim($view_id2, DIRECTORY_SEPARATOR);
                            $view_id2 = str_replace(['.php', '.md', '.txt'], ['','',''], $view_id2);
                            $view_id2 = ltrim($view_id2, '/');
                            $resolveTitle = str_ireplace($view_id, '', $view_id2);
                            $resolveTitle = ltrim($resolveTitle, '/');

                            $link = '['.$resolveTitle.']('.urldecode(Yii::$app->urlManager->createAbsoluteUrl(['/docs/docs/index', 'page' => $view_id2], 1)).')';
                            $links[$resolveTitle] = $link;
                        }
                        if ($links) {
                            sort($links);
                            $content[] = '* '.implode("\n\n* ", $links);
                            $content[] = '';
                        }
                    }
                }

            }
            $tpl = Yii::t('app', 'Index') . "\n\n" . implode("\n\n", $content);
            $content = Markdown::process($tpl, 'extra');
            return $this->render('page', [
                'content' => $content,
            ]);
        }

        if (!$page) {
            $dirsLists = \yii\helpers\FileHelper::findDirectories($viewPath,['only'=>[
                '*.md',
                '*.txt'
            ]]);
            foreach ($dirsLists as $value) {
                $view_id = str_ireplace($viewPath, '', $value);
                $view_id = str_ireplace(DIRECTORY_SEPARATOR, '/', $view_id);
                $view_id = trim($view_id, DIRECTORY_SEPARATOR);
                $view_id = str_replace(['.php', '.md', '.txt'], ['','',''], $view_id);
                $view_id = ltrim($view_id, '/');
                if ($view_id && $view_id == $page) {
                    $fileLists = \yii\helpers\FileHelper::findFiles($value,['only'=>[
                        '*.md',
                        '*.txt'
                    ]]);
                    $links = [];
                    foreach ($fileLists as $fileName) {
                        $view_id2 = str_ireplace($viewPath, '', $fileName);
                        $view_id2 = str_ireplace(DIRECTORY_SEPARATOR, '/', $view_id2);
                        $view_id2 = trim($view_id2, DIRECTORY_SEPARATOR);
                        $view_id2 = str_replace(['.php', '.md', '.txt'], ['','',''], $view_id2);
                        $view_id2 = ltrim($view_id2, '/');
                        $resolveTitle = str_ireplace($view_id, '', $view_id2);
                        $resolveTitle = ltrim($resolveTitle, '/');
                        $links[] = '['.$resolveTitle.']('.urldecode(Yii::$app->urlManager->createAbsoluteUrl(['/docs/docs/index', 'page' => $view_id2], 1)).')';
                    }
                    $tpl = Yii::t('app', 'Index') . "\n\n* " . implode("\n* ", $links);
                    $content = Markdown::process($tpl, 'extra');
                    return $this->render('page', [
                        'content' => $content,
                    ]);


                }
            }
        } else {
            $page = trim($page, '/');
            $fileLists = \yii\helpers\FileHelper::findFiles($viewPath,['only'=>[
                '*.md',
                '*.txt'
            ]]);
        }
        if (!$fileLists) {
            throw new NotFoundHttpException();
        }
        foreach ($fileLists as $value) {
            $view_id = str_ireplace($viewPath, '', $value);
            $view_id = str_ireplace(DIRECTORY_SEPARATOR, '/', $view_id);
            $view_id = trim($view_id, DIRECTORY_SEPARATOR);
            $view_id = str_replace(['.php', '.md', '.txt'], ['','',''], $view_id);
            $view_id = ltrim($view_id, '/');
            if ($view_id && $view_id == $page) {
                $file_get_contents = file_get_contents($value);
                $content = Markdown::process($file_get_contents, 'extra');
                return $this->render('page', [
                    'content' => $content,
                ]);
            }
        }
        // no page found? try show documents index:
        $dirsLists = \yii\helpers\FileHelper::findDirectories($viewPath,['only'=>[
            '*.md',
            '*.txt'
        ]]);
        foreach ($dirsLists as $value) {
            $view_id = str_ireplace($viewPath, '', $value);
            $view_id = str_ireplace(DIRECTORY_SEPARATOR, '/', $view_id);
            $view_id = trim($view_id, DIRECTORY_SEPARATOR);
            $view_id = str_replace(['.php', '.md', '.txt'], ['','',''], $view_id);
            $view_id = ltrim($view_id, '/');
            if ($view_id && $view_id == $page) {
                $fileLists = \yii\helpers\FileHelper::findFiles($value,['only'=>[
                    '*.md',
                    '*.txt'
                ]]);
                $links = [];
                foreach ($fileLists as $fileName) {
                    $view_id2 = str_ireplace($viewPath, '', $fileName);
                    $view_id2 = str_ireplace(DIRECTORY_SEPARATOR, '/', $view_id2);
                    $view_id2 = trim($view_id2, DIRECTORY_SEPARATOR);
                    $view_id2 = str_replace(['.php', '.md', '.txt'], ['','',''], $view_id2);
                    $view_id2 = ltrim($view_id2, '/');
                    $resolveTitle = str_ireplace($view_id, '', $view_id2);
                    $resolveTitle = ltrim($resolveTitle, '/');
                    $links[] = '['.$resolveTitle.']('.urldecode(Yii::$app->urlManager->createAbsoluteUrl(['/docs/docs/index', 'page' => $view_id2], 1)).')';
                }
                $tpl = Yii::t('app', 'Index') . "\n\n* " . implode("\n* ", $links);
                $content = Markdown::process($tpl, 'extra');
                return $this->render('page', [
                    'content' => $content,
                ]);


            }
        }
        throw new NotFoundHttpException();
    }
}
