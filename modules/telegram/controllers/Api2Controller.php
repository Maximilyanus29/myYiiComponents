<?php

namespace frontend\modules\telegram\controllers;

use common\models\Article;
use common\models\goods\Good;
use frontend\models\User;
use common\models\TelegramUser;
use Longman\TelegramBot\Telegram;
use Yii;
use yii\web\Controller;

/**
 * Default controller for the `user` module
 */
class Api2Controller extends Controller
{
    // Обязательное отключение csrf в данном контроллере
    public $enableCsrfValidation = false;

    public $text;
    public $username;
    public $user;
    public $userModel;
    public $message;
    public $message_id;
    public $chat_id;
    public $telegramApi;



    // Клавиатура после авторизации
    const LOGOUT_KEYBOARD = [
        [
            ['text' => "💼 Мои заказы"],
            ['text' => "🔥 Акции"],
        ],
        [
            ['text' => "📰 Новости"],
            ['text' => "❓ Помощь"],
            ['text' => "🚶🏻‍♂️ Выход"],
        ],
    ];

    // Клавиатура без авторизации
    const LOGIN_KEYBOARD = [
        [
            ['text' => "🚪 Авторизация"],
        ],
        [
            ['text' => "📰 Новости"],
            ['text' => "❓ Помощь"],
        ],
    ];



    public function actionIndex()
    {
        $content = file_get_contents("php://input");

        $update = json_decode($content, true);

        if (!$update) {
            // receive wrong update, must not happen
            exit;
        }

        if (isset($update["message"])) {
            $this->handle($update["message"]);
        }
    }


    private function handle($message)
    {
        $telegramApi = new  \frontend\modules\telegram\components\TelegramApi;

        $this->message = $message;
        $this->username = $message['from']['username'];
        $this->message_id = $message['message_id'];
        $this->chat_id = $message['chat']['id'];
        $this->telegramApi = $telegramApi;


        if (isset($message['text'])) {
            $this->text = $message['text'];

            $this->user = TelegramUser::findOne(['telegram_id' => $this->chat_id]);



            /*standart*/
            $this->loginHandler();
            $this->logoutHandler();
            $this->startHandler();


            /*custom*/

            $this->helpHandler();
            $this->getActionsHandler();
            $this->getNewsHandler();
            $this->getOrdersHandler();
            $this->goodCallbackHandler();

        }
    }

    private function logoutHandler()
    {
        if ($this->text === "/logout") {
            $this->user->is_login = 0;
            $this->user->save(false);
            $this->telegramApi->apiRequestJson("sendMessage",
                array(
                    'chat_id' => $this->chat_id,
                    "text" => 'Вы вышли',
                )
            );
        }
    }

    private function loginHandler()
    {
//telegram_code_for_auth
//telegram_login_mode
//telegram_user_name
//telegram_is_login


        if (!$this->user) {
            $this->user = new TelegramUser;
            $this->user->name = $this->username;
            $this->user->auth_mode = 1;
            $this->user->telegram_id = $this->chat_id;
            $this->user->is_login = 0;
            $this->user->save();
        }


        /*Если мы в режиме ввода пароля*/
        if ($this->user->auth_mode === 1) {
            /*Ищем юзера по коду который он ввел*/
            if ($this->userModel = User::findOne(['telegram_code_for_auth' => $this->text])) {
                $this->user->is_login = $this->userModel->id;
                $this->telegramApi->apiRequestJson("sendMessage",
                    array(
                        'chat_id' => $this->chat_id,
                        "text" => 'Привет',
                        'reply_markup' => array(
                            'keyboard' => self::LOGOUT_KEYBOARD,
                            'one_time_keyboard' => true,
                            'resize_keyboard' => true
                        )
                    )
                );
            } else {
                $this->telegramApi->apiRequestJson("sendMessage",
                    array(
                        'chat_id' => $this->chat_id,
                        "text" => 'Не верный пароль',
                    )
                );
            }
            $this->user->auth_mode = 0;
            $this->user->save(false);
        }else{
            if ($this->text === "🚪 Авторизация") {

                $this->user->auth_mode = 1;
                $this->user->save(false);

                return $this->telegramApi->apiRequestJson("sendMessage",
                    array(
                        'chat_id' => $this->chat_id,
                        "text" => 'Введите пароль',
                    )
                );
            }
        }
    }

    private function startHandler()
    {
        $keyBoard = self::LOGIN_KEYBOARD;

        if ($this->isLogin()){
            $keyBoard = self::LOGOUT_KEYBOARD;
        }

        if (strpos($this->text, "/start") === 0) {
            $this->telegramApi->apiRequestJson("sendMessage",
                array(
                    'chat_id' => $this->chat_id,
                    "text" => 'Привет это бот strongman, нажмите help для просмотра доступных комманд',
                    'reply_markup' => array(
                        'keyboard' => $keyBoard,
                        'one_time_keyboard' => true,
                        'resize_keyboard' => true
                    )
                )
            );
        }
    }

    private function isLogin()
    {
        return $this->user->is_login !== 0;
    }

    /*custom methods*/

    public function helpHandler()
    {
        if (strpos($this->text, "/help") === 0 || strpos($this->text, "❓ Помощь") === 0 ) {

            $text_help = '<b>Список доступных команд:</b>' .
                "\n" . '/help - просмотр команд' .
                "\n" . '/news - последние новости сайта' .
                "\n" . '/auth - авторизация на сайте';

            if ($this->isLogin()) {
                $text_help = 'Список доступных команд:' .
                    "\n" . '/help - просмотр команд' .
                    "\n" . '/orders - просмотр заказов' .
                    "\n" . '/discounts - акционные предложения' .
                    "\n" . '/news - последние новости сайта' .
                    "\n" . '/logout - отменить авторизацию';
            }

            $text_help .= "\n \n 🗺 Адрес магазина: \n".'<a href="https://yandex.ru/maps/193/voronezh/house/kholmistaya_ulitsa_68/Z0AYdwViS0cDQFtrfXp2cH5qYA==/?ll=39.122224%2C51.671294&z=16.94">Воронеж, ул. Холмистая, 68</a>'."\n\n";


            $this->telegramApi->apiRequestJson("sendMessage",
                [
                    'chat_id' => $this->chat_id,
                    'parse_mode' => 'HTML',
                    'text' => $text_help,
                    'reply_markup' => json_encode([
                        'inline_keyboard' => [
                            [
                                ['text' => 'Служба поддержки', 'url' => 'https://t.me/Max0nsky']
                            ]
                        ],
                    ]),
                ]
            );
        }
    }

//    private function getActionsHandler()
//    {
//        if (strpos($this->text, "🔥 Акции") === 0) {
//
//            $this->telegramApi->apiRequestJson("sendMessage",
//                array(
//                    'chat_id' => $this->chat_id,
//                    "text" => 'asf',
//                    'reply_markup' => array(
//                        'keyboard' => self::LOGOUT_KEYBOARD,
//                        'one_time_keyboard' => true,
//                        'resize_keyboard' => true
//                    )
//                )
//            );
//        }
//    }

    public function getActionsHandler()
    {
        if (strpos($this->text, "🔥 Акции") === 0) {

            $goods = Good::find()->where(['is_delete' => 0])->offset($this->user->news_pagination_page * 5)->limit(5)->all();

            if (!empty($goods)) {
                $str = "🔥 <b>Несколько специальных предложений:</b> \n";
                $keyboard_goods = [];
                $i = 0;
                foreach ($goods as $good) {
                    $i++;
                    $str .= "Предложение #" . $i . " \n ⚡️" . $good->name . "\n";
                    $str .= "Новая цена - " . $good->price . "₽, старая - <s>" . $good->discount_price . "</s>₽ \n \n";
                    $keyboard_goods[0][] = ['text' => $i . " 🔍", 'callback_data' => '/good ' . $good->id];
                }

                $keyboard_goods[1][] = ['text' => 'Все акционные предложения', 'url' => 'https://vinlam.ru/'];

                $this->telegramApi->apiRequestJson("sendMessage",
                    [
                        'chat_id' => $this->chat_id,
                        'text' => $str,
                        'parse_mode' => 'HTML',
                        'reply_markup' => json_encode([
                            'inline_keyboard' => $keyboard_goods,
                        ]),
                    ]
                );

            } else {

                $this->telegramApi->apiRequestJson("sendMessage",
                    [
                        'chat_id' => $this->chat_id,
                        'text' => 'На данный момент акций нет, попробуйте позже.',
                        'parse_mode' => 'HTML',
                    ]
                );

            }

        }


    }

    private function getNewsHandler()
    {
        if (strpos($this->text, "📰 Новости") === 0) {

            $articlePrev = Article::find()->where(['is_delete' => 0])->orderBy(['id' => SORT_DESC])->one();

            if (!empty($articlePrev)) {
                $pathImg = 'https://strongmanpro.ru' . str_replace("../..", "", $articlePrev->getImage()->getPath('x300'));

                $articles = Article::find()->where(['is_delete' => 0])->orderBy(['id' => SORT_DESC])->limit(5)->all();
                $str_articles = "";
                if ($articles > 1) {
                    foreach ($articles as $article) {
                        $str_articles .= '<a href="https://strongmanpro.ru/article/' . $article->slug . '">▫️' .  $article->name . '</a>' . "\n";
                    }
                }


                $this->telegramApi->apiRequestJson("sendPhoto",
                    [
                        'chat_id' => $this->chat_id,
                        'photo' => $pathImg,
                        'parse_mode' => 'HTML',
                        'caption' => 'Последняя новость на сайте: ' . ' от ' . date("d.m.y", $articlePrev->created_at) . "\n \n"
                            . '<b>"' . $articlePrev->name . '"</b>' . "\n"
                            . $articlePrev->short_text . " \n"
                            . 'https://strongmanpro.ru/article/' . $articlePrev->slug . "\n \n"
                            . 'Также смотрите:' . "\n"
                            . $str_articles,
                        'reply_markup' => json_encode([
                            'resize_keyboard' => true,
                            'keyboard' => self::LOGOUT_KEYBOARD,
                        ])
                    ]
                );

            }


        }

    }

    private function getOrdersHandler()
    {

    }

    public function goodCallbackHandler()
    {
        if (strpos($this->text, "/good") === 0) {
            $good_id = trim(str_replace('/good', '', $this->data));
            $good = Good::find()->where(['id' => (int)$good_id])->one();

            if (!empty($good)) {
                $pathImg = 'https://vinlam.ru' . str_replace("../..", "", $good->getImage()->getPath('360x'));

                $text_good = "<b>" . $good->name . "</b>" . "\n \n";

                $stringCh = "";
                $chars = (array_chunk($good->goodCharacteristics, 5))[0];
                foreach ($chars as $goodCharacteristic) {
                    if (!empty($goodCharacteristic->value)) {
                        $stringCh .= $goodCharacteristic->valueCharacteristic->name . "  -  " . $goodCharacteristic->value . "; \n";
                    }
                }

                $text_good .= $stringCh;
                $text_good .= "\n Цена: " . $good->price_vrn . "₽";


                $this->telegramApi->apiRequestJson("sendPhoto",
                    [
                        'chat_id' => $this->chat_id,
                        'photo' => $pathImg,
                        'parse_mode' => 'HTML',
                        'caption' => $text_good,
                        'reply_markup' => json_encode([
                            'inline_keyboard' => [
                                [
                                    ['text' => 'Смотреть на сайте', 'url' => 'https://vinlam.ru/goods/' . $good->slug],
                                ]
                            ],
                        ]),
                    ]
                );
            }
        }

    }


}
