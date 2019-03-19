<?php
/**
 * Link         :   http://www.phpcorner.net
 * User         :   qingbing<780042175@qq.com>
 * Date         :   2018-11-26
 * Version      :   1.0
 */
require("../vendor/autoload.php");

// 目录分隔符
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
// APP应用的客户端编码
defined('APP_CHARSET') or define('APP_CHARSET', 'utf-8');
// php是否调试模式
defined('APP_DEBUG') or define('APP_DEBUG', true);
// 定义环境变量
defined("PHP_ENV") or define("PHP_ENV", "dev");
// 定义配置存放目录
defined("CONFIG_PATH") or define("CONFIG_PATH", dirname(realpath(".")) . "/conf");
// 定义配置缓存的存放目录
defined("RUNTIME_PATH") or define("RUNTIME_PATH", dirname(realpath(".")) . "/runtime");
// 定义全局的视图目录
defined("VIEW_PATH") or define("VIEW_PATH", dirname(realpath(".")) . "/test/views");

// 执行应用命令
PF::createApplication('\Render\Application')->run();