<?php

use yii\helpers\ArrayHelper;
use yeesoft\widgets\assets\LanguageSelectorAsset;

LanguageSelectorAsset::register($this);
?>

<div class="multilingual">
    <ul class="nav nav-pills">
        <?php foreach ($languages as $key => $lang) : ?>
            <?php if ($key != $language) : ?>
                <?php $url = Yii::$app->urlManager->createUrl(ArrayHelper::merge($params, [$url, 'language' => $key])); ?>
                <li role="language">
                    <a href="<?= $url ?>"><?= ($display == 'code') ? $key : $lang ?></a>
                </li>
            <?php else: ?>
                <li role="language" class="active">
                    <a><?= ($display == 'code') ? $key : $lang ?></a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</div>
