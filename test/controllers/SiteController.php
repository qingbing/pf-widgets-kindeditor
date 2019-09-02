<?php
/**
 * Link         :   http://www.phpcorner.net
 * User         :   qingbing<780042175@qq.com>
 * Date         :   2019-01-04
 * Version      :   1.0
 */

namespace Controllers;


use Render\Abstracts\Controller;
use TestApp\Models\TestFormModel;

class SiteController extends Controller
{
    /**
     * @throws \Exception
     * @throws \ReflectionException
     */
    public function actionIndex()
    {
        $model = new TestFormModel();
        if (isset($_POST['submit'])) {
            $model->setAttributes($_POST['TestFormModel']);
            if ($model->validate()) {
                var_dump($model->getAttributes());
//                $this->success('验证成功');
            } else {
                $this->failure('验证失败', $model->getErrors());
            }
        }
        $this->render('index', [
            'model' => $model,
        ]);
    }

    /**
     * @throws \Exception
     * @throws \ReflectionException
     */
    public function actionCode()
    {
        $this->render('code', [
        ]);
    }
}