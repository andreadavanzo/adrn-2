<?php

namespace app\controllers;

use yii\web\Controller;

class HelloController extends Controller
{

  public function actionIndex()
  {
    $this->layout = false;  // No layout for the index action
    // Pass "Hello World" to the view
    return $this->render('index', ['helloWorldText' => 'Hello World']);
  }
}
