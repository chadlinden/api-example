<?php

namespace ChadLinden\Api\Mutators;


use Carbon\Carbon;
use ChadLinden\Api\Models\User;

class UserMutator extends Mutator
{
    public function __construct( User $user = null)
    {
        $this->model = $user;
        return $this;
    }

    /**
     * Mutate each record for
     * the API endpoint
     * @param $item
     * @return mixed
     */
    public function mutate(array $item = null)
    {
        // User can be assigned to this or passed
        $item = $item == null ? $this->model->getAttributes() : $item;

        // Do mutations
        unset( $item['password']);
        return array_merge( $item, [
            'created_at' => Carbon::parse($item['created_at'])->format('r'),
            'updated_at' => Carbon::parse($item['updated_at'])->format('r')
        ]);
    }
}