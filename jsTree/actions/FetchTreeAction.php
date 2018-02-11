<?php

namespace coderovich\jsTree\actions;

use Yii;
use coderovich\jsTree\interfaces\TreeInterface;
use yii\db\ActiveRecord;
use yii\web\HttpException;
use yii\web\Response;

/**
 * Class FetchTreeAction
 * @package coderovich\jsTree\actions
 */
class FetchTreeAction extends BaseAction {
	private $_firstLevel = [];
	public $maxDepth;

	public function init() {
		parent::init();
		$this->maxDepth = !is_callable($this->maxDepth)?(int) Yii::$app->getRequest()->get( 'maxDepth' ):$this->maxDepth;
	}

	/**
	 * @return null
	 * @throws HttpException
	 */
	public function run() {
		Yii::$app->response->format = Response::FORMAT_JSON;
		/** @var TreeInterface|ActiveRecord $model */
		$id             = Yii::$app->getRequest()->get( 'id' );
		if ( $id == "#" ) {
			$rootNodes = $this->_model::find()->roots()->all();
			if ( ! empty( $rootNodes[0] ) ) {
				/** @var ActiveRecord|TreeInterface $items */
				$this->_firstLevel = $this->prepareItems( $rootNodes[0] );
				return $this->_firstLevel;
			}
		} else {
			$node11            = $this->_model::findOne( [ "auto_list_id" => $id ] );
			$this->_firstLevel = $this->prepareItems( $node11 );
			return $this->_firstLevel;
		}
	}

	/**
	 * @param ActiveRecord|TreeInterface[] $node
	 *
	 * @return array
	 */
	private function prepareItems( $node ) {
		return $this->getNode( $node );
	}

	/**
	 * @param ActiveRecord|TreeInterface $node
	 *
	 * @return array
	 */
	protected function getNode( $node ) {
		$items = [];
		/** @var ActiveRecord[]|TreeInterface[] $children */
		$children = $node->children;
		foreach ( $children as $n => $node ) {
			$items[ $n ]['id']       = $node->getPrimaryKey();
			$items[ $n ]['text']     = $node->{$this->_model::NODE_NAME};
			$items[ $n ]['children'] = is_callable($this->maxDepth)?call_user_func($this->maxDepth,$node):($node->depth < $this->maxDepth ?: false);
		}

		return $items;
	}
}