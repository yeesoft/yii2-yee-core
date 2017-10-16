<?php

namespace yeesoft\grid;

class CheckboxColumn extends \yii\grid\CheckboxColumn {

    public $displayFilter = true;

    /**
     * Renders the filter cell.
     */
    public function renderFilterCell() {
        return ($this->displayFilter) ? parent::renderFilterCell() : '';
    }

}
