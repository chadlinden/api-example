<?php

use ChadLinden\Api\Models\Shift;
use ChadLinden\Api\Models\User;
use PHPUnit_Framework_TestCase;
class EmployeeTests extends PHPUnit_Framework_TestCase
{
    private $employee;
    private $manager;
    private $shift;

    public function init()
    {
        $this->employee = new User([
            'name'  => 'test user',
            'role'  => 'employee',
            'email' => 'testuser@email.com',
            'phone' => '651-555-1234',
        ]);
        $this->employee->save();

        $this->manager = new User([
            'name'  => 'test manager',
            'role'  => 'manager',
            'email' => 'testuser@manager.com',
            'phone' => '651-555-4321',
        ]);
        $this->manager->save();

        $this->shift = new Shift([
            'employee_id'   => $this->employee->id,
            'start_time'    => '2090-01-01 01:00:00',
            'manager_id'    => $this->manager->id,
            'end_time'  => '2090-01-01 09:00:00',
            'break' => 0.25,
        ]);
        $this->shift->save();
    }

    public function test_an_employee_can_see_shifts()
    {
        
    }
}