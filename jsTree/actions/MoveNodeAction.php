<?php

namespace coderovich\jsTree\actions;


use yii\db\ActiveRecord;
use yii\web\HttpException;

/**
 * Class MoveNodeAction
 * @package coderovich\jsTree\actions
 */
class MoveNodeAction extends BaseAction
{
    /**
     * Move a node (model) below the parent and in between left and right
     *
     * @param integer $id the primaryKey of the moved node
     * @return array
     * @throws HttpException
     */
    public function run($id,$parent)
    {
        /** @var ActiveRecord $this->_model */
        $this->_model = $this->findModel($id);
        if ($parent!="#") {
	        /** @var ActiveRecord $parent */
        	$parent = $this->findModel( $parent );
	        return $this->_model->appendTo($parent)->save();
        } else {
	        $parent = $this->_model::find()->roots()->all();
	        if (isset($parent[0])) {
		        return $this->_model->appendTo($parent[0])->save();
	        } else {
		        return $this->_model->makeRoot()->save();
	        }
        }
    }
}