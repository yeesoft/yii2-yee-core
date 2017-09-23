<?php

namespace yeesoft\db;

class ActiveQuery extends \yii\db\ActiveQuery implements FilterableQuery
{

    use ActiveFilterTrait;
}
