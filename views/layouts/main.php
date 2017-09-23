<?php

use yeesoft\models\Menu;
use yeesoft\widgets\Breadcrumbs;
use yeesoft\widgets\Nav;
use yeesoft\helpers\Html;
use yeesoft\theme\assets\AdminThemeAsset;

/* @var $this \yii\web\View */
/* @var $content string */

//\yeesoft\assets\AdminLTEAsset::register($this);
AdminThemeAsset::register($this);

//Show Flashes
foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
    $this->registerJs("Notification.show('{$key}', '{$message}');");
}
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">

        <?php $this->head() ?>

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <style>
            .example-modal .modal {
                position: relative;
                top: auto;
                bottom: auto;
                right: auto;
                left: auto;
                display: block;
                z-index: 1;
            }

            .example-modal .modal {
                background: transparent !important;
            }
            
            .logo-1, .logo-2 {
                display: inline-block;
                font-size: 26px;
                font-weight: bold;
            }
            
            .logo-1 {
                color: #4d74b0;
            }
            
            .logo-2 {
                color: #fff;
            }
            
            .logo-block {
                display: inline-block;
                height: 22px;
                width: 23px;
                background: #fff;
                color: #202731;
                line-height: 20px;
                padding: 1px 6px;
                font-weight: bold;
                text-transform: uppercase;
            }
            
            a:hover .logo-block {
                background: #4d74b0;
                color: #fff;
            }
        </style>
    </head>
    <body class="hold-transition skin-dark sidebar-mini">
        <?php $this->beginBody() ?>


        <!-- Site wrapper -->
        <div class="wrapper">

            <header class="main-header">
                <div class="logo">
                    
                    <a class="logo-title" href="/admin">
                        <img style="height: 30px; margin-top: -10px;" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+CjxzdmcKICAgeG1sbnM6ZGM9Imh0dHA6Ly9wdXJsLm9yZy9kYy9lbGVtZW50cy8xLjEvIgogICB4bWxuczpjYz0iaHR0cDovL2NyZWF0aXZlY29tbW9ucy5vcmcvbnMjIgogICB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiCiAgIHhtbG5zOnN2Zz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciCiAgIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIKICAgdmlld0JveD0iMCAwIDM5Ny43OTk5OSAzNzIiCiAgIGhlaWdodD0iMzcyIgogICB3aWR0aD0iMzk3Ljc5OTk5IgogICB2ZXJzaW9uPSIxLjEiCiAgIGlkPSJzdmczNTM3Ij4KICA8bWV0YWRhdGEKICAgICBpZD0ibWV0YWRhdGEzNTQzIj4KICAgIDxyZGY6UkRGPgogICAgICA8Y2M6V29yawogICAgICAgICByZGY6YWJvdXQ9IiI+CiAgICAgICAgPGRjOmZvcm1hdD5pbWFnZS9zdmcreG1sPC9kYzpmb3JtYXQ+CiAgICAgICAgPGRjOnR5cGUKICAgICAgICAgICByZGY6cmVzb3VyY2U9Imh0dHA6Ly9wdXJsLm9yZy9kYy9kY21pdHlwZS9TdGlsbEltYWdlIiAvPgogICAgICAgIDxkYzp0aXRsZT48L2RjOnRpdGxlPgogICAgICA8L2NjOldvcms+CiAgICA8L3JkZjpSREY+CiAgPC9tZXRhZGF0YT4KICA8ZGVmcwogICAgIGlkPSJkZWZzMzU0MSIgLz4KICA8cGF0aAogICAgIGlkPSJwYXRoMzU1MSIKICAgICBkPSJNIDkxLjgyOTc2NCwyNDQuNjA5MjQgMjE4LjEyNTg5LDE2OS44NDU4NCAxMiw2MiA1MS4wOTA4NTQsNDIuNDc2MzE0IDI5NywxNjkgOTEuMTc4ODQ0LDI5MS40MTI0OCBaIgogICAgIHN0eWxlPSJmaWxsOiM1ZjhkZDMiIC8+CiAgPHBhdGgKICAgICBpZD0icGF0aDM1NTUiCiAgICAgZD0iTSA1NSwxMzAgOTMuODcwNjQ0LDE1MC44MjAzMyA5MS43MTA5MjQsMjg4LjUxMDc0IDI5NywxNjkgMjk3LDIxNCA1NSwzNjEgWiIKICAgICBzdHlsZT0iZmlsbDojMmM1YWEwIiAvPgogIDxwYXRoCiAgICAgaWQ9InBhdGgzNTU3IgogICAgIGQ9Ik0gMTIsMzQxLjA4MjE3IDEyLDYyIDIxOC4xOTE5MSwxNjkuODI0MzQgMTY5LDE5OSA1NSwxMzUgNTUsMzYxIFoiCiAgICAgc3R5bGU9ImZpbGw6IzIxNDQ3OCIgLz4KPC9zdmc+Cg=="/>
                        <span class="logo-1">yee</span>
                        <span class="logo-2">cms</span>
                    </a>
                    
<!--                    <a class="logo-title" href="/admin">
                        <span class="logo-block">Y</span>
                        <span class="logo-block">e</span>
                        <span class="logo-block">e</span>
                        
                        CMS
                    </a>-->
                    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                </div>

                <!-- Header Navbar: style can be found in header.less -->
                <nav class="navbar navbar-static-top">


                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                            <!-- Messages: style can be found in dropdown.less-->
                            <li class="dropdown messages-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-envelope-o"></i>
                                    <span class="label label-primary">4</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="header">You have 4 messages</li>
                                    <li>
                                        <!-- inner menu: contains the actual data -->
                                        <ul class="menu">
                                            <li><!-- start message -->
                                                <a href="#">
                                                    <div class="pull-left">
                                                        <img src="https://www.iconexperience.com/_img/o_collection_png/green_dark_grey/512x512/plain/user.png" class="img-circle" alt="User Image">
                                                    </div>
                                                    <h4>
                                                        Support Team
                                                        <small><i class="fa fa-clock-o"></i> 5 mins</small>
                                                    </h4>
                                                    <p>Why not buy a new awesome theme?</p>
                                                </a>
                                            </li>
                                            <!-- end message -->
                                        </ul>
                                    </li>
                                    <li class="footer"><a href="#">See All Messages</a></li>
                                </ul>
                            </li>
                            <!-- Notifications: style can be found in dropdown.less -->
                            <li class="dropdown notifications-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-bell-o"></i>
                                    <span class="label label-primary">10</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="header">You have 10 notifications</li>
                                    <li>
                                        <!-- inner menu: contains the actual data -->
                                        <ul class="menu">
                                            <li>
                                                <a href="#">
                                                    <i class="fa fa-users text-aqua"></i> 5 new members joined today
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="footer"><a href="#">View all</a></li>
                                </ul>
                            </li>
                            <!-- Tasks: style can be found in dropdown.less -->
                            <li class="dropdown tasks-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-flag-o"></i>
                                    <span class="label label-primary">9</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="header">You have 9 tasks</li>
                                    <li>
                                        <!-- inner menu: contains the actual data -->
                                        <ul class="menu">
                                            <li><!-- Task item -->
                                                <a href="#">
                                                    <h3>
                                                        Design some buttons
                                                        <small class="pull-right">20%</small>
                                                    </h3>
                                                    <div class="progress xs">
                                                        <div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                            <span class="sr-only">20% Complete</span>
                                                        </div>
                                                    </div>
                                                </a>
                                            </li>
                                            <!-- end task item -->
                                        </ul>
                                    </li>
                                    <li class="footer">
                                        <a href="#">View all tasks</a>
                                    </li>
                                </ul>
                            </li>
                            <!-- User Account: style can be found in dropdown.less -->
                            <li class="dropdown user user-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <img src="https://www.iconexperience.com/_img/o_collection_png/green_dark_grey/512x512/plain/user.png" class="user-image" alt="User Image">
                                    <span class="hidden-xs"><?= @Yii::$app->user->identity->username ?></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <!-- User image -->
                                    <li class="user-header">
                                        <img src="https://www.iconexperience.com/_img/o_collection_png/green_dark_grey/512x512/plain/user.png" class="img-circle" alt="User Image">

                                        <p>
                                            Alexander Pierce - Web Developer
                                            <small>Member since Nov. 2012</small>
                                        </p>
                                    </li>
                                    <!-- Menu Body -->
                                    <li class="user-body">
                                        <div class="row">
                                            <div class="col-xs-4 text-center">
                                                <a href="#">Followers</a>
                                            </div>
                                            <div class="col-xs-4 text-center">
                                                <a href="#">Sales</a>
                                            </div>
                                            <div class="col-xs-4 text-center">
                                                <a href="#">Friends</a>
                                            </div>
                                        </div>
                                        <!-- /.row -->
                                    </li>
                                    <!-- Menu Footer-->
                                    <li class="user-footer">
                                        <div class="pull-left">
                                            <a href="#" class="btn btn-default btn-flat">Profile</a>
                                        </div>
                                        <div class="pull-right">
                                            <a href="#" class="btn btn-default btn-flat">Sign out</a>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="#" data-toggle="control-sidebar"><i class="fa fa-ellipsis-v"></i></a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>

            <!-- =============================================== -->

            <aside class="main-sidebar">
                <section class="sidebar">
                    <?= Nav::widget(['items' => Menu::getMenuItems('admin-menu')]) ?>
                </section>
            </aside>

            <!-- =============================================== -->

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header clearfix">
                    <h1 class="pull-left">
                        <?= Html::encode($this->title) ?>
                        <small><?= Html::encode(isset($this->params['description']) ? $this->params['description'] : '') ?></small>
                    </h1>

                    <?php if (isset($this->params['header-content'])): ?>
                        <?= $this->params['header-content'] ?>
                    <?php endif; ?>

                    <?= Breadcrumbs::widget(['links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : []]) ?>
                </section>

                <!-- Main content -->
                <section class="content">
                    <?= $content ?>
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->

            <footer class="main-footer">
                <div class="pull-right hidden-xs">
                    <b>Version</b> 0.2.x
                </div>
                <strong>Copyright &copy; 2015-<?= date("Y") ?> <a href="https://www.yee-soft.com/">YeeSoft</a>.</strong> All rights reserved.
                <?= Html::a('[elements]', ['/dashboard/default/elements']) ?>
            </footer>

        </div>
        <!-- ./wrapper -->

        <!-- jQuery 2.2.3 -->
        <!--
        <script src="../../plugins/jQuery/jquery-2.2.3.min.js"></script>
        
        <script src="../../bootstrap/js/bootstrap.min.js"></script>
      
        <script src="../../plugins/slimScroll/jquery.slimscroll.min.js"></script>
        
        <script src="../../plugins/fastclick/fastclick.js"></script>
        
        <script src="../../dist/js/app.min.js"></script>
          
        <script src="../../dist/js/demo.js"></script>

        -->

        <div class="notification-container"></div>

        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>