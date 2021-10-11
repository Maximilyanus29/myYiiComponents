<?php

namespace frontend\components\cart;


use common\models\Good;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;

class Cart extends BaseObject
{
    /**
     * @var string $storageClass
     */
    public $storageClass = 'frontend\components\cart\CookieStorage';

    public $cartItemClass;

    public $params = [];

    /**
     * @var array $defaultParams
     */
    private $defaultParams = [
        'key' => 'cart',
        'expire' => 604800,
        'productClass' => 'common\models\Good',
    ];

    /**
     * @var CartItem[]
     */
    private $items;

    /**
     * @var StorageInterface
     */
    private $storage;



    /**
     * @var int|float $deliveryPrice
     */
    private $deliveryPrice;


    /**
     * @var int|float $promo
     */
    private $promo;


    /**
     * @var int|float $scores
     */
    private $scores;


    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->params = array_merge( $this->defaultParams, $this->params );

        if (!class_exists($this->params['productClass'])) {
            throw new InvalidConfigException('productClass `' . $this->params['productClass'] . '` not found');
        }
        if (!class_exists($this->storageClass)) {
            throw new InvalidConfigException('storageClass `' . $this->storageClass . '` not found');
        }


        $this->storage = new $this->storageClass($this->params);
    }

    /**
     * @inheritdoc
     * Нужно при пересчете в бэкенде, что бы взять актуальные цены из базы, потому что из фронта может подмениться(хацкерами) цена в куках
     */
    public function reinitItemsForRecalculateOnBackend()
    {
        $this->loadItems();

        foreach (Good::find()->where(['good.id' => $this->getIds()])->joinWith('goodParams')->all() as $good){
            foreach ($this->getItems() as $item){
                if ($item->getId() == $good->id){
                    if (!empty($item->getParam())){
                        $item->setPrices($good->getPrice($item->getParam()), $good->getOldPrice($item->getParam()));
                    }else{
                        $item->setPrices($good->getPrice(), $good->getOldPrice());
                    }
                }
            }
        }
    }

    /**
     * Add an item to the cart
     * @param $productId
     * @param integer $quantity
     * @param null $modification
     * @return void
     */
    public function add( int $productId, int $quantity, $modification = null)
    {
        $this->loadItems();

        $productHash = $this->calculateHash($productId, $modification);

        if (isset($this->items[$productHash])) {
            $this->plus($productHash, $quantity);
        } else {
            $this->items[$productHash] = new CartItem($productId, $quantity, $modification);
            ksort($this->items, SORT_NUMERIC);
            $this->saveItems();
        }
    }

    /**
     *Считаем хэш для того, что-бы соеденить продукт с параметром в 1 продукт
     * @param $productId
     * @param $modification
     * @return string
     */
    public function calculateHash(int $productId, int $modification = null): string
    {
        return md5(implode(',', [$productId , $modification]));
    }

    /**
     * Adding item quantity in the cart
     * @param string $hash
     * @param integer $quantity
     * @return void
     */
    public function plus(string $hash, int $quantity): void
    {
        $this->loadItems();
        if (isset($this->items[$hash])) {
            $this->items[$hash]->setQuantity( $quantity + $this->items[$hash]->getQuantity());
        }
        $this->saveItems();
    }

    /**
     * Change item quantity in the cart
     * @param string $hash
     * @param integer $quantity
     * @return void
     */
    public function change($hash, int $quantity): void
    {
        $this->loadItems();
        if (isset($this->items[$hash])) {
            $this->items[$hash]->setQuantity($quantity);
        }
        $this->saveItems();
    }

    /**
     * Removes an items from the cart
     * @param string $hash
     * @return void
     */
    public function remove($hash): void
    {
        $this->loadItems();
        if (array_key_exists($hash, $this->items)) {
            unset($this->items[$hash]);
        }
        $this->saveItems();
    }

    /**
     * Removes all items from the cart
     * @return void
     */
    public function clear(): void
    {
        $this->items = [];
        $this->saveItems();
    }

    /**
     * Returns all items from the cart
     * @return CartItem[]
     */
    public function getItems(): array
    {
        $this->loadItems();
        return $this->items;
    }

    /**
     * Returns hashes array all items from the cart
     * @return array
     */
    public function getItemHashes()
    {
        $this->loadItems();
        $items = [];
        foreach ($this->items as $hashItem => $item) {
            $items[] = $hashItem;
        }
        return $items;
    }


    /**
     * Returns hashes array all items from the cart
     * @return array
     */
    public function getIds(): array
    {
        $this->loadItems();
        $items = [];
        foreach ($this->items as $item) {
            $items[] = $item->getId();
        }
        return $items;
    }



    /**
     * Returns total cost all items from the cart
     * @param bool $withDiscount
     * @return integer|float
     */
    public function getTotalCost($withDiscount = false)
    {
        $this->loadItems();

        $cost = 0;
        foreach ($this->items as $item) {
            $cost += $item->getCost($withDiscount);
        }
        return $cost;
    }



    /**
     * @param CartItem[] $items
     * @return integer
     */
    public function getTotalCount(array $items): int
    {
        $this->loadItems();

        $count = 0;
        foreach ($items as $item) {
            $count += $item->getQuantity();
        }
        return $count;
    }



    /**
     * Load all items from the cart
     * @return void
     */
    private function loadItems(): void
    {
        if ($this->items === null) {
            $this->items = $this->storage->load();
        }
    }

    /**
     * Save all items to the cart
     * @return void
     */
    private function saveItems(): void
    {
        $this->storage->save($this->items);
    }
}
