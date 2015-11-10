<?php

use yii\helpers\ArrayHelper;
use yeesoft\widgets\assets\LanguageSelectorAsset;

LanguageSelectorAsset::register($this);
?>

<div class="multilingual">
    <ul style="display: inline-flex; list-style: none;">
        <?php foreach ($languages as $key => $lang) : ?>
            <li style="margin-right: 10px;">
                <?php if ($key != $language) : ?>
                    <?php $url = Yii::$app->urlManager->createUrl(ArrayHelper::merge($params, [$url, 'language' => $key])); ?>
                    <a href="<?= $url ?>" style="text-decoration: none;"><?= ($display == 'code') ? $key : $lang ?></a>
                <?php else: ?>
                    <span><?= ($display == 'code') ? $key : $lang ?></span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>