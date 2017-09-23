<?php

namespace yeesoft\db;

use Yii;
use yii\base\InvalidValueException;
use yeesoft\filters\ActiveFilterInterface;

trait ActiveFilterTrait
{

    /**
     * @var array the list of the ActiveFilter.
     */
    private $_filters;

    /**
     *
     * @var boolean indicates whether were active filter applied 
     */
    private $_filterApplied = false;

    /**
     * Applies active filters to ActiveQuery.
     * 
     * @return $this
     */
    public function applyFilters()
    {


        if (!$this->_filterApplied) {
            /* @var $this ActiveQuery */
            $modelClass = $this->modelClass;
            $filters = $this->getFilters($modelClass);

            /* @var $filter \yeesoft\filters\ActiveFilter */
            foreach ($filters as $filter) {
                $filter->apply($this);
            }

            $this->_filterApplied = true;
        }

        return $this;
    }

    /**
     * Returns list of active filters.
     * 
     * @param string $modelClass
     * @return ActiveFilterInterface[] list of active filter
     * @throws InvalidValueException
     */
    public function getFilters($modelClass)
    {
        if (!$this->_filters) {

            $this->_filters = [];
            $user = Yii::$app->user;
            $filters = Yii::$app->authManager->getFiltersByUserId($modelClass, $user->id);
            
            foreach ($filters as $filter) {
                $activeFilter = (new $filter);
                if (!$activeFilter instanceof ActiveFilterInterface) {
                    throw new InvalidValueException('Unexpected type of active filter.');
                }

                $this->_filters[] = $activeFilter;
            }
        }

        return $this->_filters;
    }

}
