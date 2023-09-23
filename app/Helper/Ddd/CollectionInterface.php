<?php

namespace App\Helper\Ddd;

/**
 * Summary of CollectionInterface
 * @author PutrimakIslan
 * @copyright (c) 2023
 */
interface CollectionInterface
{
    public function find($key, $default = null);

    public function add($item);

    public function only($keys);

    public function except($keys);

    public function keys();
}
