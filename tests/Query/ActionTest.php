<?php

describe('Queries Action', function ()
{
    it('should generate SQL for having', function ()
    {
        $queries = $this->connection->pretend(function ($c)
        {
            $c->table('products')->groupBy('category_id')->having('category_id', '>', 100)->get();
            $c->table('products')->selectRaw('count(id) as number_of_products, category_id')->groupBy('category_id')->havingBetween('number_of_products', [5, 15])->get();
            $c->table('products')->groupBy('available', 'status')->having('category_id', '>', 100)->get();
        });

        $sqlExpectedA = 'select * from I_products group by category_id having category_id > ?';
        $sqlExpectedB = 'select count(id) as number_of_products, category_id from I_products group by category_id having number_of_products between ? and ?';
        $sqlExpectedC = 'select * from I_products group by available, status having category_id > ?';

        expect($queries[0])->toMatchArray(['query' => $sqlExpectedA, 'bindings' => [100]]);
        expect($queries[1])->toMatchArray(['query' => $sqlExpectedB, 'bindings' => [5, 15]]);
        expect($queries[2])->toMatchArray(['query' => $sqlExpectedC, 'bindings' => [100]]);
    });

    it('should generate SQL for group by', function ()
    {
        $queries = $this->connection->pretend(function ($c)
        {
            $c->table('products')->groupBy('id')->get();
            $c->table('products')->orderBy('id')->groupBy('id')->get();
            $c->table('products')->groupBy('category_id', 'sequence')->get();
            $c->table('products')->select('category_id', 'family_id')->groupByRaw('category_id, family_id')->get();
        });

        $sqlExpectedA = 'select * from I_products group by id';
        $sqlExpectedB = 'select * from I_products group by id order by id asc';
        $sqlExpectedC = 'select * from I_products group by category_id, sequence';
        $sqlExpectedD = 'select category_id, family_id from I_products group by category_id, family_id';

        expect($queries[0])->toMatchArray(['query' => $sqlExpectedA, 'bindings' => []]);
        expect($queries[1])->toMatchArray(['query' => $sqlExpectedB, 'bindings' => []]);
        expect($queries[2])->toMatchArray(['query' => $sqlExpectedC, 'bindings' => []]);
        expect($queries[3])->toMatchArray(['query' => $sqlExpectedD, 'bindings' => []]);
    });

    it('should generate SQL for order by', function ()
    {
        $queries = $this->connection->pretend(function ($c)
        {
            $c->table('products')->orderBy('id')->get();
            $c->table('products')->orderByDesc('id')->get();
            $c->table('products')->orderByDesc('id')->first();
            $c->table('products')->orderByRaw('updated_at - created_at DESC')->get();
            $c->table('products')->orderBy('title', 'asc')->orderBy('id', 'desc')->get();
            $c->table('products')->latest()->first();
            $c->table('products')->inRandomOrder()->first();
            $c->table('products')->orderBy('title')->reorder('id', 'desc')->get();
        });

        $sqlExpectedA = 'select * from I_products order by id asc';
        $sqlExpectedB = 'select * from I_products order by id desc';
        $sqlExpectedC = 'select * from I_products order by id desc limit 1';
        $sqlExpectedD = 'select * from I_products order by updated_at - created_at DESC';
        $sqlExpectedE = 'select * from I_products order by title asc, id desc';
        $sqlExpectedF = 'select * from I_products order by created_at desc limit 1';
        $sqlExpectedG = 'select * from I_products order by RANDOM() limit 1';
        $sqlExpectedH = 'select * from I_products order by id desc';

        expect($queries[0])->toMatchArray(['query' => $sqlExpectedA, 'bindings' => []]);
        expect($queries[1])->toMatchArray(['query' => $sqlExpectedB, 'bindings' => []]);
        expect($queries[2])->toMatchArray(['query' => $sqlExpectedC, 'bindings' => []]);
        expect($queries[3])->toMatchArray(['query' => $sqlExpectedD, 'bindings' => []]);
        expect($queries[4])->toMatchArray(['query' => $sqlExpectedE, 'bindings' => []]);
        expect($queries[5])->toMatchArray(['query' => $sqlExpectedF, 'bindings' => []]);
        expect($queries[6])->toMatchArray(['query' => $sqlExpectedG, 'bindings' => []]);
        expect($queries[7])->toMatchArray(['query' => $sqlExpectedH, 'bindings' => []]);
    });
});
