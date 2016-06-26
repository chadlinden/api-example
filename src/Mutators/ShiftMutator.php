<?php

namespace ChadLinden\Api\Mutators;


use Carbon\Carbon;
use ChadLinden\Api\Models\User;
use ChadLinden\Api\Models\Shift;

class ShiftMutator extends Mutator
{

    /**
     * Mutate each record for
     * the API endpoint
     * @param $item
     * @return mixed
     */
    public function mutate(array $item)
    {
        $start =  Carbon::parse($item['start_time']);
        $end = Carbon::parse($item['end_time']);
        // Although ID's and break are
        // stored as proper types, it's
        // helpful to cast here anyway
        return [
            'id' => (int) $item['id'],
            'manager_id' => (int) $item['manager_id'],
            'employee_id' => (int) $item['employee_id'],
            'break' => (float) $item['break'],
            'start_time' => (string) $start->format('r'),
            'end_time' => (string) $end->format('r'),
            'length' => (string) $start->diffInHours( $end )
        ];
    }

    /**
     * returns all shifts with cooworkers
     * assigned shifts at same time
     * @param array $shifts
     * @return array
     */
    public function withCoworkers( array $shifts )
    {
        // TODO: this is really messy/inefficient,
        // if the shift had a name (e.g. kitchen-swing)
        // we could just return each user who is
        // assigned to the same shift/location
        return array_map(function ($shift) {
            $shift = Shift::find( $shift['id'] );
            return [
                'shift' => $this->mutate($shift->getAttributes()),
                'coworkers' =>
                    Shift::whereBetween('start_time', [ $shift->start_time, $shift->end_time ])
                        ->where('employee_id', '!=', $shift->employee_id)
                        ->get()
                        ->map(function ($shift) {
                            if($shift->employee_id && $user = User::find($shift->employee_id) ){
                                return ['name' => $user->name];
                            }
                        })
            ];
        }, $shifts);
    }

    /**
     * returns all shifts with manager
     * assigned shifts at same time
     * @param array $shifts
     * @return array
     */
    public function withManager( array $shifts )
    {
        return array_map(function ($info) {
            //
            $shift = isset($info['shift']) ? $info['shift'] : $info;
            return array_merge(
                $info,
                ['manager' => Shift::find($shift['id'])->manager()]
            );
        }, $shifts);
    }

}