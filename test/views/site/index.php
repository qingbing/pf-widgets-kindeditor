<?php
/* @var $this \Render\Controller */
?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script src="assets/jquery-3.2.1.min.js"></script>
    <script src="assets/h.js"></script>
</head>
<body>
<?php echo \Html::beginForm(); ?>
<dl>
    <dt><?php echo \Html::activeLabel($model, 'username'); ?></dt>
    <dd><?php echo \Html::activeTextField($model, 'username'); ?></dd>
</dl>
<dl>
    <dt><?php echo \Html::activeLabel($model, 'content1'); ?></dt>
    <dd>
        <?php $this->widget('\Widgets\KindEditor', [
            'model' => $model,
            'mode' => \KindEditor::MODE_MINI,
            'contentField' => 'content1',
            'folder' => 'test',
        ]); ?>
    </dd>
</dl>
<dl>
    <dt><?php echo \Html::activeLabel($model, 'content2'); ?></dt>
    <dd>
        <?php $this->widget('\Widgets\KindEditor', [
            'model' => $model,
            'mode' => \KindEditor::MODE_SIMPLE,
            'contentField' => 'content2',
            'folder' => 'test',
        ]); ?>
    </dd>
</dl>
<dl>
    <dt><?php echo \Html::activeLabel($model, 'content3'); ?></dt>
    <dd>
        <?php $this->widget('\Widgets\KindEditor', [
            'model' => $model,
            'mode' => \KindEditor::MODE_FULL,
            'contentField' => 'content3',
            'folder' => 'test',
        ]); ?>
    </dd>
</dl>
<p>
    <?php echo \Html::submitButton('Submit', [
        'name' => 'submit',
    ]); ?>
</p>

<?php echo \Html::endForm(); ?>
</body>
</html>