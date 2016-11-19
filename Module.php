<?php
/**
 * Copyright (C) 2015-present Sergii Gamaiunov <hello@webkadabra.com>
 * All rights reserved.
 */
namespace webkadabra\yii\modules\docs;

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
    public $controllerNamespace = 'backend\modules\affiliates\controllers';
}
