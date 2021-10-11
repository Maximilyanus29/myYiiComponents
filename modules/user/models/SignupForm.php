<?php
namespace frontend\modules\user\models;

use Yii;
use yii\base\Model;
use frontend\models\User;
use yii\db\Exception;
use yii\web\HttpException;

/**
 * Signup form
 */
class SignupForm extends Model
{
    

    public $email;
    public $password;
    public $name;
    public $phone;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'email'],
            [['email', 'name', 'password'], 'required'],
            ['email', 'unique', 'targetClass' => '\frontend\models\User', 'message' => 'Этот email уже занят. Если вы забыли пароль, воспользуйтесь функцией восстановления пароля'],
            ['email', 'string', 'min' => 2, 'max' => 254],
            ['password', 'string', 'min' => 6],
            [['name'], 'string', 'max' => 254],
            ['phone', 'string', 'max' => 20],    

        ];
    }



    public function attributeLabels() {
        return [
            'name' => 'Ф.И.О',
            'phone' => 'Телефон',
            'email' => 'E-mail (Ваш логин)',
            'password' => 'Пароль',
        ];
    }


    public function signup()
    {
        $user = $this->saveUser($this->password);
        Yii::$app->user->login($user);
        return true;
    }


    public function signupBeforeOrderCheckout($name, $email, $phone, $address)
    {
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->address = $address;

        return $this->saveUser(Yii::$app->security->generateRandomString(8));
    }


    public function saveUser($pwd)
    {
        $transaction = Yii::$app->db->beginTransaction();

        $user = new User();
        $user->name = $this->name;
        $user->email = $this->email;
        $user->setPassword($pwd);
        $user->generateAuthKey();
        $user->status = 5;
        $user->phone = $this->phone;
        $user->score = 100;

        if ($user->save()){
            $transaction->commit();
            return $user;
        }else{
            $transaction->rollBack();
            throw new HttpException(404);
        }
    }


    private function sendEmail()
    {
        $user = User::findOne([
            'status' => User::STATUS_FRONT,
            'email' => $this->email,
        ]);

        return Yii::$app
            ->mailer
            ->compose(['html' => 'signup-html'], ['user' => $user])
            ->setFrom([Yii::$app->params['admin_email'] => Yii::$app->name])
            ->setTo($this->email)
            ->setSubject('Регистрация')
            ->send();
    }
}