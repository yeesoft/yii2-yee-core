<?php

namespace yeesoft\widgets\inputs;

use yii\widgets\InputWidget;
use yii\helpers\Html;
use yii\helpers\Inflector;

class RadioToggle extends InputWidget
{
    public $items = [];
    public $inline = false;

    /**
     * Runs the widget.
     */
    public function run()
    {
        if ($this->hasModel()) {
            $value = Html::getAttributeValue($this->model, $this->attribute);
            $name  = Html::getInputName($this->model, $this->attribute);
        } else {
            $value = $this->value;
            $name  = $this->attribute;
        }

        $toogle = 'radiotab-' . Inflector::slug($name);

        $content = ($this->inline) ? '' : '<div class="clearfix"></div>';
        $content .= '<div id="'.$toogle.'" class="btn-group" data-toggle="buttons">';

        if(is_array($this->items)){
            foreach ($this->items as $id => $label) {

                $checked = ($value == $id) ? true : false;
                $options = [
                    'class' => 'btn btn-default' . (($checked) ? ' active' : ''),
                    'data-toggle' => $toogle,
                    ];
                $radio = Html::radio($name, $checked, ['class' => 'non-styler', 'value' => $id]);
                $content .= Html::a($radio . $label, null, $options);
            }
        }

        $content .= '</div>';

        return $content;
    }
}