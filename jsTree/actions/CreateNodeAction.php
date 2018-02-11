<?php

namespace coderovich\jsTree\actions;

use Yii;
use coderovich\jsTree\interfaces\TreeInterface;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\web\HttpException;
use yii\web\Response;
use yii\base\InvalidParamException;

/**
 * Class CreateNodeAction
 * @package coderovich\jsTree\actions
 */
class CreateNodeAction extends BaseAction
{
	/**
     * @return null
     * @throws HttpException
     */
    public function run()
    {
	    Yii::$app->response->format = Response::FORMAT_JSON;

	    /** @var TreeInterface|ActiveRecord $this->_model */
	    $this->_model = Yii::createObject( $this->modelClass );
	    $append = Yii::$app->request->post("append");
	    if ($this->_model->load(Yii::$app->request->post())) {
		    if (!$this->_model->validate()) {
			    throw new InvalidParamException(Html::errorSummary($this->_model));
		    }
		    $roots = $this->_model::findOne($append);
		    if ($roots) {
			    $this->_model->appendTo( $roots )->save();
		    } else {
			    $this->_model->makeRoot()->save();
		    }
		    return ["success"=>true, "nodeId"=>$this->_model->getPrimaryKey(),"nodeName"=>$this->_model->{$this->_model::NODE_NAME}];
	    } else {
		    return $this->renderForm($append);
	    }
    }
}