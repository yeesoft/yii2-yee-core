<?php

use yii\helpers\ArrayHelper;

list($route, $params) = Yii::$app->getUrlManager()->parseRequest(Yii::$app->getRequest());
$params = ArrayHelper::merge($_GET, $params);
$url = isset($params['route']) ? $params['route'] : $route;
?>

<div class="multilingual">
    <ul style="display: inline-flex; list-style: none;">
        <?php foreach ($languages as $key => $lang) : ?>
            <?php if ($key != $language) : ?>
                <?php $url = Yii::$app->urlManager->createUrl(ArrayHelper::merge($params, [$url, 'language' => $key])); ?>
                <li style="margin-right: 10px;"><a href="<?= $url ?>" style="text-decoration: none;"><?= $key ?></a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</div>