# jsTree Manager for Yii2

Виджет для управления деревом.

Добавляем в контроллер
===

```php
public function actions() {
		$modelClass = 'app\models\TreeModel';

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
				# Autoload items before this node level
				"maxDepth"   => function ( $node ) {
					/** @var \app\models\TreeModel $node */
					return $node->depth = 2;
				}
			],
		];
	}
```


