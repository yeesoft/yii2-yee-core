<?php

namespace yeesoft\filters;

interface ActiveFilterInterface
{

    /**
     * Applies filters to the $query.
     * 
     * @param \yeesoft\db\ActiveQuery $query
     */
    public function apply(&$query);
}
