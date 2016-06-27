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
     * @param $startTime
     * @param $endTime
     * @param null $employeeID
     * @return mixed
     */
    public function between($startTime, $endTime, $employeeID = null)
    {
        $query = self::where('start_time', '>=', $startTime)
            ->where('end_time', '<=', $endTime);
        if( $employeeID !== null)
        {
            $query->where('employee_id', $employeeID);
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