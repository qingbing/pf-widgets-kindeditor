<?php

use Helper\FileManager;
use Components\AssetsManager;

/**
 * Link         :   http://www.phpcorner.net
 * User         :   qingbing<780042175@qq.com>
 * Date         :   2019-01-10
 * Version      :   1.0
 */
class KindEditor
{
    // 编辑器显示类型
    const MODE_MINI = 'mini';
    const MODE_SIMPLE = 'simple';
    const MODE_FULL = 'full';

    /**
     * 获取上传的根目录
     * @return string
     */
    public static function getUploadFolder()
    {
        static $_baseUploadPath;
        if (null !== $_baseUploadPath) {
            return $_baseUploadPath;
        }
        $uploadFolder = \PF::app()->getParam('kdUploadFolder');
        if (null === $uploadFolder) {
            $uploadFolder = 'kindeditor';
        } else {
            $uploadFolder = trim($uploadFolder, '/');
        }
        return $_baseUploadPath = dirname($_SERVER['SCRIPT_FILENAME']) . DS . $uploadFolder;
    }

    /**
     * 获取上传的根目录对应的baseUrl
     * @return string
     */
    public static function getUploadBaseUrl()
    {
        static $_baseUrl;
        if (null !== $_baseUrl) {
            return $_baseUrl;
        }
        $uploadFolder = \PF::app()->getParam('kdUploadFolder');
        if (null === $uploadFolder) {
            $uploadFolder = 'kindeditor';
        }
        return $_baseUrl = \PF::app()->getRequest()->getBaseUrl() . '/' . $uploadFolder;
    }

    /**
     * 删除由编辑器创建的上传文件夹
     * @param string $folder
     * @param string $xFlag
     * @return bool
     */
    public static function removeEditor($folder, $xFlag)
    {
        if (empty($xFlag)) {
            return true;
        }
        $path = self::getUploadFolder() . "/{$folder}/{$xFlag}";
        if (!file_exists($path)) {
            return true;
        }
        return FileManager::rmdir($path, true);
    }

    /**
     * 代码美化功能
     * @throws \Exception
     */
    public static function codePrettify()
    {
        $src = dirname(__FILE__) . '/source/kindeditor';
        $baseUri = AssetsManager::getInstance('assets-manager')->publish($src, 'kindeditor');
        \ClientScript::getInstance()->registerCssFile("{$baseUri}/plugins/code/prettify.css");
        \ClientScript::getInstance()->registerScriptFile("{$baseUri}/plugins/code/prettify.js");
        \ClientScript::getInstance()->registerScript('', "window.onload = function () {prettyPrint();}");
    }
}