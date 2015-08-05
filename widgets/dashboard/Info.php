<?php

namespace yeesoft\widgets\dashboard;

class Info extends \yii\base\Widget
{
    /**
     * Widget Height
     */
    public $height = '1-5';

    /**
     * Widget Width
     */
    public $width = '4';

    /**
     *
     * @var type
     */
    public $position = 'left';

    public function run()
    {
        return $this->render('info',
            [
                'height' => $this->height,
                'width' => $this->width,
                'position' => $this->position,
            ]);
    }
}