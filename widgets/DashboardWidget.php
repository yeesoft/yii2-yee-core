<?php

namespace yeesoft\widgets;

abstract class DashboardWidget extends \yii\base\Widget
{
    /**
     * Widget height
     */
    public $height = 'auto';

    /**
     * Widget width
     */
    public $width = '6';

    /**
     * Widget position
     *
     * @var string
     */
    public $position = 'left';

}