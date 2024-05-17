<?php

namespace frontend\controllers;

use Yii;
use yii\rest\ActiveController;

class ApiUserController extends ActiveController
{
    public $modelClass = 'common\models\User';
}