<?php

use yeesoft\widgets\assets\LanguageSelectorAsset;
use yii\helpers\ArrayHelper;
use yeesoft\helpers\LanguageHelper;

LanguageSelectorAsset::register($this);
?>

<div class="multilingual">
    <ul style="display: inline-flex; list-style: none;">
        <?php foreach ($languages as $key => $lang) : ?>
            <li style="margin-right: 10px;">
                <?php if (LanguageHelper::getLangRedirect($language) == $key) : ?>
                    <span><?= ($display == 'code') ? $key : $lang ?></span>
                <?php else: ?>
                    <?php $link = Yii::$app->urlManager->createUrl(ArrayHelper::merge($params, [$url, 'language' => $key])); ?>
                    <a href="<?= $link ?>" style="text-decoration: none;"><?= ($display == 'code') ? $key : $lang ?></a>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>