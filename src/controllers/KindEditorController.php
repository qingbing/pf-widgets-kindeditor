<?php
/**
 * Link         :   http://www.phpcorner.net
 * User         :   qingbing<780042175@qq.com>
 * Date         :   2019-01-08
 * Version      :   1.0
 */

namespace Controllers;


use Helper\Coding;
use Web\Abstracts\Controller;

class KindEditorController extends Controller
{
    /* @var int 允许上传的最大size */
    public $maxSize = '1000000';
    /* @var string 上传类型 */
    private $_type;
    /* @var array 上传支持的扩展名 */
    private $_exts = [
        'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
        'flash' => array('swf', 'flv'),
        'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
        'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'),
    ];

    private $_file; // $_FILES['imgFile']
    private $_ext; // 保存文件的扩展名
    private $_savePath; // 保存文件的目录名
    private $_baseUrl; // 上传文件的url
    private $_order; // 文件管理排序方式

    /**
     * 文件上传
     * @throws \Helper\Exception
     */
    public function actionUpload()
    {
        // 检查类型
        $this->_checkType();
        // 设置上传 $_FILES
        $this->_file = $_FILES['imgFile'];
        // 检查上传 $_FILES
        $this->_checkFiles();
        // 检查上传情况
        $this->_checkUpload();
        // 检查并创建上传目录
        $this->_checkSavePath();
        // 文件上传
        $this->_upload();
    }

    /**
     * 文件管理，用于图片空间等
     * @throws \Helper\Exception
     */
    public function actionManage()
    {
        $this->_order = empty($_GET['order']) ? 'name' : strtolower($_GET['order']);
        // 检查类型
        $this->_checkType();
        // 检查并获取上传目录
        $this->_checkSavePath(false);
        if (!is_dir(@$this->_savePath)) {
            $this->_jsonOutput([
                'current_url' => $this->_baseUrl . '/', //当前目录的URL
                'total_count' => 0, //文件数
                'file_list' => [], // 文件列表数组
            ]);
            echo 'Directory does not exist.';
            exit;
        }
        // 遍历目录获取文件列表
        $cur_path = $this->_savePath;
        $fileList = [];
        $supportExts = $this->_supportExts();

        if ($dp = opendir($cur_path)) {
            while ($filename = @readdir($dp)) {
                if (false === $filename)
                    break;
                if ('.' === $filename || '..' === $filename)
                    continue;
                $cur_file = $cur_path . '/' . $filename;
                // 不读取目录，不过目录的上下翻查
                if (is_dir($cur_file)) {
                    continue;
                }
                $_r = [];
                $_r['filetype'] = strtolower(pathinfo($cur_file, PATHINFO_EXTENSION));
                if (!in_array($_r['filetype'], $supportExts)) {
                    continue;
                }
                $_r['filesize'] = filesize($cur_file);
                $_r['dir_path'] = '';
                $_r['is_photo'] = in_array($_r['filetype'], $this->_exts['image']);
                $_r['filename'] = $filename;
                $_r['datetime'] = date('Y-m-d H:i:s', filemtime($cur_file));//文件最后修改时间
                $_r['is_dir'] = false;
                $_r['has_file'] = false;
                $fileList[] = $_r;
            }
            @closedir($dp);
        }
        usort($fileList, [$this, 'folderSort']);
        $this->_jsonOutput([
            'current_url' => $this->_baseUrl . '/', //当前目录的URL
            'total_count' => count($fileList), //文件数
            'file_list' => $fileList, // 文件列表数组
        ]);
    }

    /**
     * 文件排序
     * @param array $a
     * @param array $b
     * @return int
     */
    public function folderSort($a, $b)
    {
        if ($this->_order == 'size') {
            if ($a['filesize'] > $b['filesize']) {
                return 1;
            } else if ($a['filesize'] < $b['filesize']) {
                return -1;
            } else {
                return 0;
            }
        } else if ($this->_order == 'type') {
            return strcmp($a['filetype'], $b['filetype']);
        } else {
            return strcmp($a['filename'], $b['filename']);
        }
    }

    /**
     * 上传类型检查
     * @throws \Helper\Exception
     */
    private function _checkType()
    {
        //检查目录名
        $type = empty($_GET['dir']) ? 'image' : trim($_GET['dir']);
        if (!isset($this->_exts[$type])) {
            $this->_error('不支持的上传类型');
        }
        $this->_type = $type;
    }

    /**
     * 检查上传过程中是否有发生错误
     * @throws \Helper\Exception
     */
    private function _checkFiles()
    {
        if (!empty($this->_file['error'])) {
            switch ($this->_file['error']) {
                case '1':
                    $errorMsg = '超过php.ini允许的大小。';
                    break;
                case '2':
                    $errorMsg = '超过表单允许的大小。';
                    break;
                case '3':
                    $errorMsg = '图片只有部分被上传。';
                    break;
                case '4':
                    $errorMsg = '请选择图片。';
                    break;
                case '6':
                    $errorMsg = '找不到临时目录。';
                    break;
                case '7':
                    $errorMsg = '写文件到硬盘出错。';
                    break;
                case '8':
                    $errorMsg = 'File upload stopped by extension。';
                    break;
                case '999':
                default:
                    $errorMsg = '未知错误。';
            }
            $this->_error($errorMsg);
        }
    }

    /**
     * 检查上传文件类型等
     * @throws \Helper\Exception
     */
    private function _checkUpload()
    {
        $file = $this->_file;
        // 文件选择
        if (!$file['name']) {
            $this->_error('请选择文件。');
        }
        //检查是否已上传
        if (false === @is_uploaded_file($file['tmp_name'])) {
            $this->_error('上传失败。');
        }
        //文件大小
        if ($file['size'] > $this->maxSize) {
            $this->_error('上传文件大小超过限制。');
        }
        // 扩展名检查
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $supportExts = $this->_supportExts();
        if (empty($supportExts)) {
            $this->_error('ERROR：程序错误，不允许文件上传。');
        }
        if (!in_array($ext, $supportExts)) {
            $this->_error('不允许该类文件上传。');
        }
        $this->_ext = $ext;
    }

    /**
     * 获取支持的后缀类型
     * @return array
     */
    private function _supportExts()
    {
        $sp = $this->_type . 'Exts';
        if (isset($_POST[$sp]) && !empty($_POST[$sp])) {
            $allowExts = $_POST[$sp];
        } elseif (isset($_GET[$sp]) && !empty($_GET[$sp])) {
            $allowExts = $_GET[$sp];
        }
        if (isset($allowExts) && !empty($allowExts)) {
            if (is_array($allowExts)) {
                $exts = array_intersect($this->_exts[$this->_type], $allowExts);
            } else {
                $exts = array_intersect($this->_exts[$this->_type], array_map('trim', explode(',', $allowExts)));
            }
        } else {
            $exts = $this->_exts[$this->_type];
        }
        return $exts;
    }

    /**
     * 检查并设置上传目录
     * @param bool $mkdir
     * @throws \Helper\Exception
     */
    private function _checkSavePath($mkdir = true)
    {
        $folder = isset($_POST['folder']) ? $_POST['folder'] : (isset($_GET['folder']) ? trim($_GET['folder'], '/') : 'common');
        $kFlag = isset($_POST['kFlag']) ? $_POST['kFlag'] : (isset($_GET['kFlag']) ? $_GET['kFlag'] : null);
        if (empty($kFlag)) {
            $this->_error('未指定文件上传目录');
        }
        $rootPath = \KindEditor::getUploadFolder();
        if (!is_dir($rootPath)) {
            $this->_error('上传目录不存在。');
        }
        //检查目录写权限
        if (false === @is_writable($rootPath)) {
            $this->_error('上传目录没有写权限。');
        }

        $baseUrl = \KindEditor::getUploadBaseUrl() . "/{$folder}";
        $savePath = $rootPath . "/{$folder}";
        if (false === @is_dir($savePath)) {
            @mkdir($savePath, 0777);
        }
        $savePath = $savePath . "/{$kFlag}";
        $baseUrl = $baseUrl . "/{$kFlag}";

        if (false === @is_dir($savePath) && $mkdir) {
            @mkdir($savePath, 0777);
        }
        $this->_savePath = $savePath;
        $this->_baseUrl = $baseUrl;
    }

    /**
     * 上传文件
     * @throws \Helper\Exception
     */
    private function _upload()
    {
        // 文件名
        $filename = date('YmdHis') . rand(1000, 9999) . '.' . $this->_ext;
        // 移动文件
        $filepath = $this->_savePath . '/' . $filename;
        if (false === move_uploaded_file($this->_file['tmp_name'], $filepath)) {
            $this->_error('上传文件失败。');
        }
        @chmod($filepath, 0644);
        $url = $this->_baseUrl . '/' . $filename;
        $this->_jsonOutput([
            'error' => 0,
            'url' => $url,
        ]);
    }

    /**
     * 上传错误回调
     * @param string $errorMsg
     * @throws \Helper\Exception
     */
    private function _error($errorMsg)
    {
        $this->_jsonOutput([
            'error' => 1,
            'message' => $errorMsg,
        ]);
    }

    /**
     * 页面json输出
     * @param mixed $data
     * @throws \Helper\Exception
     */
    private function _jsonOutput($data)
    {
        header('Content-Type:text/html;Charset=' . APP_CHARSET);
        echo Coding::json_encode($data);
        $this->getApp()->end();
    }
}