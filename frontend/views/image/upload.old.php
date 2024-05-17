<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
# use yii\bootstrap4\ActiveForm;
# use yii\widgets\ActiveForm;
# use yii\bootstrap4\Modal;
use yii\bootstrap5\ActiveForm;
use yii\widgets\Pjax;
use yii\jui\JuiAsset;
use yii\web\JsExpression;
use kartik\file\FileInput;
# use kartik\widgets\FileInput;

use yii\helpers\FileHelper;


$this->title = 'Загрузка картинок';
$this->params['breadcrumbs'][] = $this->title;

//Подгружаем иконки Awesome 
// use kartik\icons\FontAwesomeAsset;
// FontAwesomeAsset::register($this);

$this->registerCss('

    /*Стили для виджета Fileinput*/
    .krajee-default.file-preview-frame {
        width: calc(20% - 16px);
        height: auto !important;
        /*box-sizing: border-box;*/
        padding: 0;
    }

    .krajee-default.file-preview-frame .kv-file-content {
        width: 100%;
    }

');


// Yii::setAlias('@web', Url::base(true));

// exec("mkdir -p /var/www/banki.local/public/frontend/web/temp/");

?>

<div class="image-form">

    <?php $form = ActiveForm::begin([
        'id' => 'image-form',
        'options' => [
            'class' => 'upload-image row m-0',
            'enctype' => 'multipart/form-data', 
            ['data-pjax' => true]
        ],

    ]); ?>

    <?php
        $this->registerJs(
            '$("document").ready(function(){

            });'
        );
    ?>

    <div class="col-sm-12 p-0">

        <div class="panel panel-default card border-primary mb-3">

            <!---------- Форма начало ------->

            <div class="panel-heading card-header bg-primary text-white">
                <h4>
                    <i class="glyphicon glyphicon-paperclip"></i> Загрузка картинок
                </h4>
            </div>


            <div class="panel-body">


                <div class="container-products card-body text-primary"><!-- widgetContainer -->


                        <div class="item panel panel-default bid-product"><!-- widgetBody -->

                            <div class="row panel-body m-0">

                                <div class="col-sm-12">

                                    <!---------- Загрузка файлов начало ------->

                                    <div class="panel panel-default card border-primary mb-3">

                                        <div class="panel-body scroll-preview">

                                            <div class="card-body text-primary">

                                                <?=$form->field($mImage, "file_name[]")->widget(FileInput::classname(), [
                                                    'language' => substr(\Yii::$app->language, 0, 2),
                                                    'options'=>[
                                                        'multiple' => true,
                                                    ],
                                                    //https://plugins.krajee.com/file-input
                                                    'pluginOptions' => [
                                                        'msgPlaceholder' => 'Выберете файлы',

                                                        'msgFilesTooMany' => 'Колличество файлов <b>({n})</b> превышает максимальное <b>{m}</b>.',
                                                        'msgTotalFilesTooMany' => 'Вы можете загрузить максимум <b>{m}</b> файлов (<b>{n}</b>).',
                                                        'msgNoFilesSelected' => 'Нету выбранных файлов',
                                                        'msgResumableUploadRetriesExceeded' => 'Загрузка прервана <b>{max}</b> попыток для файла <b>{file}</b>! Ошибка: <pre>{error}</pre>',

                                                        'msgUploadEmpty' => 'Данные не валидны.',

                                                        'resumableUploadOptions' => [
                                                            'fallback' => null,
                                                            'testUrl' => null,
                                                            'chunkSize' => 2048, // in KB
                                                            'maxThreads' => 4,
                                                            'maxRetries' => '1',
                                                            'showErrorLog' => true,
                                                            'retainErrorHistory' => true,
                                                            'skipErrorsAndProceed' => false // when set to true, files with errors will be skipped and upload will continue with other files
                                                        ],

                                                        'elPreviewStatus' => false,
                                                        'preProcessUpload' => true,
                                                        'dropZoneEnabled' => true,
                                                        'dropZoneTitle' => 'Загрузить?',
                                                        'dropZoneClickTitle' => '', 
                                                        'maxFileCount' => Yii::$app->params['maxFileCount'],
                                                        'maxFileSize' => Yii::$app->params['maxSize'],
                                                        'browseLabel' => 'Выбрать файлы',
                                                        'uploadLabel' => 'Загрузить файлы',
                                                        'removeLabel' => 'Удалить файлы',
                                                        'elCaptionText' => '',

                                                        // 'fileActionSettings' => [
                                                        //     // 'browseClass' => 'btn btn-success',
                                                        //     // 'browseIcon' => '<i class="glyphicon glyphicon-camera"></i>',
                                                        //     // 'uploadClass' => 'btn btn-info',
                                                        //     // 'uploadIcon' => '',
                                                        //     // 'removeClass' => 'btn btn-danger',
                                                        //     // 'removeIcon' => '<i class="glyphicon glyphicon-trash"></i>',
                                                        //     // 'removeTitle' => '',
                                                        //     // 'downloadClass' => '',
                                                        //     // 'downloadIcon' => '',
                                                        //     // 'downloadTitle' => '',
                                                        //     // 'downloadUrl' => '',
                                                        // ],
                                                        'browseOnZoneClick' => true,
                                                        'showPreview' => true,
                                                        'showCaption' => true,
                                                        'showRemove' => true,
                                                        'showUpload' => true,
                                                        'showDownload' => true,
                                                        'showClose' => false,
                                                        'showCancel' => false,
                                                        'previewFileType' => 'any',
                                                        'retainErrorHistory' => false,
                                                        //'encodeUrl' => false,
                                                        'uploadUrl' => Url::to(['/image/upload-url-ajax']),
                                                        'deleteUrl' => Url::to(['/image/delete-url-ajax']),
                                                        'initialPreviewConfig' => [],
                                                        'initialPreview' => [],
                                                        'initialPreviewAsData' => true,
                                                        'showUploadedThumbs' => true,
                                                        'overwriteInitial' => false,
                                                        'validateInitialCount' => false,
                                                        'enableResumableUpload' => true,
                                                        // 'allowedFileExtensions' => ['jpg', 'png', 'gif', 'jpeg', 'ico', 'bmp'],

                                                        //'fileTypeSettings' => '',
                                                        'allowedFileTypes' => ['image'],
                                                        'uploadExtraData' => [
                                                            //'uploadToken' => Yii::$app->request->getCsrfToken(),
                                                        ],

                                                        'previewSettings' => [
                                                            'image' => [
                                                                'width' => 'auto',
                                                                'height' => 'auto',
                                                                'max-width' => 'auto',
                                                                'max-height' => 'auto'
                                                            ],
                                                            'removeFromPreviewOnError' => true,
                                                        ],
                                                        'pluginLoading' => true,

                                                        //previewTemplates => [],

                                                        'layoutTemplates' => [
                                                            //'main1' => '',
                                                            //'main2' => '{preview}',
                                                            //'preview' => '',

                                                            'actions' => '<div class="file-actions">' .
                                                                        '    <div class="file-footer-buttons">' .
                                                                        '     {delete} {upload} {download} {zoom} {other}' .
                                                                        '    </div>' .
                                                                        '    <div class="clearfix"></div>' .
                                                                        '</div>',
                                                        ],
                                                    ],

                                                    //https://plugins.krajee.com/file-input/plugin-methods
                                                    //https://plugins.krajee.com/file-input/plugin-events
                                                    'pluginEvents' => [

                                                        'filebrowse' => 'function(event){
                                                            //console.log("filebrowse");
                                                        }'

                                                    ]
                                                ])->label(false);

                                                ?>
                             
                                            </div>

                                        </div>

                                    </div>

                                    <!---------- Загрузка файлов конец ------->

                                </div>



                            </div>

                        </div>


                </div>


            </div>

            <!--------- Форма конец ------------>

            <div class="row m-0">

                <div class="form-group col-sm-6">
                    <?= Html::submitButton('Сохранить файлы', [
                        'value' => 1,
                        'name' => 'submit',
                        'class' => 'btn btn-primary'
                    ]); ?>
                </div>

            </div>
    

        </div>

    </div>


    <?php ActiveForm::end(); ?>

</div>