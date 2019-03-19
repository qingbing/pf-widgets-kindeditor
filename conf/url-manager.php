<?php
/**
 * Link         :   http://www.phpcorner.net
 * User         :   qingbing<780042175@qq.com>
 * Date         :   2018-12-06
 * Version      :   1.0
 */
return [
    'routeVar' => "r", // get 模式下路由的标志
    'urlSuffix' => ".shtml", // path 模式下链接的后缀名
    'showScriptName' => true, // path 创建URL访问链接时是否显示脚本名
    'appendParams' => true, // 参数是否增加在 pathInfo 后
    'caseSensitive' => "false", // 链接的地址是否区分大小写
    'urlFormat' => "path", // 路由的显示模式，get和path两种
    'cacheInstanceString' => "\Components\FileCache::getInstance('file-cache');", // cache 实例的实例化字符串代码，设置为空表示不缓存规则
    'throwNotMatchRule' => false, // 当无匹配的url规则时是否抛出异常 todo 很重要的一个配置
    'rules' => [
        // 首页路由
        [
            'pattern' => '',
            'route' => 'site/index',
        ],
        // 二级路由
        [
            'pattern' => '<controller:\w+>/list/*',
            'route' => '<controller>/index',
        ], [
            'pattern' => '<controller:\w+>/<action:(edit|delete)>/<id:\d+>',
            'route' => '<controller>/<action>',
        ], [
            'pattern' => '<controller:\w+>/<id:\d+>',
            'route' => '<controller>/view',
        ],
        // 三级路由
        [
            'pattern' => '<module:\w+>/<controller:\w+>/list/*',
            'route' => '<module>/<controller>/index',
        ], [
            'pattern' => '<module:\w+>/<controller:\w+>/<action:(edit|delete)>/<id:\d+>',
            'route' => '<module>/<controller>/<action>',
        ], [
            'pattern' => '<module:\w+>/<controller:\w+>/<id:\d+>',
            'route' => '<module>/<controller>/view',
        ],
    ],
];