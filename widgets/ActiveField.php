<?php

namespace yeesoft\widgets;

use Yii;
use yii\helpers\Html;
use yeesoft\helpers\FA;
use yeesoft\widgets\assets\EditableTextInputAsset;
use yeesoft\widgets\assets\SlugableTextInputAsset;
use yeesoft\multilingual\helpers\MultilingualHelper;

/**
 * @inheritdoc
 */
class ActiveField extends \yeesoft\multilingual\widgets\ActiveField
{

    /**
     * @var string the template for checkboxes in default layout
     */
    public $checkboxTemplate = "<div class=\"checkbox\">\n{input}\n{beginLabel}\n{labelTitle}\n{endLabel}\n{error}\n{hint}\n</div>";

    /**
     * @var string the template for radios in default layout
     */
    public $radioTemplate = "<div class=\"radio\">\n{input}\n{beginLabel}\n{labelTitle}\n{endLabel}\n{error}\n{hint}\n</div>";

    /**
     * Renders a text value.
     *
     * @return $this the field object itself.
     */
    public function value($options = [])
    {
        $this->addAriaAttributes($options);
        $this->adjustLabelFor($options);

        if (!array_key_exists('id', $options)) {
            $options['id'] = Html::getInputId($this->model, $this->attribute);
        }

        $value = isset($options['value']) ? $options['value'] : Html::getAttributeValue($this->model, $this->attribute);
        $this->parts['{input}'] = Html::tag('span', ": {$value}", $options);

        return $this;
    }

    /**
     * Renders editable text input.
     * 
     * @param mixed $options
     * @param string $prefix
     * @param string $suffix
     * @return $this
     */
    public function editableTextInput($options = [], $prefix = null, $suffix = null)
    {
        EditableTextInputAsset::register(Yii::$app->view);
        $this->parts['{input}'] = $this->editableTextInputWidget($options, $prefix, $suffix);
        return $this;
    }

    /**
     * @var string|array 
     */

    /**
     * Renders slug input.
     * 
     * @param mixed $options
     * @param string $attribute the attribute whose value will be converted into a slug
     * @param string $prefix
     * @param string $suffix
     * @return $this
     */
    public function slugInput($options = [], $attribute = null, $prefix = null, $suffix = null)
    {
        if ($attribute) {
            $slugInputId = Html::getInputId($this->model, $this->attribute);
            $attributeInputId = Html::getInputId($this->model, $attribute);

            if ($this->model->getBehavior('multilingual')) {
                $language = Yii::$app->language;
                $attributeInputId = MultilingualHelper::getAttributeName($attributeInputId, $language);
            }

            SlugableTextInputAsset::register(Yii::$app->view);
            Yii::$app->view->registerJs("Slugable.addDependency('{$slugInputId}', '{$attributeInputId}');");
        }

        $this->parts['{input}'] = $this->editableTextInputWidget($options, $prefix, $suffix);
        return $this;
    }

    private function editableTextInputWidget($options = [], $prefix = null, $suffix = null)
    {
        $value = $this->model->{$this->attribute};
        $options = array_merge($this->inputOptions, $options);
        $this->addAriaAttributes($options);
        $this->adjustLabelFor($options);

        $input = Html::activeTextInput($this->model, $this->attribute, $options);
        $prefixAddon = ($prefix) ? Html::tag('span', $prefix, ['class' => 'input-group-addon']) : '';
        $suffixAddon = ($suffix) ? Html::tag('span', $suffix, ['class' => 'input-group-addon']) : '';
        $button = Html::button(FA::i(FA::_CHECK), ['class' => 'btn btn-primary btn-flat']);
        $actions = Html::tag('span', $button, ['class' => 'input-group-btn']);
        $inputGroup = Html::tag('div', $prefixAddon . $input . $suffixAddon . $actions, ['class' => 'input-group']);

        $linkPrefix = ($prefix) ? Html::tag('span', $prefix) : '';
        $linkSuffix = ($suffix) ? Html::tag('span', $suffix) : '';
        $link = Html::tag('div', $linkPrefix . Html::tag('a', $value) . $linkSuffix, ['class' => 'input']);

        return Html::tag('div', $link . $inputGroup, ['class' => 'editable']);
    }

}
