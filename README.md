# jsTree Manager for Yii2
Виджет для управления деревом для моделей Nested Tree.

Набор Yii2 Behavior для хранения деревьев в БД и их совместного использования
===
https://habrahabr.ru/post/266155/

Nested Behaviors
===
https://github.com/paulzi/yii2-materialized-path

https://github.com/paulzi/yii2-adjacency-list

https://github.com/paulzi/yii2-nested-sets

https://github.com/paulzi/yii2-nested-intervals

How to use this extension
==

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
					return $node->depth = 1;
				}
			],
		];
	}
```

Добавляем в представление
===
```php
use coderovich\jsTree\JsTree;

echo JsTree::widget([
    'modelClass' => '\app\models\TreeModel',
    'core'    => [
    "themes" => [ "stripes" => true ],
    ],
    'contextmenu' => [
    "items_top"=>new \yii\web\JsExpression( '{"assoc": {
    "separator_before": false,
    "separator_after": false,
    "label": "My Label",
    "title": "My Label",
    "_disabled":function(){
    return node.text.match(/disable_me_by_some_js_behavior_if_needed/i);
    },
    "action": function (obj) {
    window.location = "'.\yii\helpers\Url::to(['update']).'?id="+node.id
    }
    }}' )
    ],
    
    'plugins' => [ "changed", "state", 'types', 'dnd', 'contextmenu', 'sort' ,'wholerow'],
    //...
    ]); 
```

