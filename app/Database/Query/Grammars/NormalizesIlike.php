<?php

namespace App\Database\Query\Grammars;

use Illuminate\Database\Query\Builder;

trait NormalizesIlike
{
    protected function whereBasic(Builder $query, $where)
    {
        $operator = strtolower((string) $where['operator']);

        if ($operator === 'ilike') {
            $where['operator'] = 'like';
        } elseif ($operator === 'not ilike') {
            $where['operator'] = 'not like';
        }

        return parent::whereBasic($query, $where);
    }
}
