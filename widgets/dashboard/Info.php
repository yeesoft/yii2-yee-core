<?php

namespace yeesoft\widgets\dashboard;

use yeesoft\widgets\DashboardWidget;

class Info extends DashboardWidget
{
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