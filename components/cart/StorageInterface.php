<?php

namespace frontend\components\cart;

interface StorageInterface
{
    /**
     * @param array $params (configuration params)
     */
    public function __construct(array $params);
    /**
     * @return CartItem[]
     */
    public function load();
    /**
     * @param CartItem[] $items
     */
    public function save(array $items);
}
