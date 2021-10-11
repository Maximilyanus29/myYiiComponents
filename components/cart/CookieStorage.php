<?php

namespace frontend\components\cart;


use yii\helpers\Json;
use yii\web\Cookie;
use Yii;

class CookieStorage implements StorageInterface
{
    /**
     * @var array $params Custom configuration params
     */
    private $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * @return array
     */
    public function load(): array
    {
        if ($cookie = Yii::$app->request->cookies->get($this->params['key'])) {
            return array_filter(array_map(static function (array $row) {
                if ( isset($row['id'], $row['quantity']) ) {
                    return new CartItem(
                        $row['id'],
                        $row['quantity'],
                        $row['param']
                    );
                }
                return false;
            }, Json::decode($cookie->value)));
        }
        return [];
    }

    /**
     * @param CartItem[] $items
     * @return void
     */
    public function save(array $items): void
    {
        Yii::$app->response->cookies->add(new Cookie([
            'name' => $this->params['key'],
            'value' => Json::encode(array_map(static function (CartItem $item) {
                return [
                    'id' => $item->getId(),
                    'quantity' => $item->getQuantity(),
                    'price' => $item->getPrice(),
                    'old_price' => $item->getOldPrice(),
                    'param' => $item->getParam()
                ];
            }, $items)),
            'expire' => time() + $this->params['expire'],
        ]));
    }

}
