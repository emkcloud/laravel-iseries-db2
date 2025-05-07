<?php

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

describe('Queries Where', function ()
{
    it('should generate SQL for where', function ()
    {
        $queries = $this->connection->pretend(function ($c)
        {
            $c->table('products')->whereId(1)->get();
            $c->table('products')->where('plu', 123)->get();
            $c->table('products')->where('plu', 'A10101')->first();
            $c->table('products')->where('plu', 'A10101')->where('price', '10')->get();
            $c->table('products')->where('plu', 'A10101')->where('price', '>=', '10')->get();
            $c->table('products')->where('plu', 'A10101')->where('price', '<>', '10')->get();
            $c->table('products')->where('stock', 0)->orWhere('avalaible', true)->get();
            $c->table('products')->whereNot('stock', 0)->orWhere('avalaible', true)->get();
            $c->table('products')->whereRaw('price > IF(state = "TX", ?, 100)', [200])->get();
        });

        $sqlExpectedA = 'select * from I_products where id = ?';
        $sqlExpectedB = 'select * from I_products where plu = ?';
        $sqlExpectedC = 'select * from I_products where plu = ? limit 1';
        $sqlExpectedD = 'select * from I_products where plu = ? and price = ?';
        $sqlExpectedE = 'select * from I_products where plu = ? and price >= ?';
        $sqlExpectedF = 'select * from I_products where plu = ? and price <> ?';
        $sqlExpectedG = 'select * from I_products where stock = ? or avalaible = ?';
        $sqlExpectedH = 'select * from I_products where not stock = ? or avalaible = ?';
        $sqlExpectedI = 'select * from I_products where price > IF(state = "TX", ?, 100)';

        expect($queries[0])->toMatchArray(['query' => $sqlExpectedA, 'bindings' => [1]]);
        expect($queries[1])->toMatchArray(['query' => $sqlExpectedB, 'bindings' => [123]]);
        expect($queries[2])->toMatchArray(['query' => $sqlExpectedC, 'bindings' => ['A10101']]);
        expect($queries[3])->toMatchArray(['query' => $sqlExpectedD, 'bindings' => ['A10101', 10]]);
        expect($queries[4])->toMatchArray(['query' => $sqlExpectedE, 'bindings' => ['A10101', 10]]);
        expect($queries[5])->toMatchArray(['query' => $sqlExpectedF, 'bindings' => ['A10101', 10]]);
        expect($queries[6])->toMatchArray(['query' => $sqlExpectedG, 'bindings' => [0, true]]);
        expect($queries[7])->toMatchArray(['query' => $sqlExpectedH, 'bindings' => [0, true]]);
        expect($queries[8])->toMatchArray(['query' => $sqlExpectedI, 'bindings' => [200]]);
    });

    it('should generate SQL for where between', function ()
    {
        $queries = $this->connection->pretend(function ($c)
        {
            $c->table('products')->whereBetween('votes', [1, 100])->get();
            $c->table('products')->whereNotBetween('votes', [1, 100])->get();
            $c->table('products')->whereNotBetween('votes', [1, 100])->first();
            $c->table('products')->whereNotBetweenColumns('price', ['minimum_allowed', 'maximum_allowed'])->get();
        });

        $sqlExpectedA = 'select * from I_products where votes between ? and ?';
        $sqlExpectedB = 'select * from I_products where votes not between ? and ?';
        $sqlExpectedC = 'select * from I_products where votes not between ? and ? limit 1';
        $sqlExpectedD = 'select * from I_products where price not between minimum_allowed and maximum_allowed';

        expect($queries[0])->toMatchArray(['query' => $sqlExpectedA, 'bindings' => [1, 100]]);
        expect($queries[1])->toMatchArray(['query' => $sqlExpectedB, 'bindings' => [1, 100]]);
        expect($queries[2])->toMatchArray(['query' => $sqlExpectedC, 'bindings' => [1, 100]]);
        expect($queries[3])->toMatchArray(['query' => $sqlExpectedD, 'bindings' => []]);
    });

    it('should generate SQL for where columns', function ()
    {
        $queries = $this->connection->pretend(function ($c)
        {
            $c->table('products')->whereColumn('category', 'family')->get();
            $c->table('products')->whereColumn('updated_at', '>', 'created_at')->get();
            $c->table('products')->whereColumn([['category', '=', 'family'], ['updated_at', '>', 'created_at']])->get();
        });

        $sqlExpectedA = 'select * from I_products where category = family';
        $sqlExpectedB = 'select * from I_products where updated_at > created_at';
        $sqlExpectedC = 'select * from I_products where (category = family and updated_at > created_at)';

        expect($queries[0])->toMatchArray(['query' => $sqlExpectedA, 'bindings' => []]);
        expect($queries[1])->toMatchArray(['query' => $sqlExpectedB, 'bindings' => []]);
        expect($queries[2])->toMatchArray(['query' => $sqlExpectedC, 'bindings' => []]);
    });

    it('should generate SQL for where datetime', function ()
    {
        $queries = $this->connection->pretend(function ($c)
        {
            $c->table('products')->whereDate('created_at', '2025-12-31')->get();
            $c->table('products')->whereMonth('created_at', '12')->get();
            $c->table('products')->whereDay('created_at', '31')->get();
            $c->table('products')->whereYear('created_at', '2025')->get();
            $c->table('products')->whereTime('created_at', '=', '11:20:45')->get();
            $c->table('products')->wherePast('created_at')->get();
            $c->table('products')->whereFuture('created_at')->get();
            $c->table('products')->whereNowOrPast('created_at')->get();
            $c->table('products')->whereNowOrFuture('created_at')->get();
            $c->table('products')->whereToday('created_at')->get();
            $c->table('products')->whereBeforeToday('created_at')->get();
            $c->table('products')->whereAfterToday('created_at')->get();
            $c->table('products')->whereTodayOrBefore('created_at')->get();
            $c->table('products')->whereTodayOrAfter('created_at')->get();
        });

        $sqlExpectedA = 'select * from I_products where date(created_at) = ?';
        $sqlExpectedB = 'select * from I_products where month(created_at) = ?';
        $sqlExpectedC = 'select * from I_products where day(created_at) = ?';
        $sqlExpectedD = 'select * from I_products where year(created_at) = ?';
        $sqlExpectedE = 'select * from I_products where time(created_at) = ?';
        $sqlExpectedF = 'select * from I_products where created_at < ?';
        $sqlExpectedG = 'select * from I_products where created_at > ?';
        $sqlExpectedH = 'select * from I_products where created_at <= ?';
        $sqlExpectedI = 'select * from I_products where created_at >= ?';
        $sqlExpectedL = 'select * from I_products where date(created_at) = ?';
        $sqlExpectedM = 'select * from I_products where date(created_at) < ?';
        $sqlExpectedN = 'select * from I_products where date(created_at) > ?';
        $sqlExpectedO = 'select * from I_products where date(created_at) <= ?';

        expect($queries[0])->toMatchArray(['query' => $sqlExpectedA, 'bindings' => ['2025-12-31']]);
        expect($queries[1])->toMatchArray(['query' => $sqlExpectedB, 'bindings' => ['12']]);
        expect($queries[2])->toMatchArray(['query' => $sqlExpectedC, 'bindings' => ['31']]);
        expect($queries[3])->toMatchArray(['query' => $sqlExpectedD, 'bindings' => ['2025']]);
        expect($queries[4])->toMatchArray(['query' => $sqlExpectedE, 'bindings' => ['11:20:45']]);
        expect($queries[5])->toMatchArray(['query' => $sqlExpectedF, 'bindings' => $queries[5]['bindings']]);
        expect($queries[6])->toMatchArray(['query' => $sqlExpectedG, 'bindings' => $queries[6]['bindings']]);
        expect($queries[7])->toMatchArray(['query' => $sqlExpectedH, 'bindings' => $queries[7]['bindings']]);
        expect($queries[8])->toMatchArray(['query' => $sqlExpectedI, 'bindings' => $queries[8]['bindings']]);
        expect($queries[9])->toMatchArray(['query' => $sqlExpectedL, 'bindings' => $queries[9]['bindings']]);
        expect($queries[10])->toMatchArray(['query' => $sqlExpectedM, 'bindings' => $queries[10]['bindings']]);
        expect($queries[11])->toMatchArray(['query' => $sqlExpectedN, 'bindings' => $queries[11]['bindings']]);
        expect($queries[12])->toMatchArray(['query' => $sqlExpectedO, 'bindings' => $queries[11]['bindings']]);
    });

    it('should generate SQL for where exists', function ()
    {
        $queries = $this->connection->pretend(function ($c)
        {
            $c->table('users')->whereExists(function (Builder $query)
            {
                $query->select(DB::raw(1))->from('orders')->whereColumn('orders.user_id', 'users.id');

            })->get();
        });

        $sqlExpectedA = 'select * from I_users where exists (select 1 from I_orders where I_orders.user_id = I_users.id)';

        expect($queries[0])->toMatchArray(['query' => $sqlExpectedA, 'bindings' => []]);
    });

    it('should generate SQL for where helper', function ()
    {
        $queries = $this->connection->pretend(function ($c)
        {
            $c->table('products')->where('active', true)->whereAny(['line', 'category'], 'like', '%phone%')->get();
            $c->table('products')->where('active', true)->whereAll(['line', 'category'], 'like', '%phone%')->get();
            $c->table('products')->where('active', true)->whereNone(['line', 'category'], 'like', '%phone%')->get();
        });

        $sqlExpectedA = 'select * from I_products where active = ? and (line like ? or category like ?)';
        $sqlExpectedB = 'select * from I_products where active = ? and (line like ? and category like ?)';
        $sqlExpectedC = 'select * from I_products where active = ? and not (line like ? or category like ?)';

        expect($queries[0])->toMatchArray(['query' => $sqlExpectedA, 'bindings' => [true, '%phone%', '%phone%']]);
        expect($queries[1])->toMatchArray(['query' => $sqlExpectedB, 'bindings' => [true, '%phone%', '%phone%']]);
        expect($queries[2])->toMatchArray(['query' => $sqlExpectedC, 'bindings' => [true, '%phone%', '%phone%']]);
    });

    it('should generate SQL for where in', function ()
    {
        $queries = $this->connection->pretend(function ($c)
        {
            $c->table('products')->whereIn('id', [1, 2, 3, 4, 5])->get();
            $c->table('products')->whereIn('id', ['1', '2', '3', '4', '5'])->get();
            $c->table('products')->whereNotIn('id', ['1', '2', '3', '4', '5'])->first();
        });

        $sqlExpectedA = 'select * from I_products where id in (?, ?, ?, ?, ?)';
        $sqlExpectedB = 'select * from I_products where id in (?, ?, ?, ?, ?)';
        $sqlExpectedC = 'select * from I_products where id not in (?, ?, ?, ?, ?) limit 1';

        expect($queries[0])->toMatchArray(['query' => $sqlExpectedA, 'bindings' => [1, 2, 3, 4, 5]]);
        expect($queries[1])->toMatchArray(['query' => $sqlExpectedB, 'bindings' => ['1', '2', '3', '4', '5']]);
        expect($queries[2])->toMatchArray(['query' => $sqlExpectedC, 'bindings' => ['1', '2', '3', '4', '5']]);
    });

    it('should generate SQL for where like', function ()
    {
        $queries = $this->connection->pretend(function ($c)
        {
            $c->table('products')->whereLike('description', '%phone%')->get();
            $c->table('products')->whereNotLike('description', '%phone%')->get();
            $c->table('products')->whereNotLike('description', '%phone%')->first();
            $c->table('products')->where('stock', '>', 0)->orWhereLike('description', '%phone%')->get();
            $c->table('products')->where('stock', '>', 0)->orWhereNotLike('description', '%phone%')->get();
        });

        $sqlExpectedA = 'select * from I_products where description like ?';
        $sqlExpectedB = 'select * from I_products where description not like ?';
        $sqlExpectedC = 'select * from I_products where description not like ? limit 1';
        $sqlExpectedD = 'select * from I_products where stock > ? or description like ?';
        $sqlExpectedE = 'select * from I_products where stock > ? or description not like ?';

        expect($queries[0])->toMatchArray(['query' => $sqlExpectedA, 'bindings' => ['%phone%']]);
        expect($queries[1])->toMatchArray(['query' => $sqlExpectedB, 'bindings' => ['%phone%']]);
        expect($queries[2])->toMatchArray(['query' => $sqlExpectedC, 'bindings' => ['%phone%']]);
        expect($queries[3])->toMatchArray(['query' => $sqlExpectedD, 'bindings' => [0, '%phone%']]);
        expect($queries[4])->toMatchArray(['query' => $sqlExpectedE, 'bindings' => [0, '%phone%']]);
    });

    it('should generate SQL for where multiple', function ()
    {
        $queries = $this->connection->pretend(function ($c)
        {
            $c->table('products')->where('id', 1)->orWhere('plu', 123)->get();
            $c->table('products')->where('id', 1)->wherePlu('A10101')->get();
            $c->table('products')->where('id', 1)->wherePlu('A10101')->first();
            $c->table('products')->where([['status', '=', '1'], ['stock', '<>', 0]])->get();
        });

        $sqlExpectedA = 'select * from I_products where id = ? or plu = ?';
        $sqlExpectedB = 'select * from I_products where id = ? and plu = ?';
        $sqlExpectedC = 'select * from I_products where id = ? and plu = ? limit 1';
        $sqlExpectedD = 'select * from I_products where (status = ? and stock <> ?)';

        expect($queries[0])->toMatchArray(['query' => $sqlExpectedA, 'bindings' => [1, 123]]);
        expect($queries[1])->toMatchArray(['query' => $sqlExpectedB, 'bindings' => [1, 'A10101']]);
        expect($queries[2])->toMatchArray(['query' => $sqlExpectedC, 'bindings' => [1, 'A10101']]);
        expect($queries[3])->toMatchArray(['query' => $sqlExpectedD, 'bindings' => ['1', 0]]);
    });

    it('should generate SQL for where null', function ()
    {
        $queries = $this->connection->pretend(function ($c)
        {
            $c->table('products')->whereNull('id')->get();
            $c->table('products')->whereNotNull('id')->get();
            $c->table('products')->whereNotNull('id')->first();
            $c->table('products')->whereNull('id')->orWhereNull('title')->get();
            $c->table('products')->whereNull('id')->orWhereNotNull('title')->get();
        });

        $sqlExpectedA = 'select * from I_products where id is null';
        $sqlExpectedB = 'select * from I_products where id is not null';
        $sqlExpectedC = 'select * from I_products where id is not null limit 1';
        $sqlExpectedD = 'select * from I_products where id is null or title is null';
        $sqlExpectedE = 'select * from I_products where id is null or title is not null';

        expect($queries[0])->toMatchArray(['query' => $sqlExpectedA, 'bindings' => []]);
        expect($queries[1])->toMatchArray(['query' => $sqlExpectedB, 'bindings' => []]);
        expect($queries[2])->toMatchArray(['query' => $sqlExpectedC, 'bindings' => []]);
        expect($queries[3])->toMatchArray(['query' => $sqlExpectedD, 'bindings' => []]);
        expect($queries[4])->toMatchArray(['query' => $sqlExpectedE, 'bindings' => []]);
    });
});
