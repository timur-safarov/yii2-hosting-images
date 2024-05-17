<?php

namespace frontend\controllers;

use Yii;
use yii\rest\ActiveController;

class ApiImageController extends ActiveController
{
    public $modelClass = 'common\models\Image';
}