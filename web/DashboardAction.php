<?php

namespace yeesoft\web;

use Yii;
use yii\base\Action;
use yii\base\InvalidParamException;

class DashboardAction extends Action
{
    public $widgets;
    public $layout;

    /**
     * Runs the action.
     * This method displays the view requested by the user.
     * @throws NotFoundHttpException if the view file cannot be found
     */
    public function run()
    {
        $this->controller->getView()->title = Yii::t('yee', 'Dashboard');

        if (!is_array($this->widgets)) {
            throw new NotFoundHttpException(Yii::t('yee', 'Invalid settings for dashboard widgets.'));
        }

        $controllerLayout = null;
        if ($this->layout !== null) {
            $controllerLayout = $this->controller->layout;
            $this->controller->layout = $this->layout;
        }

        try {
            $output = $this->render();

            if ($controllerLayout) {
                $this->controller->layout = $controllerLayout;
            }
        } catch (InvalidParamException $e) {

            if ($controllerLayout) {
                $this->controller->layout = $controllerLayout;
            }

            if (YII_DEBUG) {
                throw new NotFoundHttpException($e->getMessage());
            } else {
                throw new NotFoundHttpException(
                    Yii::t('yii', 'The requested view was not found.')
                );
            }
        }

        return $output;
    }

    /**
     * Renders a view
     *
     * @return string result of the rendering
     */
    protected function render()
    {
        $content = '<div class="dashboard"><div class="row"><div class="col-sm-12">';

        foreach ($this->widgets as $widget) {
            if (is_string($widget)) {

                $content .= $widget::widget();

            } elseif (is_array($widget)) {

                if (!isset($widget['class'])) {
                    throw new NotFoundHttpException(Yii::t('yee', 'Invalid settings for dashboard widgets.'));
                }

                $class = $widget['class'];
                $settings = $widget;
                unset($settings['class']);
                $content .= $class::widget($settings);

            } else {
                throw new NotFoundHttpException(Yii::t('yee', 'Invalid settings for dashboard widgets.'));
            }
        }

        $content .= '</div></div></div>';

        return $this->controller->renderContent($content);

    }
}