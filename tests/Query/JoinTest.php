<?php

describe('Queries Join', function ()
{
    it('should generate SQL for select join', function ()
    {
        $queries = $this->connection->pretend(function ($c)
        {
            $c->table('products')->join('categories', function ($join)
            {
                $join->on('products.category_id', '=', 'categories.category_id');
                $join->on('products.sublevel_id', '=', 'categories.sublevel_id');

            })->get();

            $c->table('products')->leftjoin('categories', function ($join)
            {
                $join->on('products.category_id', '=', 'categories.category_id');
                $join->on('products.sublevel_id', '=', 'categories.sublevel_id');

            })->first();

            $c->table('products')->crossjoin('categories', function ($join)
            {
                $join->on('products.category_id', '=', 'categories.category_id');
                $join->on('products.sublevel_id', '=', 'categories.sublevel_id');

            })->first();

            $c->table('products')->rightjoin('categories', function ($join)
            {
                $join->on('products.category_id', '=', 'categories.category_id');
                $join->on('products.sublevel_id', '=', 'categories.sublevel_id');

            })->get();
        });

        $sqlExpectedA = 'select * from I_products inner join I_categories on I_products.category_id = I_categories.category_id and I_products.sublevel_id = I_categories.sublevel_id';
        $sqlExpectedB = 'select * from I_products left join I_categories on I_products.category_id = I_categories.category_id and I_products.sublevel_id = I_categories.sublevel_id limit 1';
        $sqlExpectedC = 'select * from I_products cross join I_categories on I_products.category_id = I_categories.category_id and I_products.sublevel_id = I_categories.sublevel_id limit 1';
        $sqlExpectedD = 'select * from I_products right join I_categories on I_products.category_id = I_categories.category_id and I_products.sublevel_id = I_categories.sublevel_id';

        expect($queries[0])->toMatchArray(['query' => $sqlExpectedA, 'bindings' => []]);
        expect($queries[1])->toMatchArray(['query' => $sqlExpectedB, 'bindings' => []]);
        expect($queries[2])->toMatchArray(['query' => $sqlExpectedC, 'bindings' => []]);
        expect($queries[3])->toMatchArray(['query' => $sqlExpectedD, 'bindings' => []]);
    });
});
