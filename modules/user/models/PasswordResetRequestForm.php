<?php
namespace frontend\modules\user\models;


use Yii;
use yii\base\Model;
use frontend\models\User;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\frontend\models\User',
                'message' => 'Нет пользователя с таким адресом электронной почты .'
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne(['email' => $this->email]);

        if (!$user) {
            return false;
        }


        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
            $user->save(false);

            if (!$user->save(false)) {
                return false;
            }
        }

        return Yii::$app->mailer->compose(
                ['html' => 'password-reset-message-html'],
                ['user' => $user]
            )
            ->setFrom([ Yii::$app->params['adminEmail'] => Yii::$app->name . ' robot'])
            ->setTo($user->email)
            ->setSubject('Восстановление пароля')
            ->send();
    }
}
