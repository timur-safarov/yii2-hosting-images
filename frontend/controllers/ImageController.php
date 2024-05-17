<?php

namespace frontend\controllers;

use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\helpers\Json;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use common\models\Image;
use yii\web\NotFoundHttpException;

use common\helpers\Common;
use Defuse\Crypto\Key;
use Defuse\Crypto\Crypto;


/**
 * Site controller
 */
class ImageController extends Controller
{




    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['upload'],
                'rules' => [
                    [
                        'actions' => ['upload'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            // 'verbs' => [
            //     'class' => VerbFilter::class,
            //     'actions' => [
            //         'logout' => ['post'],
            //     ],
            // ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {

        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
            'captcha' => [
                'class' => \yii\captcha\CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Загрузка файлов
     *
     * @return mixed
     */
    public function actionUpload()
    {

        $mImage = new Image;

        // $data = [
        //     'file_name' => 'test',
        //     'size' => 34535353,
        //     'ext' => 'png'
        // ];
        // $mImage->create($data);
        // die;

        return $this->render('upload', [
            'mImage' => $mImage
        ]);
    }

    public function actionCatalog($page)
    {

        echo 'Catalog';
        die;

    }


    /**
     * Экшен загружает и выводит данные по загружанным файлам
     * Обращаемся к нему по Ajax
     */
    public function actionUploadUrlAjax()
    {

        if (Yii::$app->request->isAjax && isset(Yii::$app->request->post()['created_at'])) {

            // sleep(3);

            $created_at = Yii::$app->request->post()['created_at'];

            // Загружаем файлы
            $json = (new Image)->jsonUploadFile($created_at);

            //Раскоментируем чтобы сохранить данные для просмотра того что было в Post
            // file_put_contents(Image::getFileFolder($created_at).'check.txt', serialize($_POST['fileName']),);

            // file_put_contents(
            //     Image::getFileFolder($created_at).'post.log',
            //     print_r($_FILES['fileBlob']['name'], true). "\n\r",
            //     FILE_APPEND
            // );

            echo Json::encode($json);

        } else {

            //Иначе скажем что страница не найдена
            throw new NotFoundHttpException('Not found');

        }

    }


    /**
     * Удаление одного файла
     * /image/delete-url-ajax/<id>
     * 
     * При успехе возвращает Json
     * 
     */
    public function actionDeleteUrlAjax($id)
    {

        if (Yii::$app->request->isAjax) {

            $json = (new Image)->jsonDeleteFile(id_file: $id);

            echo Json::encode($json);

        } else {
            //Иначе скажем что страница не найдена
            throw new NotFoundHttpException('Страница не найдена.');
        }

    }


    /**
     * Удаление одного файла
     * /image/delete-bunch-ajax
     * 
     * При успехе возвращает Json
     * 
     */
    public function actionDeleteBunchAjax()
    {

        if (Yii::$app->request->isAjax && isset(Yii::$app->request->post()['id_list'])) {

            $id_list = Yii::$app->request->post()['id_list'];

            // file_put_contents(
            //     'post.log',
            //     print_r($_POST, true). "\n\r",
            //     FILE_APPEND
            // );

            // echo 'Hi there';
            // die;

            $json = [];

            foreach ($id_list as $key => $arr) {
                $id = $arr['id'];
                $data = (new Image)->jsonDeleteFile(id_file: $id);
                $json[] = $data;
            }

            echo Json::encode($json);

        } else {
            //Иначе скажем что страница не найдена
            throw new NotFoundHttpException('Страница не найдена.');
        }

    }


}
