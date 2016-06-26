<?php
namespace ChadLinden\Api\Mutators;


abstract class Mutator
{
    protected $model;

    /**
     * @param $items
     * @return array
     */
    public function mutateMany( $items )
    {
        //  We kindly cast collections to
        //  an array if not done elsewhere
        $items = method_exists($items, 'toArray') ? $items->toArray() : $items;
        return array_map([$this,'mutate'], $items);
    }

    /**
     * Mutate each record for
     * the API endpoint
     * @param $item
     * @return mixed
     */
    abstract public function mutate(array $item );

}