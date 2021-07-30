<?php

namespace App\Pagination;

class PaginatedCollection implements \JsonSerializable
{
    private $items;
    private $count;
    private $totalPages;
    private $_links = array();

    public function __construct(array $items, $totalPages)
    {
        $this->items = $items;
        $this->totalPages = $totalPages;
        $this->count = count($items);
    }

    public function addLink($ref, $url)
    {
        $this->_links[$ref] = $url;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
