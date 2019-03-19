<?php
/**
 * Link         :   http://www.phpcorner.net
 * User         :   qingbing<780042175@qq.com>
 * Date         :   2018-12-05
 * Version      :   1.0
 */
return [
    'params' => [
        'kdUploadFolder' => 'upload',
    ],// 用户自定义配置
    'preLoad' => [],
//    'layout' => '//layouts/html',
    'modules' => [
        'pf' => [],
        'admin' => [
//            'layout' => '/layouts/main',/
            'modules' => [
                'good' => [
//                    'layout' => '/layouts/main',
                ],
            ],
        ],
    ],
    'components' => \Helper\CMap::mergeArray(
        [
            'database' => [
                'class' => '\Components\Db',
                'c-file' => 'database',
                'c-group' => 'master',
            ],
            'urlManager' => [
                'class' => '\Components\UrlManager',
                'c-file' => 'url-manager',
            ],
            'cache' => [
                'class' => '\Components\FileCache',
                'c-file' => 'file-cache',
            ],
        ],
        require('component-errorHandler.php')
    ),
];