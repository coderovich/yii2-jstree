# jsTree Manager for Yii2

Виджет для управления деревом.

Have to add this into Controller
===



```php
public function actions() {
		$modelClass = 'app\models\TreeClass';

		return [
			'moveNode'   => [
				'class'      => 'coderovich\jsTree\actions\MoveNodeAction',
				'modelClass' => $modelClass,
			],
			'deleteNode' => [
				'class'      => 'coderovich\jsTree\actions\DeleteNodeAction',
				'modelClass' => $modelClass,
			],
			'updateNode' => [
				'class'      => 'coderovich\jsTree\actions\UpdateNodeAction',
				'modelClass' => $modelClass,
			],
			'createNode' => [
				'class'      => 'coderovich\jsTree\actions\CreateNodeAction',
				'modelClass' => $modelClass,
			],
			'fetchTree'  => [
				'class'      => 'coderovich\jsTree\actions\FetchTreeAction',
				'modelClass' => $modelClass,
				"maxDepth"   => function ( $node ) {
					/** @var \backend\models\AutoList $node */
					return $node->group_name != AutoList::GROUP_MODIFICATION;
				}
			],
		];
	}
```


