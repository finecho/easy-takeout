<h1 align="center"> finecho/waimai </h1>

<p align="center"> 外卖平台 SDK</p>


## 安装

```shell
$ composer require finecho/waimai -vvv
```

## 介绍

现阶段将会支持两家外卖平台

* [饿了吗](https://open.shop.ele.me/openapi/documents/workflow)
* [美团](https://developer.waimai.meituan.com/home/guide/6)

## 配置
```php
$config = [
    'default_platform' => 'meituan',
    'platforms' => [
        'meituan' => [
            'app_id' => 10020201024, 
            'secret_id' => 'AKIDsiQzQla780mQxLLU2GJCxxxxxxxxxxx', 
        ],
        'eleme' => [
            'app_id' => 20020201024, 
            'secret_id' => 'CKIDsiQzQla780mQxLLU2GJCxxxxxxxxxxx', 
        ],
    ],
];
```

## 使用

您可以使用两种调用方式：原始方式调用 和 链式调用，请根据你的喜好自行选择使用方式，效果一致。

### 方式一 - 原始方式调用
```php
use EasyWaimai\Application;

$app = new Application($config);

$api = $app->getClient();

$response = $api->post(
    '/poi/save',
    [
        'name'    => 'finecho 的快餐店',
        'address' => '深圳市南山区',
    ]
);
```

### 方式二 - 链式调用
```php
use EasyWaimai\Application;

$app = new Application($config);

$api = $app->getClient();

$response = $api->poi->save->post(
    [
        'name'    => 'finecho 的快餐店',
        'address' => '深圳市南山区',
    ]
);
```

当一个项目中同时使用到了多个平台，可以使用 `setPlatform` 进行灵活切换
```php
...
$app->setPlatform(\EasyMeiTuan\Platforms\Eleme::PLATFORM_NAME);
....
```

## License

MIT
