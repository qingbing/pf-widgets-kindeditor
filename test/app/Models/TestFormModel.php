<?php
/**
 * Link         :   http://www.phpcorner.net
 * User         :   qingbing<780042175@qq.com>
 * Date         :   2019-01-07
 * Version      :   1.0
 */

namespace TestApp\Models;


use Abstracts\FormModel;

class TestFormModel extends FormModel
{
    /* 用户名 */
    public $username;
    /* 内容 */
    public $content1;
    public $content2;
    public $content3;
    public $x_flag;

    public function rules()
    {
        return [
            ['username', 'username', 'allowEmpty' => false],
            ['x_flag', 'string', 'allowEmpty' => true],
            ['content1, content2, content3', 'string', 'allowEmpty' => false],
        ];
    }
}