<?php

namespace ChadLinden\Api\Models;


use Illuminate\Database\Eloquent\Model as EloquentModel;

abstract class Model extends EloquentModel
{
    protected $attributes = [];
}