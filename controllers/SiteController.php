<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\User;
use app\models\RegStepOneForm;
use app\models\RegStepTwoForm;
use app\models\RegStepThreeForm;
use app\models\AnswerQuestions;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    // Вывод главной страницы 
    public function actionIndex()
    {
        return $this->render('index');
    }

    // action for reg
    public function actionRegStepOne()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new RegStepOneForm(); 

        if ($model->load(Yii::$app->request->post())) {
            if(!$model->validate()){
                 print_r($model->errors);
            } 
            if ($model->validate()) {
                $valueArray = array();
                $post = Yii::$app->request->post();
                foreach ($post['RegStepOneForm'] as $key => $value) {
                    $valueArray[$key] = $value; 
                }                           
                // $user = new User();
                // $userCreate = $user->addUser($valueArray);
                //$model = new RegStepTwoForm(); 
                $session = Yii::$app->session;
                $session->open();
                $valueArray = $session->set('stepOneValue', $valueArray);
                $session->close();
                //$session->setFlash('valueArray', $valueArray);
                return $this->redirect(['reg-step-two']);
            }
        }
        return $this->render('regStepOne', [
            'model' => $model,
        ]);
    }

    public function actionRegStepTwo()
    {
       $model = new RegStepTwoForm(); 

       if ($model->load(Yii::$app->request->post())) {
            if(!$model->validate()){
                 print_r($model->errors);
            }

            if ($model->validate()) {
                $stepTwoValue = array();
                $post = Yii::$app->request->post();
                foreach ($post['RegStepTwoForm'] as $key => $value) {
                    $stepTwoValue[$key] = $value; 
                }            
                // $user = new User();
                // $userCreate = $user->addUser($valueArray);
                //$model = new RegStepTwoForm(); 
                $session = Yii::$app->session;

                $session->open();
                $session->set('stepTwoValue', $stepTwoValue);
                $session->close();
                return $this->redirect(['reg-step-three']);
            }
        }
        return $this->render('regStepTwo', [
            'model' => $model,
        ]);
    }

    public function actionRegStepThree()
    {
        $model = new RegStepThreeForm(); 

       if ($model->load(Yii::$app->request->post())) {
            if(!$model->validate()){
                 print_r($model->errors);
            }

            if ($model->validate()) {
                $session = Yii::$app->session;

                $session->open();
                $stepOne = $session->get('stepOneValue');
                $stepTwo = $session->get('stepTwoValue');
                $session->close();
                $result = array_merge ($stepOne, $stepTwo);
                $user = new User();
                $userCreate = $user->addUser($result);
                $session->destroy();
                
                return $this->redirect(['login']);
            }
        }
        
        return $this->render('regStepThree', [
            'model' => $model,
        ]);
    }
    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    // Секция Вопросы-ответы
    public function actionAnswerQuestions ()
    {
        $answerQuestions = AnswerQuestions::getAllAnswerQuestions();
        return $this->render('answerQuestions', compact('answerQuestions'));
    }
}
