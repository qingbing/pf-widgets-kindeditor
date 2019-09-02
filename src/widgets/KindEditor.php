<?php
/**
 * Link         :   http://www.phpcorner.net
 * User         :   qingbing<780042175@qq.com>
 * Date         :   2019-01-09
 * Version      :   1.0
 */

namespace Widgets;


use Abstracts\Model;
use Abstracts\OutputProcessor;
use Components\AssetsManager;
use Helper\Coding;
use Html;

class KindEditor extends OutputProcessor
{
    /* @var \Abstracts\Model 编辑器所在模型 */
    public $model;
    /* @var string 当前编辑器显示类型 */
    public $mode = \KindEditor::MODE_MINI;
    /* @var string 编辑器上传文件夹 */
    public $folder = 'common';
    /* @var string 编辑器ID */
    public $id;
    /* @var array 放在编辑器上的属性 */
    public $htmlOptions = [];
    /* @var string 编辑器提交字段 */
    public $contentField = 'content';
    /* @var string 编辑器默认内容 */
    public $content;
    /* @var boolean 编辑器唯一标志字段 */
    public $openFlag = true;
    /* @var string 编辑器唯一标志字段 */
    public $kFlagField = 'x_flag';
    /* @var string 编辑器唯一标志 */
    public $kFlag;
    /* @var boolean 是否开放源码按钮 */
    public $openSource = false;
    /* @var string 上传图片时，支持添加别的参数一并传到服务器 */
    public $imageExts = [];
    /* @var string 上传Flash时，支持添加别的参数一并传到服务器 */
    public $flashExts = [];
    /* @var string 上传视音频时，支持添加别的参数一并传到服务器 */
    public $mediaExts = [];
    /* @var string 上传文件时，支持添加别的参数一并传到服务器 */
    public $fileExts = [];
    /* @var string 编辑器拖动：[0:不能拖动;1:改变高度;2:改变宽度和高度] */
    public $resizeType = 0;
    /* @var int|string 编辑器宽度 */
    public $width = '666px';
    /* @var int|string 编辑器高度 */
    public $height = '150px';
    /* @var boolean 是否需要图片上传 */
    public $image = true;
    /* @var boolean 是否需要文件上传 */
    public $file = false;
    /* @var boolean 是否需要flash上传 */
    public $flash = false;
    /* @var boolean 是否需要media上传 */
    public $media = false;
    /* @var boolean 是否需要代码插入 */
    public $code = false;
    /* @var boolean 是否需要google地图 */
    public $map = false;
    /* @var boolean 是否需要baidu地图 */
    public $bmap = false;
    /* @var boolean 是否需要word粘贴 */
    public $wordPaste = true;

    /* @var string 编辑器ID */
    private $_htmlId;

    private static $_isLoadScript = false; // 编辑器的script是否已经加载
    private static $_baseUri;
    private static $_isLoadCodeJs = false;

    /**
     * 组件自动调用
     * @throws \Exception
     */
    public function init()
    {
        if (!self::$_isLoadScript) {
            self::$_isLoadScript = true;
            $src = dirname(__DIR__) . '/source/kindeditor';
            self::$_baseUri = $baseUri = AssetsManager::getInstance('assets-manager')->publish($src, 'kindeditor');
            \ClientScript::getInstance()->registerScriptFile("{$baseUri}/kindeditor-all-min.js");
            \ClientScript::getInstance()->registerScriptFile("{$baseUri}/lang/zh-CN.js");
        }
    }

    /**
     * 当开启代码时，添加代码js
     * @throws \Exception
     */
    protected function loadCodeJs()
    {
        if (!self::$_isLoadCodeJs) {
            self::$_isLoadCodeJs = true;
            \ClientScript::getInstance()->registerScriptFile(self::$_baseUri . "/plugins/code/code.js");
        }
    }

    /**
     * 获取编辑器的唯一标志
     * @return string
     */
    protected function getKFlag()
    {
        if (null === $this->kFlag) {
            $k_flag = null;
            if ($this->model instanceof Model) {
                try {
                    $k_flag = $this->model->{$this->kFlagField};
                } catch (\Exception $e) {
                }
            }
            if (empty($k_flag)) {
                $time = microtime();
                $k_flag = date('YmdHis') . '_' . substr($time, 2, 5);
            }
            $this->kFlag = $k_flag;
        }
        return $this->kFlag;
    }

    /**
     * 插入组件
     * @return array
     * @throws \Exception
     */
    protected function plugins()
    {
        $items = $extraFileUploadParams = $ops = [];
        $isUpload = false;

        if ($this->image) {
            $isUpload = true;
            $items[] = 'image';
            $items[] = 'multiimage';
            if (!empty($this->imageExts)) {
                $extraFileUploadParams['imageExts'] = $this->imageExts;
            }
        }
        if ($this->file) {
            $isUpload = true;
            $items[] = 'insertfile';
            // 限制 file 后缀
            if (!empty($this->fileExts)) {
                $extraFileUploadParams['fileExts'] = $this->fileExts;
            }
            // 允许文件上传
            $ops['allowFileManager'] = true;
            $ops['fillDescAfterUploadImage'] = true;
        }
        if ($this->flash) {
            $isUpload = true;
            $items[] = 'flash';
            // 限制 flash 后缀
            if (!empty($this->flashExts)) {
                $extraFileUploadParams['flashExts'] = $this->flashExts;
            }
        }
        if ($this->media) {
            $isUpload = true;
            $items[] = 'media';
            // 限制 media 后缀
            if (!empty($this->mediaExts)) {
                $extraFileUploadParams['mediaExts'] = $this->mediaExts;
            }
        }
        if ($this->code) {
            $items[] = 'code';
            $ops['cssPath'][] = self::$_baseUri . "/plugins/code/prettify.css";
            $this->loadCodeJs();
        }
        if ($this->map) {
            $items[] = 'map';
        }
        if ($this->bmap) {
            $items[] = 'baidumap';
        }
        if ($this->wordPaste) {
            $items[] = 'wordpaste';
        }

        if (empty($items)) {
            return [];
        }

        array_unshift($items, '|');

        if ($isUpload) {
            $ops['extraFileUploadParams'] = array_merge($extraFileUploadParams, [
                'folder' => $this->folder,
                'kFlag' => $this->getKFlag(),
            ]);
            $ops['uploadJson'] = $this->createUrl('//kindEditor/upload');
            $ops['fileManagerJson'] = $this->createUrl('//kindEditor/manage', $ops['extraFileUploadParams']);
        }
        $ops['items'] = $items;
        return $ops;
    }

    /**
     * 获取编辑器的实例化参数
     * @return array
     * @throws \Exception
     */
    protected function getOptions()
    {
        $ops = [
            'minWidth' => '600px',
            'minHeight' => '150px',
        ];
        // 拖动
        if (in_array($this->resizeType, [0, 1, 2])) {
            $ops['resizeType'] = $this->resizeType;
        }

        // css 中提取高度和宽度
        if (isset($this->htmlOptions['style']) && !empty($this->htmlOptions['style'])) {
            $styles = explode(';', $this->htmlOptions['style']);
            foreach ($styles as $style) {
                $pv = explode(':', $style);
                if ('height' === $pv[0]) {
                    $this->height = $pv[1];
                } else if ('width' === $pv[0]) {
                    $this->height = $pv[1];
                }
            }
        }
        // 宽度
        if (false !== $this->width) {
            $ops['width'] = $this->width;
        }
        // 高度
        if (false !== $this->height) {
            $ops['height'] = $this->height;
        }

        switch ($this->mode) {
            case \KindEditor::MODE_FULL:
                $ops['items'] = [
                    'source', 'emoticons', 'undo', 'redo',
                    '|', 'bold', 'italic', 'underline', 'strikethrough',
                    '|', 'fontname', 'fontsize', 'forecolor', 'hilitecolor', 'formatblock',
                    '|', 'justifyleft', 'justifycenter', 'justifyright', 'justifyfull', 'lineheight', 'indent', 'outdent',
                    '|', 'selectall', 'cut', 'copy', 'paste', 'plainpaste',
                    '|', 'table', 'hr', 'insertorderedlist', 'insertunorderedlist', 'pagebreak', 'subscript', 'superscript',
                    '|', 'anchor', 'link', 'unlink',
                ];

                // 插入组件
                $temp = $this->plugins();
                if (!empty($temp)) {
                    $ops['items'] = array_merge($ops['items'], $temp['items']);
                    unset($temp['items']);
                    $ops = array_merge($ops, $temp);
                }

                $ops['items'] = array_merge($ops['items'], ['|', 'template', 'clearhtml', 'preview', 'quickformat', 'fullscreen', 'print']);
                break;
            case \KindEditor::MODE_SIMPLE:
                if ($this->openSource) {
                    $ops['items'] = ['source', '|'];
                } else {
                    $ops['items'] = [];
                }
                $ops['items'] = array_merge($ops['items'], [
                    'emoticons', 'bold', 'italic', 'underline', 'strikethrough',
                    '|', 'justifyleft', 'justifycenter', 'justifyright', 'justifyfull',
                    '|', 'insertorderedlist', 'insertunorderedlist',
                    '|', 'anchor', 'link', 'unlink',
                ]);

                // 插入组件
                $temp = $this->plugins();
                if (!empty($temp)) {
                    $ops['items'] = array_merge($ops['items'], $temp['items']);
                    unset($temp['items']);
                    $ops = array_merge($ops, $temp);
                }

                $ops['items'] = array_merge($ops['items'], ['|', 'quickformat']);
                break;
            default: // mini
                if ($this->openSource) {
                    $ops['items'] = ['source', '|'];
                } else {
                    $ops['items'] = [];
                }
                $ops['items'] = array_merge($ops['items'], [
                    'emoticons', 'bold', 'italic', 'underline', 'strikethrough',
                    '|', 'anchor', 'link', 'unlink',
                ]);

                // 插入组件
                $temp = $this->plugins();
                if (!empty($temp)) {
                    $ops['items'] = array_merge($ops['items'], $temp['items']);
                    unset($temp['items']);
                    $ops = array_merge($ops, $temp);
                }

                $ops['items'] = array_merge($ops['items'], ['|', 'quickformat']);
                break;
        }
        return $ops;
    }

    /**
     * 获取页面编辑器的ID
     * @return string
     */
    protected function getHtmlId()
    {
        if (null !== $this->_htmlId) {
            return $this->_htmlId;
        }

        if (null !== $this->id) {
            return $this->_htmlId = $this->id;
        }
        if (isset($this->htmlOptions['id'])) {
            return $this->_htmlId = $this->htmlOptions['id'];
        }
        return $this->_htmlId = $this->contentField . '_' . $this->getKFlag();
    }

    /**
     * Run the main method of widget.
     * @throws \Exception
     */
    public function run()
    {
        if ($this->model instanceof Model) {
            if ($this->openFlag) {
                try {
                    echo Html::activeHiddenField($this->model, $this->kFlagField, [
                        'value' => $this->getKFlag(),
                    ]);
                } catch (\Exception $e) {
                    echo Html::hiddenField(get_class($this->model) . "[{$this->kFlagField}]", $this->getKFlag());
                }
            }
            echo Html::activeTextArea($this->model, $this->contentField, array_merge($this->htmlOptions, [
                'id' => $this->getHtmlId(),
            ]));
        } else {
            if ($this->openFlag) {
                echo Html::hiddenField($this->kFlagField, $this->getKFlag());
            }
            echo Html::textArea($this->contentField, $this->content, array_merge($this->htmlOptions, [
                'id' => $this->getHtmlId(),
            ]));
        }
        $option_text = Coding::json_encode($this->getOptions());
        echo <<<EOD
<script type="text/javascript">
    if(window.KEditors === undefined) {
        window.KEditors = {};
        KindEditor.pluginsPath = '/assets/kindeditor';
    }
    KindEditor.ready(function (K) {
        window.KEditors[{$this->getHtmlId()}] = K.create("#{$this->getHtmlId()}", {$option_text});
    });
</script>
EOD;
    }
}