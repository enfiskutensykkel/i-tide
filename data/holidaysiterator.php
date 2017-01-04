<?php
namespace data;
use \Iterator;

final class HolidaysIterator implements Iterator
{
    private $aggregated;
    private $keys;
    private $current;
    private $count;

    public function __construct(&$holidays)
    {
        $this->aggregated = &$holidays;
        $this->keys = array_keys($this->aggregated);
        $this->count = count($this->aggregated);
    }

    public function rewind()
    {
        $this->current = 0;
    }

    public function current()
    {
        return $this->aggregated[$this->keys[$this->current]];
    }

    public function key()
    {
        if ($this->current < $this->count)
        {
            return $this->keys[$this->current];
        }

        return null;
    }

    public function next()
    {
        ++$this->current;
    }

    public function valid()
    {
        return $this->current < $this->count;
    }
}

?>
