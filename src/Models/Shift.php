<?php
namespace ChadLinden\Api\Models;

class Shift extends Model
{
    protected $fillable = [
        'employee_id',
        'start_time',
        'manager_id',
        'end_time',
        'break',
    ];

    protected $casts = [
        'start_time' => 'date',
        'end_time' => 'date'
    ];

    public function manager()
    {
        return User::where('id', $this->manager_id)->get();
    }

    public function employee()
    {
        return User::where('id', $this->employee_id)->get();
    }

}