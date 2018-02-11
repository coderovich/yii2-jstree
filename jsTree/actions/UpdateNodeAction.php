<?php

namespace coderovich\jsTree\actions;

use coderovich\jsTree\interfaces\TreeInterface;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\db\ActiveRecord;
use yii\web\Response;
use yii\helpers\Html;

/**
 * Class UpdateNodeAction
 * @package coderovich\jsTree\actions
 */
class UpdateNodeAction extends BaseAction
{

    /**
     * Rename a node ($this->_model)
     *
     * @param integer $id the primaryKey of the renamed node
     * @return array
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function run($id)
    {
    	/** @var ActiveRecord|TreeInterface $this->_model */
        $this->_model = $this->findModel($id);
	    if ($this->_model->load(Yii::$app->request->post())) {
		    Yii::$app->response->format = Response::FORMAT_JSON;
	    	if (!$this->_model->save())
		    	throw new InvalidParamException(Html::errorSummary($this->_model));
		    else
	    	    return ["success"=>true, "nodeName"=>$this->_model->{$this->_model::NODE_NAME}];
	    } else {
		    return $this->renderForm();
	    }
    }
}