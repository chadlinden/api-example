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

    /**
     * Find shifts between two times with
     * the option to filter employee_id
     * @param array $options
     * @return mixed
     */
    public static function between(array $options = ['start_time', 'end_time', 'employee_id'])
    {
        extract($options);
        $query = self::where('start_time', '>=', $start_time)
            ->where('end_time', '<=', $end_time);
        if( ! empty($employee_id) )
        {
            $query->where('employee_id', $employee_id);
        }
        return $query->get();
    }

    public function manager()
    {
        return User::where('id', $this->manager_id)->get();
    }

    public function employee()
    {
        return User::where('id', $this->employee_id)->get();
    }

}