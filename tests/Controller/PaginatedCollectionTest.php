<?php

namespace App\Tests\Controller;

use App\Pagination\PaginatedCollection;
use PHPUnit\Framework\TestCase;

class PaginatedCollectionTest extends TestCase
{

    public function testSuccesPagination()
    {


        $rows = [
            0 => ["name" => "test"],
            1 => ["name" => "test1"]
        ];
        $totalPages = 1;

        $paginatedCollection = new PaginatedCollection($rows, $totalPages);

        $this->assertSame(2, $paginatedCollection->getCount());
    }
}
