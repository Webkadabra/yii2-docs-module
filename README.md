# yii2-docs-module

A simple documentation viewer for your `*.MD` files.

### Installation via Composer

You can install package with a command:

> composer require webkadabra/yii2-docs-module

### Example usage with Yii2 Advanced App

Assume the following structure of your applicaiton's folders:

```
/
  backend
  common
  docs
    /user
      /orders
        /fulfillment.md
  frontend  
```

Where `fulfillment.md` is your user documentaiton for, well, looks like some e-commerce thing. 
Anyways, add this module to your `backend` config first:

```
// ...
'modules' => [
        // ...
        'docs' => [
            'class' => 'webkadabra\yii\modules\docs\Module',  
        ],
        // ...
],
// ...
```

Advanced configuration, with breadcrumbs and shit can be achieved like this (simple example):

```
// ...
'modules' => [
        // ...
        'docs' => [
            'class' => 'webkadabra\yii\modules\docs\Module',
            'layout' => '/docs',
            'on beforeAction'=>function($event) {
                /** @var $event yii\base\ActionEvent */
                Yii::$app->view->params['breadcrumbs'][] = ['label' => Yii::t('app', ucfirst($event->sender->id)), 'url' => ['/docs/index']];
                Yii::$app->view->params['breadcrumbs'][] = ucwords(str_replace(['/', '_',], [' / ', ' '], Yii::$app->request->getQueryParam('page')));
            }
        ],
        // ...
],
// ...
```
Also, looks like this should be in too, for `backend` (or any yii2 app):

```
// ...
'components' => [
  // ...
    'urlManager' => [
      'rules' => [
        'docs/<page:[\w\d\/\-]+>' => 'docs/docs/index',
      ],
    ],
  ],
```

Now you have your documentaiton available at `http://backend/docs/user/orders/fulfillment`.

Thanks, pull requests are welcome!

- Sergii
