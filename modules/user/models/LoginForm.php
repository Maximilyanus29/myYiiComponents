<?php
namespace frontend\modules\user\models;

use Yii;
use yii\base\Model;
use frontend\models\User;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $email;
    public $password;
    public $rememberMe = true;

    private $_user;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['email', 'password'], 'required'],

            ['email', 'email'],
//            // rememberMe must be a boolean value
//            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }



    public function attributeLabels() {
        return [
            'username' => 'Логин',
            'rememberMe' => 'Запомнить пароль',
            'password' => 'Пароль'
        ];
    }




    public function validatePassword($attribute, $params) {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Не верный логин или пароль');
            }
        }
    }



    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login() {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByEmailFrontend($this->email);
        }

        return $this->_user;
    }
}