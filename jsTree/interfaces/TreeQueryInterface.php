<?php

namespace coderovich\jsTree\interfaces;

use yii\db\ActiveQuery;


/**
 * Interface TreeQueryInterface
 * @package coderovich\jsTree\interfaces
 */
interface TreeQueryInterface
{
    /**
     * @return ActiveQuery
     */
    public function roots();
}