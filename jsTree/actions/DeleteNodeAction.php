<?php

namespace coderovich\jsTree\actions;

use coderovich\jsTree\interfaces\TreeInterface;
use Yii;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class DeleteNodeAction
 * @package coderovich\jsTree\actions
 */
class DeleteNodeAction extends BaseAction
{
    /**
     * Delete a node (model)
     *
     * @param integer $id the primaryKey of the moved node
     * @return array
     * @throws NotFoundHttpException
     */
    public function run()
    {
	    Yii::$app->response->format = Response::FORMAT_JSON;

    	$id = Yii::$app->request->post("id");

    	/** @var ActiveRecord|TreeInterface $model */
        $model = $this->findModel($id);

        return ["success"=>true,"deleted"=>$model->deleteWithChildren()];
    }
}