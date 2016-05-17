<?php

namespace yeesoft\grid\columns;

use Closure;
use yeesoft\helpers\Html;
use Yii;

/**
 * TitleActionColumn is a column for the [[GridView]] widget that displays action
 * buttons and title for viewing and manipulating the items.
 *
 * To add an TitleActionColumn to the gridview, add it to the [[GridView::columns|columns]]
 * configuration as follows:
 *
 * ```php
 * 'columns' => [
 *     // ...
 *     [
 *         'class' => TitleActionColumn::className(),
 *         // you may configure additional properties here
 *     ],
 * ]
 * ```

 */
class TitleActionColumn extends \yii\grid\DataColumn
{
    public $attribute = 'title';

    /**
     * @var string the ID of the controller that should handle the actions specified here.
     * If not set, it will use the currently active controller. This property is mainly used by
     * [[urlCreator]] to create URLs for different actions. The value of this property will be prefixed
     * to each action name to form the route of the action.
     */
    public $controller;

    /**
     * @var string the template used for composing each cell in the action column.
     * Tokens enclosed within curly brackets are treated as controller action IDs (also called *button names*
     * in the context of action column). They will be replaced by the corresponding button rendering callbacks
     * specified in [[buttons]]. For example, the token `{view}` will be replaced by the result of
     * the callback `buttons['view']`. If a callback cannot be found, the token will be replaced with an empty string.
     *
     * As an example, to only have the view, and update button you can add the ActionColumn to your GridView columns as follows:
     *
     * ```
     * ['class' => 'backend\components\TitleActionColumn', 'template' => '{view} {update}'],
     * ```
     *
     * @see buttons
     */
    public $template = '<span class="action-title">{title}</span> <div class="quick-actions"> {buttons} </div>';
    public $buttonsTemplate = '{update} {view} {delete}';

    /**
     * @var array button rendering callbacks. The array keys are the button names (without curly brackets),
     * and the values are the corresponding button rendering callbacks. The callbacks should use the following
     * signature:
     *
     * ```php
     * function ($url, $model, $key) {
     *     // return the button HTML code
     * }
     * ```
     *
     * where `$url` is the URL that the column creates for the button, `$model` is the model object
     * being rendered for the current row, and `$key` is the key of the model in the data provider array.
     *
     * You can add further conditions to the button, for example only display it, when the model is
     * editable (here assuming you have a status field that indicates that):
     *
     * ```php
     * [
     *     'update' => function ($url, $model, $key) {
     *         return $model->status === 'editable' ? Html::a('Update', $url) : '';
     *     };
     * ],
     * ```
     */
    public $buttons = [];

    /**
     *
     * @var callable or string
     */
    public $title;

    /**
     * @var callable a callback that creates a button URL using the specified model information.
     * The signature of the callback should be the same as that of [[createUrl()]].
     * If this property is not set, button URLs will be created using [[createUrl()]].
     */
    public $urlCreator;

    /**
     * @var array html options to be applied to the [[initDefaultButtons()|default buttons]].
     */
    public $buttonOptions = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->initDefaultButtons();
    }

    /**
     * Initializes the default button rendering callbacks.
     */
    protected function initDefaultButtons()
    {
        if (!isset($this->buttons['view'])) {
            $this->buttons['view'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t('yee', 'View'),
                    'aria-label' => Yii::t('yee', 'View'),
                    'data-pjax' => '0',
                ], $this->buttonOptions);
                return Html::a(Yii::t('yee', 'View'), $url, $options);
            };
        }
        if (!isset($this->buttons['update'])) {
            $this->buttons['update'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t('yee', 'Edit'),
                    'aria-label' => Yii::t('yee', 'Edit'),
                    'data-pjax' => '0',
                ], $this->buttonOptions);
                return Html::a(Yii::t('yee', 'Edit'), $url, $options);
            };
        }
        if (!isset($this->buttons['delete'])) {
            $this->buttons['delete'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t('yee', 'Delete'),
                    'aria-label' => Yii::t('yee', 'Delete'),
                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'data-method' => 'post',
                    'data-pjax' => '0',
                ], $this->buttonOptions);

                return Html::a(Yii::t('yee', 'Delete'), $url, $options);
            };
        }
    }

    /**
     * Creates a URL for the given action and model.
     * This method is called for each button and each row.
     * @param string $action the button name (or action ID)
     * @param \yeesoft\db\ActiveRecord $model the data model
     * @param mixed $key the key associated with the data model
     * @param integer $index the current row index
     * @return string the created URL
     */
    public function createUrl($action, $model, $key, $index)
    {
        if ($this->urlCreator instanceof Closure) {
            return call_user_func($this->urlCreator, $action, $model, $key,
                $index);
        } else {
            $params = is_array($key) ? $key : ['id' => (string)$key];
            $params[0] = $this->controller ? $this->controller . '/' . $action : $action;

            return $params;
        }
    }

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $buttons = preg_replace_callback('/\\{([\w\-\/]+)\\}/',
            function ($matches) use ($model, $key, $index) {
                $name = $matches[1];
                if (isset($this->buttons[$name])) {
                    $url = $this->createUrl($name, $model, $key, $index);

                    return call_user_func($this->buttons[$name], $url, $model, $key);
                } else {
                    return '';
                }
            }, $this->buttonsTemplate);


        return preg_replace_callback('/\\{([\w\-\/]+)\\}/',
            function ($matches) use ($buttons, $model) {
                $name = $matches[1];
                if ($name == 'buttons') {
                    return $buttons;
                } elseif ($name == 'title') {
                    return ($this->title instanceof Closure) ? call_user_func($this->title,
                        $model) : $this->title;
                } else {
                    return '';
                }
            }, $this->template);
    }

    /**
     * Renders the filter cell content.
     * The default implementation simply renders a space.
     * This method may be overridden to customize the rendering of the filter cell (if any).
     * @return string the rendering result
     */
    protected function renderFilterCellContent()
    {
        if (is_string($this->filter)) {
            return $this->filter;
        }

        $model = $this->grid->filterModel;

        if ($this->filter !== false && $model instanceof Model && $this->attribute
            !== null && $model->isAttributeActive($this->attribute)
        ) {
            if ($model->hasErrors($this->attribute)) {
                Html::addCssClass($this->filterOptions, 'has-error');
                $error = ' ' . Html::error($model, $this->attribute,
                        $this->grid->filterErrorOptions);
            } else {
                $error = '';
            }

            return Html::activeTextInput($model, $this->attribute,
                $this->filterInputOptions) . $error;
        } else {
            return parent::renderFilterCellContent();
        }
    }
}