# pf-widgets-kindeditor
## 描述
渲染部件——在线编辑器(kindeditor)

## 注意事项
- 该组件基于"qingbing/php-render"上开发运行
- kindeditor 资源无需手动管理，参考"qingbing/pf-assets-manager" 即可
- 如果需要使用到 kindeditor 的文件上传功能，则需要在url-manager规则最前放加入
```php
        // 编辑器路由规则
        [
            'pattern' => 'KindEditor/<action:\w+>/*',
            'route' => 'KindEditor/<action>',
        ],
```
- 编辑器默认上传文件为，根目录下"kindeditor"，可通过 \PF::app()->getParams('kdUploadFolder'); 进行设置和获取
- "\Widgets\KindEditor" 默认支持"\KindEditor::MODE_MINI"(默认), "\KindEditor::MODE_SIMPLE", "\KindEditor::MODE_FULL"

## 使用方法
### 使用 "\Widgets\KindEditor" 小部件渲染视图
```php
$this->widget('\Widgets\KindEditor', [
    'model' => $model,
    'mode' => \KindEditor::MODE_FULL,
    'folder' => 'static',
    'height' => '400px',
]);
```

## ====== 异常代码集合 ======

异常代码格式：1024 - XXX - XX （组件编号 - 文件编号 - 代码内异常）
```
 - 无
```