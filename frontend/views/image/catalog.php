<?php

use common\models\Image;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
use newerton\fancybox\FancyBox;
use kartik\icons\Icon;
/** @var yii\web\View $this */
/** @var common\models\search\ImageSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Все картинки';
$this->params['breadcrumbs'][] = $this->title;

Icon::map($this);

?>
<div class="image-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php

    	echo newerton\fancybox\FancyBox::widget([
		    'target' => 'a[rel=fancybox]',
		    'helpers' => true,
		    'mouse' => true,
		    'config' => [
		        'maxWidth' => '90%',
		        'maxHeight' => '90%',
		        'playSpeed' => 7000,
		        'padding' => 0,
		        'fitToView' => false,
		        'width' => '70%',
		        'height' => '70%',
		        'autoSize' => false,
		        'closeClick' => false,
		        'openEffect' => 'elastic',
		        'closeEffect' => 'elastic',
		        'prevEffect' => 'elastic',
		        'nextEffect' => 'elastic',
		        'closeBtn' => false,
		        'openOpacity' => true,
		        'helpers' => [
		            'title' => ['type' => 'float'],
		            'buttons' => [],
		            'thumbs' => ['width' => 68, 'height' => 50],
		            'overlay' => [
		                'css' => [
		                    'background' => 'rgba(0, 0, 0, 0.8)'
		                ]
		            ]
		        ],
		    ]
		]);

	?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',

            [
                'attribute' => 'file_img',
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-center'],
                'value' => function($dataProvider) {

                	// Modal::begin([ 'id' => 'imageview', 'footer' => '<a href="#" class="btn btn-sm btn-primary" data-dismiss="modal">Close</a>', ]); Modal::end();

                	$file_path = Yii::$app->request->hostInfo.Yii::$app->params['filePath']
                					. $dataProvider->file_img;

                	return Html::a(
                		Html::img($file_path,
                		[
                			// 'width' => 150,
						    // 'height' => 150,
						    'id' => $dataProvider->id . '-thumb',
						    'style' => 'max-width: 150px; max-height: 150px;',
						    'class' => 'image-thumb',
                		]), 
                		$file_path, 
                		[
	                		'rel' => 'fancybox'
	                	]
	                );

                },
            ],

            [
            	'attribute' => 'file_name',
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-center'],
                'value' => function($dataProvider) {

                	$file_path = Yii::$app->request->hostInfo.Yii::$app->params['filePath']
                					. $dataProvider->file_img;

                	return Html::a($dataProvider->file_name, $file_path, ['rel' => 'fancybox']);

                }

            ],

            'ext',
            'size',

            [
                'attribute' => 'created_at',
                'format' => 'raw',
                'value' => function($dataProvider) {
                    return date('Y-m-d: H:i:s', $dataProvider->created_at);
                },
            ],

			[
			   'class' => ActionColumn::className(),
			   'buttons' => [

			      	'zip' => function ($url, $model) {

						return Html::a('<span class="fa fa-file"></span>', 
							['image/zip-arhive', 'file_name' => $model->file_img],
							[
						    // 'onclick' => '(function($event) {

					        //     $.post(
							// 		"image/zip-arhive/" + ' .$model->file_img. ', 
							// 		{
							// 			pk : "something"
							// 		},
							// 		function (data) {
							// 			console.log(data);
							// 		}
							// 	);

						    // })();',

							'target' => '_blank',
							'title' => 'Скачать zip',
							// 'data-confirm' => Yii::t('yii', 'Вы уверены что хотите удалить эту запись?'),
							'data-method' => 'post',
							'data-pjax' => '0'
						]);

					}

			    ],
			   'template'=>'{zip}',
			]

        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
