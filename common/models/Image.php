<?php

namespace common\models;

use Yii;
use yii\web\UploadedFile;
use yii\web\JsExpression;
use common\helpers\Common;
use yii\helpers\FileHelper;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\base\InvalidArgumentException;



/**
 * This is the model class for table "image".
 *
 * @property int $id id Картинки
 * @property string $file_name Название
 * @property string $ext Тип файла
 * @property string $created_at Дата создания
 */
class Image extends \yii\db\ActiveRecord
{

    public $file_img;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        // return 'image';
        return '{{%image}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['file_name', 'ext', 'size', 'created_at'], 'required'],
            [['created_at'], 'safe'],
            [['size', 'created_at'], 'integer'],
            [['file_name'], 'string', 'max' => 32],
            [['ext'], 'string', 'max' => 4],
            [
                ['file_name'],
                'file',
                'skipOnEmpty' => true,
                'extensions' => implode(',', Yii::$app->params['allowedFileExtensions']),
                'maxSize' => Yii::$app->params['maxFileSize'],
                'maxFiles' => Yii::$app->params['maxFileCount'],
                //если мы хотим чтобы файлы отправлялись только после сохранения в виджете
                //fileInput тогда пишем tooMany
                'tooMany' => 'С начала загрузите файлы!'
            ],
            [['file_name', 'ext'], 'unique', 'targetAttribute' => ['file_name', 'ext']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'id Картинки',
            'file_name' => 'Название',
            'ext' => 'Тип файла',
            'size' => 'Размер файла',
            'created_at' => 'Дата создания',
            'file_img' => 'Картинка'
        ];
    }

    /**
     * Метод записи данных в модель
     * 
     */
    public function create(array $dataImage)
    {

        $mImage = $this;

        //$valid = Model::validateMultiple($mImages) && $valid;

        // $mImage->load($dataImage, '');
        // $mImage->validate();
        // print_r($mImage->getErrors());


        if ($mImage->load($dataImage, '') && $mImage->validate()) {

            $transaction = Yii::$app->db->beginTransaction();

            try {

                if ($mImage->save(false)) {

                    $transaction->commit();

                    // Возвращаем модель чтобы мы могли вывести данные
                    return $mImage;

                } else {
                    throw new \Exception('Данные не были обновлены для модели Image.');
                }

            } catch (Exception $e) {

                // Показываем ошибку на клиенте
                Yii::$app->getSession()->setFlash('error', $e->getMessage());

                $file_path = self::getTempFolder() . $dataImage['file_name'] . '.' . $dataImage['ext'];

                // Если файл не был записан в базу, тогда удаляем его
                if (file_exists($file_path)) {
                    FileHelper::unlink($file_path);
                }

                $transaction->rollBack();
            }

        }

    }


    public static function setUniqueFileName($file_name, $ext, $created_at): string
    {

        $file_name = Common::rusTranslit($file_name);

        $mImage = self::find()->where([
            'file_name' => $file_name,
            'ext' => $ext,
        ])->andWhere([
            '!=', 'created_at', $created_at
        ])->one();

        if ($mImage) {
            // Добавим сахара в конец названия, если такой файл уже есть
            $file_name = substr($file_name, 0, 26).'_'.substr(md5($created_at), strlen(md5($created_at)) - 5);;

            // Запускаем по кругу чтобы файл гарантировано был с уникальным именем
            // return self::setUniqueFileName($file_name, $ext, $created_at);
        }
        
        return $file_name;

    }


    /**
     * Получаем папку которая нужна для хранения временных файлов
     * для поставщика с id = $id_user
     * 
     * @return string
     */
    public static function getFileFolder(): string
    {
        // Если нужно чтобы файлы загружались всегда в новую папку
        // То дабавляем сюда в конец название папки со слэшем на конце
        return Yii::getAlias('@webroot'.Yii::$app->params['filePath']);
    }


    /**
     * Загрузка файлов после Drag And Drop или просто выбора файлов
     * в виджете FileInput
     * 
     * Вместо created_at можно создавать папку для каждой сессии загрузок 
     * и тогда каждая загрузка будет загружаться в отдельную папку
     * @param int $created_at - дата загрузки для текущей сессии
     * @param int $id_list - список id файлов, которые мы загрузили в текущей сессии
     * 
     * @return array
     */
    public function jsonUploadFile(int $created_at): array
    {

        $preview = $config = [];
        $error = false;

        //Это значения поля input которое передаёт виджет FileInput
        $input = 'fileBlob';
        $inputFile = UploadedFile::getInstanceByName($input);

        // Make sure we have a file path
        if (isset($inputFile->extension)) {

            $ext = strtolower($inputFile->extension);

            // Папка куда будем сохранть файл
            $path = self::getFileFolder();
            
            // Если нужно чтобы файлы загружались всегда в новую папку
            // То дабавляем сюда в конец название папки со слэшем на конце
            $url_folder = Yii::$app->request->hostInfo.Yii::$app->params['filePath'];
            $url_delete = Yii::$app->request->hostInfo.'/image/delete-url-ajax/';
            
            if (!file_exists($path)) {
                FileHelper::createDirectory($path);
            }

            // Setup our new file path
            // $newFilePath  = $path . $inputFile->name;

            // Генерируем уникальное имя файла
            // pathinfo обрезает расширение у файла вместе с точкой
            $file_name = self::setUniqueFileName(
                pathinfo($inputFile->name, PATHINFO_FILENAME),
                $ext,
                $created_at
            );

            // Если каждая сессия будет сохранться в новой папке, то генерировать имена не нужно
            // Просто транслитеруем имя файла в нужный формат
            // $file_name = Common::rusTranslit(pathinfo($inputFile->name, PATHINFO_FILENAME));

            // Имя файла в массиве $_FILES
            // $file_name = $_FILES['fileBlob']['name'];

            // Будущий путь к файлу
            $newFilePath  = $path . $file_name . '.' . $ext;

            $newFileUrl  = $url_folder . $file_name . '.' . $ext;
            $type = Common::fileType($inputFile->name);


            // Обязательно делаем проверку, есть ли такой файл так 
            // Так как FileInput отправляет файлы по 3 раза каждый
            if (!file_exists($newFilePath)) {

                // Проверка параметров
                // $arr = [
                //     "Upload: " => $_FILES["fileBlob"]["name"] . "<br>",
                //     "Type: " => $_FILES["fileBlob"]["type"] . "<br>",
                //     "Size: " => ($_FILES["fileBlob"]["size"] / 1024) . " kB<br>",
                //     "Temp file: " => $_FILES["fileBlob"]["tmp_name"] . "<br>"
                // ];

                // file_put_contents(
                //     'post.log',
                //     print_r($arr, true) . "\n\r",
                //     FILE_APPEND
                // );
                

                // Виджет FileInput сам проверяет есть ли такой файл в списке файлов
                // Поэтому мы можем не проверять есть ли он уже или нет
                // и сразу сохранять
                // if ($inputFile->saveAs($newFilePath))
                if (!move_uploaded_file($_FILES['fileBlob']['tmp_name'], $newFilePath)) {
                    return [
                        'error' => 'Файл - ' . $inputFile->name . ' не удалось загрузить.'
                    ];
                }

                // Ставим права на файлы и папки
                // Чтобы их легко можно было удалить или переместить
                Common::rChmodDir($path);

            }        

            $mImage = self::find()->where([
                'file_name' => $file_name,
                'ext' => $ext,
                'created_at' => $created_at
            ])->one();

            if (!$mImage) {

                // Данные для сохранения в модель Image
                $dataImage = [
                    'file_name' => $file_name,
                    'ext' => $ext,
                    'size' => $inputFile->size,
                    'created_at' => $created_at
                ];

                // Сохраним файл в базе
                $mImage = $this->create($dataImage);

                if (!$mImage instanceof $this) {
                    return [
                        'error' => 'Файл - ' . $inputFile->name . ' не удалось сохранить в базе данных.'
                    ];
                }

            }

            // $dataFiles = Bid::getDataFiles($id_user, $id_bid);
            // $config = $dataFiles['config'];
            // $preview = $dataFiles['url_file'];

            // Выше в методе getDataFiles мы добавляем примерно эти данные для каждого файла
            // но так работает более корректно
            $fileId  = $mImage->id;
            $preview[] = $newFileUrl;
            $config[] = [
                'type' => $type, 
                'key' => $fileId,
                'caption' => $inputFile->name,
                'width' => '90px',
                'size' => $inputFile->size,
                // the url to download the file
                'downloadUrl' => $newFileUrl,
                // server api to delete the file based on key
                //'url' => $url_delete.urlencode($inputFile->name),
                'url' => $url_delete . $mImage->id,
                'extra' => [
                    // Доп данные если нужно
                ]
            ];

        } else {
            $error = 'Файлов не было выбрано для загрузки.';
        }

        $out = [
            'initialPreview' => $preview,
            'initialPreviewConfig' => $config, 
            'initialPreviewAsData' => true,
            // 'append' => true
        ];

        if ($error) {
            $out['error'] = $error;
        }

        return $out;
    }


    /**
     * Удаление файла
     * 
     * @param int $id_file
     * 
     */
    public function jsonDeleteFile(int $id_file)
    {
        // Формируем json для ответа
        $out = [];

        //Значение key передаёт виджет FileInput через Ajax
        // $out['key'] = Yii::$app->request->post()['key'];
        $out['key'] = $id_file;

        $mImage = self::findOne($id_file);

        $transaction = Yii::$app->db->beginTransaction();

        try {

            if ($mImage) {

                // Удалим файл из бд если он там есть
                if (!$mImage->delete()) {
                    throw new \Exception('Файл не был удалён с базы данных.');
                }

                $path = self::getFileFolder();

                $file_path = $path . $mImage->file_name . '.' . $mImage->ext;

                if (file_exists($file_path)) {

                    // Проверяем удалился файл или нет
                    if (!FileHelper::unlink($file_path)) {
                        throw new \Exception('Файл не был удалён с сервера.');
                    }

                    //Если нету файлов в папке - удаяем папку
                    // Так как у нас все файлы в одной папке то и удалять её не нужно
                    // if (!FileHelper::findFiles($path)) {
                    //     // удаляем папку
                    //     FileHelper::removeDirectory($path);
                    // }

                }

                $transaction->commit();
                return $out;
         
            } else {
                throw new \Exception('Файла нету в базе данных.');
            }

        } catch (Exception $e) {

            // Показываем ошибку на клиенте
            Yii::$app->getSession()->setFlash('error', $e->getMessage());

            $transaction->rollBack();

            return [
                'error' => $e->getMessage()
            ];

        }


    }



}
