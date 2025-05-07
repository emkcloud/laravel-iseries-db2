<?php

describe('Queries Aggregate', function ()
{
    it('should generate SQL for avg', function ()
    {
        $queries = $this->connection->pretend(function ($c)
        {
            $c->table('products')->avg('price');
            $c->table('products')->where('id', '>', 0)->avg('stock');
        });

        $sqlExpectedA = 'select avg(price) as aggregate from I_products';
        $sqlExpectedB = 'select avg(stock) as aggregate from I_products where id > ?';

        expect($queries[0])->toMatchArray(['query' => $sqlExpectedA, 'bindings' => []]);
        expect($queries[1])->toMatchArray(['query' => $sqlExpectedB, 'bindings' => [0]]);
    });

    it('should generate SQL for count', function ()
    {
        $queries = $this->connection->pretend(function ($c)
        {
            $c->table('products')->count();
            $c->table('products')->where('id', '>', 0)->count();
        });

        $sqlExpectedA = 'select count(*) as aggregate from I_products';
        $sqlExpectedB = 'select count(*) as aggregate from I_products where id > ?';

        expect($queries[0])->toMatchArray(['query' => $sqlExpectedA, 'bindings' => []]);
        expect($queries[1])->toMatchArray(['query' => $sqlExpectedB, 'bindings' => [0]]);
    });

    it('should generate SQL for distinct', function ()
    {
        $queries = $this->connection->pretend(function ($c)
        {
            $c->table('products')->distinct()->get();
            $c->table('products')->select('plu')->distinct()->get();
        });

        $sqlExpectedA = 'select distinct * from I_products';
        $sqlExpectedB = 'select distinct plu from I_products';

        expect($queries[0])->toMatchArray(['query' => $sqlExpectedA, 'bindings' => []]);
        expect($queries[1])->toMatchArray(['query' => $sqlExpectedB, 'bindings' => []]);
    });

    it('should generate SQL for max', function ()
    {
        $queries = $this->connection->pretend(function ($c)
        {
            $c->table('products')->max('price');
            $c->table('products')->where('id', '>', 0)->max('stock');
        });

        $sqlExpectedA = 'select max(price) as aggregate from I_products';
        $sqlExpectedB = 'select max(stock) as aggregate from I_products where id > ?';

        expect($queries[0])->toMatchArray(['query' => $sqlExpectedA, 'bindings' => []]);
        expect($queries[1])->toMatchArray(['query' => $sqlExpectedB, 'bindings' => [0]]);
    });
});
