<?php

describe('Queries Select', function ()
{
    it('should generate SQL for select', function ()
    {
        $queries = $this->connection->pretend(function ($c)
        {
            $c->table('products')->get();
            $c->table('schema.products')->get();
            $c->table('schema.products')->select(['plu', 'title'])->get();
            $c->table('schema.products')->select(['plu', 'title'])->first();
            $c->table('products')->selectRaw('price * ? as price_with_tax', [1.08])->get();
            $c->table('products')->select('plu')->addSelect('price')->get();
            $c->table('products')->find(3);
            $c->table('products')->pluck('title');
        });

        $sqlExpectedA = 'select * from I_products';
        $sqlExpectedB = 'select * from schema.I_products';
        $sqlExpectedC = 'select plu, title from schema.I_products';
        $sqlExpectedD = 'select plu, title from schema.I_products limit 1';
        $sqlExpectedE = 'select price * ? as price_with_tax from I_products';
        $sqlExpectedF = 'select plu, price from I_products';
        $sqlExpectedG = 'select * from I_products where id = ? limit 1';
        $sqlExpectedH = 'select title from I_products';

        expect($queries[0])->toMatchArray(['query' => $sqlExpectedA, 'bindings' => []]);
        expect($queries[1])->toMatchArray(['query' => $sqlExpectedB, 'bindings' => []]);
        expect($queries[2])->toMatchArray(['query' => $sqlExpectedC, 'bindings' => []]);
        expect($queries[3])->toMatchArray(['query' => $sqlExpectedD, 'bindings' => []]);
        expect($queries[4])->toMatchArray(['query' => $sqlExpectedE, 'bindings' => [1.08]]);
        expect($queries[5])->toMatchArray(['query' => $sqlExpectedF, 'bindings' => []]);
        expect($queries[6])->toMatchArray(['query' => $sqlExpectedG, 'bindings' => [3]]);
        expect($queries[7])->toMatchArray(['query' => $sqlExpectedH, 'bindings' => []]);
    });

    it('should generate SQL for select exists', function ()
    {
        $queries = $this->connection->pretend(function ($c)
        {
            $c->table('products')->whereId(1)->exists();
            $c->table('products')->where('plu', 123)->doesntExist();
            $c->table('products')->whereIn('plu', [1, 2, 3])->exists();
        });

        $sqlExpectedA = 'select exists(select * from I_products where id = ?) as exists';
        $sqlExpectedB = 'select exists(select * from I_products where plu = ?) as exists';
        $sqlExpectedC = 'select exists(select * from I_products where plu in (?, ?, ?)) as exists';

        expect($queries[0])->toMatchArray(['query' => $sqlExpectedA, 'bindings' => [1]]);
        expect($queries[1])->toMatchArray(['query' => $sqlExpectedB, 'bindings' => [123]]);
        expect($queries[2])->toMatchArray(['query' => $sqlExpectedC, 'bindings' => [1, 2, 3]]);
    });

    it('should generate SQL for select limit', function ()
    {
        $queries = $this->connection->pretend(function ($c)
        {
            $c->table('products')->first();
            $c->table('products')->limit(10)->get();
            $c->table('products')->limit(10)->orderByDesc('id')->get();
            $c->table('products')->take(20)->orderByDesc('id')->get();
        });

        $sqlExpectedA = 'select * from I_products limit 1';
        $sqlExpectedB = 'select * from I_products limit 10';
        $sqlExpectedC = 'select * from I_products order by id desc limit 10';
        $sqlExpectedD = 'select * from I_products order by id desc limit 20';

        expect($queries[0])->toMatchArray(['query' => $sqlExpectedA, 'bindings' => []]);
        expect($queries[1])->toMatchArray(['query' => $sqlExpectedB, 'bindings' => []]);
        expect($queries[2])->toMatchArray(['query' => $sqlExpectedC, 'bindings' => []]);
        expect($queries[3])->toMatchArray(['query' => $sqlExpectedD, 'bindings' => []]);
    });

    it('should generate SQL for select offset', function ()
    {
        $queries = $this->connection->pretend(function ($c)
        {
            $c->table('products')->offset(20)->get();
            $c->table('products')->offset(20)->first();
            $c->table('products')->limit(10)->offset(20)->get();
            $c->table('products')->limit(10)->skip(30)->orderByDesc('id')->get();
        });

        $sqlExpectedA = 'select * from I_products offset 20';
        $sqlExpectedB = 'select * from I_products limit 1 offset 20';
        $sqlExpectedC = 'select * from I_products limit 10 offset 20';
        $sqlExpectedD = 'select * from I_products order by id desc limit 10 offset 30';

        expect($queries[0])->toMatchArray(['query' => $sqlExpectedA, 'bindings' => []]);
        expect($queries[1])->toMatchArray(['query' => $sqlExpectedB, 'bindings' => []]);
        expect($queries[2])->toMatchArray(['query' => $sqlExpectedC, 'bindings' => []]);
        expect($queries[3])->toMatchArray(['query' => $sqlExpectedD, 'bindings' => []]);
    });
});
