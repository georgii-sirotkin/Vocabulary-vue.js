<?php

namespace App\DataStructures;

/**
 * One-pointer Circular Buffer
 *
 * This comes without any overwrite
 * checks or FIFO functionality.
 * You'll need SPL.
 *
 * @author Bernhard Häussner
 * @date 2011-09-07
 */
class RingBuffer
{

    /**
     * Next insert index
     * @var int
     */
    private $pos = 0;

    /**
     * Maximum number of distinct elements
     * @var int
     */
    private $size = 0;

    /**
     * Tuple of elements
     * @var SplFixedArray
     */
    private $data = null;

    /**
     * Initialize with a fixed size
     * @param int Element count when overwriting starts
     */
    public function __construct($size)
    {
        $this->size = $size;
        $this->data = new \SplFixedArray($size);
        $this->pos = $size - 1;
    }

    /**
     * Append to the buffer
     * @param mixed elemet
     * @return self this (chainable)
     */
    public function add($item)
    {
        $this->data[$this->pos--] = $item;
        if ($this->pos < 0) {
            $this->pos = $this->size - 1;
        }

        return $this;
    }

    /**
     * Build an array starting with the last inserted element
     * @return array
     */
    public function dump()
    {
        $a = $this->data->toArray();
        $p = $this->pos + 1;
        return array_merge(array_slice($a, $p), array_slice($a, 0, $p));
    }

    /**
     * Last inserted element
     * @param int optional, return last but [offs] element
     * @return mixed element
     */
    public function top($offs = 0)
    {
        return $this->data[($this->pos + 1 + $offs) % $this->size];
    }

    /**
     * Get an array of nonempty elements (order is not specified).
     *
     * @return array
     */
    public function getNonemptyElements()
    {
        return array_filter($this->data->toArray());
    }

}
