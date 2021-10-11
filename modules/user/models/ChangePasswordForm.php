<?php
namespace frontend\modules\user\models;

use Yii;
use yii\base\Model;
use yii\base\InvalidParamException;
use frontend\models\User;

/**
 * Password reset form
 */
class ChangePasswordForm extends Model
{
    public $password;
    public $password_repeat;

    /**
     * Creates a form model given a token.
     *
     * @param string $token
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($config = [])
    {
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['password', 'password_repeat'], 'required'],
            [['password','password_repeat'], 'string', 'min' => 6 ],
            [['password_repeat'], 'compare', 'compareAttribute' => 'password'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'password' => 'Пароль',
            'password_repeat' => 'Повтор пароля',
        ];
    }

    /**
     * Resets password.
     *
     * @return bool if password was reset.
     */
    public function changePassword()
    {
        $user = Yii::$app->user->identity;

        $user->setPassword( $this->password );
        $user->removePasswordResetToken();

        return $user->save( false );
    }
}
