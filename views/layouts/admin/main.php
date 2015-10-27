<?php

use backend\assets\AppAsset;
use yeesoft\assets\FormStylerAsset;
use yeesoft\assets\MetisMenuAsset;
use yeesoft\assets\YeeAsset;
use yeesoft\models\Menu;
use yeesoft\widgets\Nav;
use yeesoft\Yee;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
FormStylerAsset::register($this);
YeeAsset::register($this);
MetisMenuAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="wrap">

    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">

        <?php
        NavBar::begin([
            'brandLabel' => '<b style="font-size:1.1em;">Yee</b> ' . Yee::t('yee', 'Control Panel'),
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar-inverse navbar-static-top',
                'style' => 'margin-bottom: 0'
            ],
            'innerContainerOptions' => [
                'class' => 'container-fluid'
            ]
        ]);

        $menuItems = [
            ['label' => str_replace('http://', '', Yii::$app->urlManager->hostInfo), 'url' => Yii::$app->urlManager->hostInfo],
        ];

        if (Yii::$app->user->isGuest) {
            $menuItems[] = ['label' => Yee::t('yee', 'Login'), 'url' => ['/auth/login']];
        } else {
            $menuItems[] = [
                'label' => Yee::t('yee', 'Logout {username}', ['username' => Yii::$app->user->identity->username]),
                'url' => Yii::$app->urlManager->hostInfo . '/auth/logout',
                'linkOptions' => ['data-method' => 'post']
            ];
        }
        
        echo Nav::widget([
            'encodeLabels' => false,
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => $menuItems,
        ]);

        NavBar::end();
        ?>

        <!-- SIDEBAR NAV -->
        <div class="navbar-default sidebar metismenu" role="navigation">
            <?= Nav::widget([
                'encodeLabels' => false,
                'dropDownCaret' => '<span class="arrow"></span>',
                'options' => [
                    ['class' => 'nav side-menu'],
                    ['class' => 'nav nav-second-level'],
                    ['class' => 'nav nav-third-level']
                ],
                'items' => Menu::getMenuItems('admin-main-menu'),
            ]) ?>
        </div>
        <!-- !SIDEBAR NAV -->
    </nav>

    <div id="page-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <?= Breadcrumbs::widget(['links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : []]) ?>

                    <?php if (Yii::$app->session->hasFlash('crudMessage')): ?>
                        <div class="alert alert-info alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <?= Yii::$app->session->getFlash('crudMessage') ?>
                        </div>
                    <?php endif; ?>

                    <?= $content ?>
                </div>
            </div>
        </div>
    </div>

</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">
            <a href="http://www.yee-soft.com/" rel="external">Yee</a>
            <?= Yee::t('yee', 'Control Panel') ?> &copy; <?= date('Y') ?>
        </p>
        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
