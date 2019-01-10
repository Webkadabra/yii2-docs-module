<?php
/**
 * Copyright (C) 2015-present Sergii Gamaiunov <hello@webkadabra.com>
 * All rights reserved.
 */
namespace webkadabra\yii\modules\docs;

use Yii;

/**
 * Documentation Viewer module
 * @package webkadabra\yii\modules\docs
 * @author sergii gamaiunov <hello@webkadabra.com>
 */
class Module extends \yii\base\Module
{
    public $defaultRoute = ['docs'];

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'webkadabra\yii\modules\docs\controllers';

    public $docsPath = '@app/../docs';

    public $allowedRoles = ['admin'];

    /**
     * @var string|false
     */
    public $translationsFolder = 'translations';

    /**
     * @var string main language of documentation pages in `Module::$docsPath` path
     */
    public $sourceLanguage = 'en';

    /**
     * @var string default language may be differen from the source based on client needs
     */
    public $defaultLanguage = 'en';

    /**
     * @var array available languages, with `key` being a name of folder in `$translationsFolder` for translation
     *
     * For example, in your config add:
     * ```
     * 'docs' => [
     *      ...
     *      'languages' => [
     *          'ru' => 'Russian',
     *      ],
     *      ...
     * ]
     * ```
     */
    public $languages = [];

    public $languageParam = 'language';

    /**
     * @return array|mixed|string current language code
     */
    public function getLanguage() {
        $userLanguage = Yii::$app->request->get($this->languageParam, $this->defaultLanguage);
        return (isset($this->languages[$userLanguage]) ? $userLanguage : $this->defaultLanguage);
    }

    /**
     * @return array|mixed|string current language code
     */
    public function getDocsPath($path) {
        return $this->getLanguage() == $this->sourceLanguage
            ? $path
            : $path . DIRECTORY_SEPARATOR . $this->translationsFolder . DIRECTORY_SEPARATOR . $this->getLanguage();
    }

    public function init()
    {
        parent::init();
        if (!isset($this->languages[$this->sourceLanguage])) {
            $this->languages[$this->sourceLanguage] = $this->sourceLanguage;
        }
    }
}
