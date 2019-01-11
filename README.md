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

Advanced configuration example (e.g. add breadcrumbs):

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

Add custom rule in `urlManager` component:

```
// ...
'components' => [
  // ...
    'urlManager' => [
      'rules' => [
        'docs/<page:[\w\d\/_-]*>' => 'docs/docs/index',
      ],
    ],
  ],
```

Now you have your documentaiton available at `http://backend.website.test/docs/user/orders/fulfillment`, and going up a tree structure would bring index of documents in that directory, e.g.: `http://backend.website.test/docs/user/` will bring list of documents in 'docs/user` directory (recursively). 

# TODO

* Add multilingual support

TODO:

* [] Multilanguage support
* [] Admin/collaborator UI to edit pages

Thanks, pull requests are welcome!

- Sergii
