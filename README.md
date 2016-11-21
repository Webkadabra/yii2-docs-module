# yii2-docs-module

A simple documentation viewer for your `*.MD` files.

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

Also, looks like this should be in too, for `backend` (or any yii2 app):

```
// ...
'components' => [
  // ...
    'urlManager' => [
      'rules' => [
        'docs/<page:[\w\d\/]+>' => 'docs/docs/index',
      ],
    ],
  ],
```

Now you have your documentaiton available at `http://backend/docs/user/orders/fulfillment`.

Thanks, pull requests are welcome!

- Sergii
