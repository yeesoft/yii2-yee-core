<?php

namespace yeesoft\db;

interface FilterableQuery
{

    /**
     * Applies active filters to ActiveQuery.
     * 
     * @return ActiveQuery
     */
    public function applyFilters();
}
