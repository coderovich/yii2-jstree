<?php

/**
 * @copyright Copyright &copy; Sergei Pavlov, illine.com, 2017
 * @package yii2-jstree
 * @version 1.0.0
 */

namespace coderovich\jsTree;

use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use kartik\dialog\Dialog;
use rmrevin\yii\fontawesome\FA;

/**
 * JsTree widget is a Yii2 wrapper for the jsTree jQuery plugin.
 *
 * @author Thiago Talma <thiago@thiagomt.com>
 * @since 1.0
 * @see http://jstree.com
 */
class JsTree extends Widget {
	/**
	 * @var array Data configuration.
	 * If left as false the HTML inside the jstree container element is used to populate the tree (that should be an unordered list with list items).
	 */
	public $data = [];

	/**
	 * @var array Stores all defaults for the core
	 */
	public $core = [
		'expand_selected_onload' => true,
		'themes'                 => [
			'icons' => false
		]
	];

	/**
	 * @var array Stores all defaults for the checkbox plugin
	 */
	public $checkbox = [
		'three_state'         => true,
		'keep_selected_style' => false
	];

	/**
	 * @var array Stores all defaults for the contextmenu plugin
	 */
	public $contextmenu = [];

	/**
	 * @var array Stores all defaults for the drag'n'drop plugin
	 */
	public $dnd = [];

	/**
	 * @var array Stores all defaults for the search plugin
	 */
	public $search = [];

	/**
	 * @var string
	 */
	public $modelClass;

	/**
	 * @var string the settings function used to sort the nodes.
	 * It is executed in the tree's context, accepts two nodes as arguments and should return `1` or `-1`.
	 */
	public $sort = [];

	/**
	 * @var array Stores all defaults for the state plugin
	 */
	public $state = [];

	public $maxDepth = false;

	/**
	 * @var array Configure which plugins will be active on an instance. Should be an array of strings, where each element is a plugin name.
	 */
	public $plugins = [ "checkbox" ];

	/**
	 * @var array Stores all defaults for the types plugin
	 */
	public $types = [
		'#'       => [],
		'default' => [],
	];

	public $root;

	private $moveUrl;

	/**
	 * @inheritdoc
	 */
	public function init() {
		parent::init();
		if ( empty( $this->id ) ) {
			$this->id = $this->getId();
		}
		if ( $this->modelClass == null ) {
			throw new InvalidConfigException( 'Param "modelClass" must be contain model name' );
		}

		$this->contextmenu = array_merge( [ "items_bottom"=>"{}","items_top"=>"{}"], $this->contextmenu );
		$this->contextmenu = array_merge( [ 'items' => new JsExpression( $this->renderContextMenuJS() ) ], $this->contextmenu );
//echo "<pre>",var_dump($this->contextmenu["items_bottom"]),"</pre>";
		$this->moveUrl = Url::to( [ 'moveNode' ] );

		//if (!isset(new ($this->modelClass)::)


	}

	/**
	 * Registers the needed assets
	 */
	public function run() {
		$view = $this->getView();
		JsTreeAsset::register( $view );
		$url        = Url::to( [ "fetchTree", "maxDepth" => $this->maxDepth ?: null ] );
		$this->data = [
			'url'  => new JsExpression( "function (node) {
	            return '{$url}';
            }" ),
			'data' => new JsExpression( "function (node) {
	            return { 'id' : node.id };
            }" )
		];

		$checkCallback =
			new JsExpression( /** @lang JavaScript 1.8 */
				"function (op, node, par, pos, more) {
    if (op === 'move_node' && more && more.core && !confirm('Вы уверены, что хотите переместить элемент?')) {
        return false;
    }
    return true;
}");
		
		$config = [
			'core'        => array_merge( [ 'data' => $this->data, 'check_callback'=>$checkCallback ], $this->core ),
			'contextmenu' => $this->contextmenu,
			'plugins'     => $this->plugins,
			'types'       => $this->types
		];
		$defaults = Json::encode( $config );
		$this->renderButtons();

		echo Html::tag( 'div', "", [ 'id' => "jsTree_{$this->id}" ] );
		echo Dialog::widget();

		$js = /** @lang JavaScript 1.8 */
			<<<SCRIPT
;(function ($, window, document, undefined) {
    var jsTree = $('#jsTree_{$this->id}');
    jsTree.jstree({$defaults})
        .bind("select_node.jstree", function (event, data) {
            jsTree.jstree(true).open_node(data.node);
        })
        .bind("move_node.jstree", function (event, data, tree) {
            $.getJSON("{$this->moveUrl}", {id: data.node.id, "parent": data.parent}, function (response) {
            });
            
        });
    
     $(document).on('keypress', '#DialogForm', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            $(".action-button").click();
        }});
   
    })(window.jQuery, window, document);
SCRIPT;
		$view->registerJs( $js );

        $css =
            <<<SCRIPT
.jstree-contextmenu {
 z-index:999;
}
SCRIPT;
        $view->registerCss( $css );

	}

	public function renderButtons() {
		$btn = Html::a( FA::i( "compress" ) . ' Свернуть все', "#", [ 'class'   => "btn btn-sm btn-primary",
		                                                              'onclick' => "$('#jsTree_{$this->id}').jstree('close_all');return false;"
		] );
		$btn .= " " . Html::a( FA::i( "refresh" ) . ' Обновить дерево', "#", [ 'class'   => "btn btn-sm btn-success",
		                                                                       'onclick' => "$('#jsTree_{$this->id}').jstree('refresh');return false;"
			] );
		echo Html::tag( 'p', $btn );
	}

	public function renderContextMenuJS() {
		$updateNodeUrl = Url::to( [ "updateNode" ] );
		$deleteNodeUrl = Url::to( [ "deleteNode" ] );
		$createNodeUrl = Url::to( [ "createNode" ] );
		//echo "<pre>",var_dump($this->id),"</pre>";
		$js            = /** @lang JavaScript 1.8 */
			<<<SCRIPT
function context (node) {
    var tree = $("#jsTree_{$this->id}").jstree(true);
    var items_top = {$this->contextmenu["items_top"]};
    var items_bottom = {$this->contextmenu["items_bottom"]};
    var items = {
        
        "create": {
            "separator_before": false,
            "separator_after": false,
            "label": "Добавить элемент",
            "action": function (obj) {

                BootstrapDialog.show({
                    title: 'Создать элемент',
                    message: $('<div></div>').load('{$createNodeUrl}', {append: node.id}),
                    buttons: [{
                        label: 'Сохранить',
                        cssClass: 'btn-primary action-button',
                        action: function (dialogItself) {
                            var form = $("#DialogForm");
                            $.ajax({
                                type: "POST",
                                dataType: "json",
                                url: form.attr('action'),
                                data: form.serialize()+"&append="+form.data("append"),
                                success: function (response) {
                                    if (response.success == true) {
                                        dialogItself.close();
                                        var newNode = tree.create_node(node);
                                        tree.rename_node(newNode, response.nodeName);
                                        tree.set_id(newNode, response.nodeId);
                                        tree.open_node(node);
                                    }
                                }, error: function (xhr) {
                                    rsp = JSON.parse(xhr.responseText);
                                    krajeeDialog.alert(rsp.message);
                                }
                            });
                            return;
                        }
                    }, {
                        label: 'Закрыть',
                        cssClass: 'btn-warning',
                        action: function (dialogItself) {
                            dialogItself.close();
                        }
                    }]
                });             
            }
        },
        "rename": {
            "separator_before": false,
            "separator_after": false,
            "label": "Переименовать",
            "action": function (obj) {
                BootstrapDialog.show({
                    title: 'Переименовать: ' + node.text,
                    message: $('<div></div>').load('{$updateNodeUrl}?id=' + node.id),
                    buttons: [{
                        label: 'Сохранить',
                        cssClass: 'btn-primary action-button',
                        action: function (dialogItself) {
                            var form = $("#DialogForm");
                            $.ajax({
                                type: "POST",
                                dataType: "json",
                                url: form.attr('action'),
                                data: form.serialize(),
                                success: function (response) {
                                    if (response.success == true) {
                                        dialogItself.close();
                                        tree.rename_node(node, response.nodeName);
                                    }
                                }, error: function (xhr) {
                                    rsp = JSON.parse(xhr.responseText);
                                    krajeeDialog.alert(rsp.message);
                                }
                            });
                            return;
                        }
                    }, {
                        label: 'Закрыть',
                        cssClass: 'btn-warning',
                        action: function (dialogItself) {
                            dialogItself.close();

                        }
                    }]
                });
            }
        },
        "delete": {
            "separator_before": true,
            "separator_after": false,
            "label": "Удалить",
            "action": function (obj) {
                if (confirm("Вы уверены? Это действие удалит и всех потомков этого элемента.")) {
                    $.post("{$deleteNodeUrl}", {id: node.id}, function (response) {
                        if (response.success == true) {
                            tree.delete_node(node);
                        }
                    });
                }
            }
        }
    };
    var items = Object.assign(items_top,  items,items_bottom);
    return items;
}
SCRIPT;
		return $js;
	}
}

