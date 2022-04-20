<?php

namespace App\Repository\Interface;

interface SearchInterface
{
    public function search($term, $order, $limit, $offset, $fields = []);
    public function countValue($fields = []);
}