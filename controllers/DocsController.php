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
 *
 * @property \webkadabra\yii\modules\docs\Module $module
 *
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
    public function actionIndex($page=null, $language = null)
    {
        $sourceViewPath = \Yii::getAlias($this->module->docsPath);
        $viewPath = $this->module->getDocsPath(\Yii::getAlias($this->module->docsPath));

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
                if ($this->module->translationsFolder && $view_id == $this->module->translationsFolder) {
                    continue;
                }
                if ($view_id) {
                    $fileLists = \yii\helpers\FileHelper::findFiles($value,['only'=>[
                        '*.md',
                        '*.txt'
                    ],
                    ]);
                    if ($fileLists) {
                        $content[] = '## [' . mb_ucfirst($view_id).']('.urldecode(\yii\helpers\Url::toRoute(['index', 'page' => $view_id], 1)).')';
                        $links = [];
                        foreach ($fileLists as $fileName) {
                            $view_id2 = str_ireplace($viewPath, '', $fileName);
                            $view_id2 = str_ireplace(DIRECTORY_SEPARATOR, '/', $view_id2);
                            $view_id2 = trim($view_id2, DIRECTORY_SEPARATOR);
                            $view_id2 = str_replace(['.php', '.md', '.txt'], ['','',''], $view_id2);
                            $view_id2 = ltrim($view_id2, '/');
                            $resolveTitle = str_ireplace($view_id, '', $view_id2);
                            $resolveTitle = ltrim($resolveTitle, '/');

                            $link = '['.$resolveTitle.']('.urldecode(\yii\helpers\Url::toRoute(['index', 'page' => $view_id2], 1)).')';
                            $links[$resolveTitle] = $link;
                        }
                        if ($links) {
                            sort($links);
                            $content[] = '* '.implode("\n* ", $links);
                            $content[] = '';
                        }
                    }
                }

            }
            $tpl = Yii::t('app', 'Index') . "\n\n" . implode("\n\n", $content);
            $content = Markdown::process($tpl, 'extra');
            return $this->render('page', [
                'content' => $content,
                'availableTranslations' => $availableTranslations ?? [],
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
                        $links[] = '['.$resolveTitle.']('.urldecode(\yii\helpers\Url::toRoute(['index', 'page' => $view_id2], 1)).')';
                    }
                    $tpl = Yii::t('app', 'Index') . "\n\n* " . implode("\n* ", $links);
                    $content = Markdown::process($tpl, 'extra');
                    return $this->render('page', [
                        'content' => $content,
                        'availableTranslations' => $availableTranslations ?? [],
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

            if ($this->module->translationsFolder && $view_id == $this->module->translationsFolder) {
                continue;
            }

            // show single page:
            if ($view_id && $view_id == $page) {
                $file_get_contents = file_get_contents($value);
                $content = Markdown::process($file_get_contents, 'extra');

                $availableTranslations = [];

                // check for available translations
                if ($this->module->translationsFolder) {
                    foreach ($this->module->languages as $languageKey => $language)
                    {
                        $view_idFull = str_ireplace($viewPath, '', $value);
                        if ($languageKey == $this->module->getLanguage()) {
                            continue;
                        }
                        if ($languageKey == $this->module->sourceLanguage ) {
                            $translatedViewId = $viewPath . DIRECTORY_SEPARATOR . $view_idFull;
                        } else {
                            $translatedViewId = $viewPath . DIRECTORY_SEPARATOR . $this->module->translationsFolder
                                . DIRECTORY_SEPARATOR . $languageKey . DIRECTORY_SEPARATOR  . $view_idFull;
                        }
                        if ($languageKey == $this->module->defaultLanguage) {
                            $url = yii\helpers\Url::toRoute(['index', 'page' => $page]);
                        } else {
                            $url = yii\helpers\Url::toRoute(['index', 'page' => $page, 'language' => $languageKey]);
                        }
                        if (file_exists($translatedViewId)) {
                            $availableTranslations[$language] = rawurldecode($url); // must use `rawurldecode` for correct forward slashes in page URLs
                        }
                    }
                }
                return $this->render('page', [
                    'content' => $content,
                    'availableTranslations' => $availableTranslations,
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
                    $links[] = '['.$resolveTitle.']('.urldecode(\yii\helpers\Url::toRoute(['index', 'page' => $view_id2], 1)).')';
                }
                $tpl = Yii::t('app', 'Index') . "\n\n* " . implode("\n* ", $links);
                $content = Markdown::process($tpl, 'extra');
                return $this->render('page', [
                    'content' => $content,
                    'availableTranslations' => $availableTranslations ?? [],
                ]);


            }
        }
        throw new NotFoundHttpException();
    }
}
