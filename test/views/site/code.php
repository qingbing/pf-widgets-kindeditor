<?php
use Components\Request;
?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<?php KindEditor::codePrettify(); ?>
<pre class="prettyprint linenums lang-php">
    /**
     * 获取编辑器的唯一标志
     * @return string
     */
    protected function getKFlag()
    {
        if (null === $this-&gt;kFlag) {
            $k_flag = null;
        }
        return $this-&gt;kFlag;
    }</pre>
</body>
</html>