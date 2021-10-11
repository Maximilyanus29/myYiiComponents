<?php

namespace frontend\modules\user\controllers;

use common\models\ScoresLog;
use Yii;
use yii\web\Controller;
use frontend\modules\user\models\LoginForm;
use frontend\modules\user\models\PasswordResetRequestForm;
use frontend\modules\user\models\ResetPasswordForm;
use frontend\modules\user\models\SignupForm;
use frontend\modules\user\components\AuthHandler;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use frontend\models\User;
use common\models\Logs;
use frontend\modules\user\models\UpdateUserForm;
use frontend\modules\user\models\ChangePasswordForm;
use yii\web\HttpException;


/**
 * Default controller for the `user` module
 */
class DefaultController extends \frontend\controllers\AppController
{

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'site-settings', 'change-admin-pass', 'sitemap', 'new-pass', 'stat', 'import-export', 'export', 'generate-xml'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }


    public function actionProfile()
    {
        return $this->render('profile');
    }


    public function actionLogin()
    {
        Yii::$app->response->format = 'json';

        $model = new LoginForm();

        $post = Yii::$app->request->post();

        if ($model->load($post) && $model->validate() && $model->login()) {
            return [
                'status' => true,
                'redirect' => '/lk/profile',
            ];
        }

        return [
            'status' => false,
            'content' => $this->renderPartial('login', ['model' => $model]),
        ];
    }


    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect('/');
    }



    public function actionSignup()
    {
        Yii::$app->response->format = 'json';

        $model = new SignupForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->signup();
            return [
                'status' => true,
                'redirect' => '/user/default/profile',
            ];
        }

        return [
            'status' => false,
            'content' => $this->renderPartial('signup', ['model' => $model]),
        ];
    }

    /*Кинуть на емеил восстановление пароля*/
    public function actionRequestPasswordReset()
    {
        Yii::$app->response->format = 'json';

        $model = new PasswordResetRequestForm();

        $post = Yii::$app->request->post();

        if ($model->load($post)&&$model->validate()) {

			$model->sendEmail();

            return [
                'status' => true,
                'content' => 'Вам на почту отправлено письмо с ссылкой на восстановление пароля',
            ];
        }

        return [
            'status' => false,
            'content' => $this->renderPartial('reset-password-request', ['model' => $model]),
        ];
    }

    /*Когда перешли по ссылке восстановления пароля*/
    public function actionResetPassword()
    {
        $token = Yii::$app->request->get('qh');

        $model = new ChangePasswordForm();

        if ($token){
            $user = User::findOne(['password_reset_token'=> $token]);
        }else{
            $user = Yii::$app->user->identity;

            $post = Yii::$app->request->post();

            if ($model->load($post) && $model->validate()){
                $user->setPassword($model->password);

                if ($user->save(false)){
                    return $this->redirect('/user/default/profile');
                }
            }
        }


        if (!empty($user)){
            Yii::$app->user->login($user);
            return $this->render('change_password', ['model'=> $model, 'user_id' => $token]);
        }else{
            throw new HttpException(404);
        }
    }

    /*Изменение пароля в личном кабинете*/
    public function actionChangePassword()
    {
        $post = Yii::$app->request->post();

        $user = User::findOne(Yii::$app->user->getId());

        $form = new ChangePasswordForm();

        if ($form->load($post) && $form->validate()) {
            $user->setPassword($form->password);
            $user->save();
        } else {
            $errors = $form->getErrors();
        }

        return $this->redirect('/user/default/profile');
    }


    /*Повторить заказ*/
    public function actionOrderRepeat($id) {
        $modelOrder = \common\models\Order::findOne($id);
        if (!empty($modelOrder)) {
            $goods = $modelOrder->orderGoods;
            foreach ($goods as $good) {
                Yii::$app->cart->add($good->goods_id, $good->count);
            }
        }
        return $this->redirect(['/cart']);
    }



}
