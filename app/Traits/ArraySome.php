<?php

namespace App\Traits;

trait ArraySome
{
    protected function arraySome(array $data, callable $callback)
    {
        $result = array_filter($data, $callback);

        return count($result) > 0;
    }
}
