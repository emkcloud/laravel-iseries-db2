<?php

describe('Queries Update', function ()
{
    it('should generate SQL for delete', function ()
    {
        $queries = $this->connection->pretend(function ($c)
        {
            $c->table('products')->delete();
            $c->table('products')->whereId(150)->delete();
            $c->table('products')->whereNull('plu')->limit(10)->delete();
        });

        $sqlExpectedA = 'delete from I_products';
        $sqlExpectedB = 'delete from I_products where id = ?';
        $sqlExpectedC = 'delete from I_products where plu is null';

        expect($queries[0])->toMatchArray(['query' => $sqlExpectedA, 'bindings' => []]);
        expect($queries[1])->toMatchArray(['query' => $sqlExpectedB, 'bindings' => [150]]);
        expect($queries[2])->toMatchArray(['query' => $sqlExpectedC, 'bindings' => []]);
    });

    it('should generate SQL for increment', function ()
    {
        $queries = $this->connection->pretend(function ($c)
        {
            $c->table('products')->increment('votes');
            $c->table('products')->increment('votes', 5);
            $c->table('products')->decrement('votes');
            $c->table('products')->decrement('votes', 5);
            $c->table('products')->increment('votes', 1, ['status' => 'V']);
            $c->table('products')->incrementEach(['votes' => 5, 'balance' => 100]);
        });

        $sqlExpectedA = 'update I_products set votes = votes + 1';
        $sqlExpectedB = 'update I_products set votes = votes + 5';
        $sqlExpectedC = 'update I_products set votes = votes - 1';
        $sqlExpectedD = 'update I_products set votes = votes - 5';
        $sqlExpectedE = 'update I_products set votes = votes + 1, status = ?';
        $sqlExpectedF = 'update I_products set votes = votes + 5, balance = balance + 100';

        expect($queries[0])->toMatchArray(['query' => $sqlExpectedA, 'bindings' => []]);
        expect($queries[1])->toMatchArray(['query' => $sqlExpectedB, 'bindings' => []]);
        expect($queries[2])->toMatchArray(['query' => $sqlExpectedC, 'bindings' => []]);
        expect($queries[3])->toMatchArray(['query' => $sqlExpectedD, 'bindings' => []]);
        expect($queries[4])->toMatchArray(['query' => $sqlExpectedE, 'bindings' => ['V']]);
        expect($queries[5])->toMatchArray(['query' => $sqlExpectedF, 'bindings' => []]);
    });

    it('should generate SQL for insert', function ()
    {
        $queries = $this->connection->pretend(function ($c)
        {
            $c->table('products')->insert(
                ['plu' => 'A10101001', 'price' => 10]
            );

            $c->table('products')->insert([
                ['plu' => 'A10101001', 'price' => 10],
                ['plu' => 'A10101002', 'price' => 15],
            ]);

            $c->table('products')->insertGetId(
                ['plu' => 'A10101001', 'price' => 10],
            );
        });

        $sqlExpectedA = 'insert into I_products (plu, price) values (?, ?)';
        $sqlExpectedB = 'insert into I_products (plu, price) values (?, ?), (?, ?)';
        $sqlExpectedC = 'insert into I_products (plu, price) values (?, ?)';

        expect($queries[0])->toMatchArray(['query' => $sqlExpectedA, 'bindings' => ['A10101001', 10]]);
        expect($queries[1])->toMatchArray(['query' => $sqlExpectedB, 'bindings' => ['A10101001', 10, 'A10101002', 15]]);
        expect($queries[2])->toMatchArray(['query' => $sqlExpectedC, 'bindings' => ['A10101001', 10]]);
    });

    it('should generate SQL for update', function ()
    {
        $queries = $this->connection->pretend(function ($c)
        {
            $c->table('products')->whereId(1)->update(['votes' => 1]);
            $c->table('products')->updateOrInsert(['id' => '1'], ['votes' => '2']);
        });

        $sqlExpectedA = 'update I_products set votes = ? where id = ?';
        $sqlExpectedB = 'select exists(select * from I_products where (id = ?)) as exists';

        expect($queries[0])->toMatchArray(['query' => $sqlExpectedA, 'bindings' => [1, 1]]);
        expect($queries[1])->toMatchArray(['query' => $sqlExpectedB, 'bindings' => [1]]);
    });
});
