<?php


use Carbon\Carbon;
use Faker\Factory;
use ChadLinden\Api\Models\User;
use ChadLinden\Api\Models\Shift;
class SeedFactory 
{
    private $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    /**
     * @return SeedFactory
     */
    public static function create()
    {
        return new self;
    }

    /***
     * Calls internal methods to
     * generate records
     * @param int $recordsToMake
     * @return array
     */
    public function seed( $recordsToMake = 100)
    {
        /*
         * IMPORTANT: If using this to seed empty database,
         * the users must be added prior to shifts.
         */
        return [
            'users' => [
                'latest' => $this->addUsers($recordsToMake)
            ],
            'shifts' => [
                'latest' => $this->addShifts($recordsToMake)
            ]
        ];
    }

    /***
     * Hard coded function to seed
     * shifts into the shifts table
     * optional assignment for/by id
     * @param $number
     * @param null $forId
     * @param null $byId
     * @return bool
     * @internal param null $for
     * @internal param null $by
     */
    private function addShifts($number, $forId = null, $byId = null)
    {
        $faker = $this->faker;
        $manager = empty($byId) ? User::where('role', 'manager')->get()->random()->id : $byId;
        $employee = empty($forId) ? User::where('role', 'employee')->get()->random()->id : $forId;
        $collection = [];
        while($number){
            $dt = Carbon::now()->subDays(rand(1,5))->hour(rand(6,18))->minute( [00,30][rand(0,1)] )->second(00);
            $collection[] = Shift::create([
                'manager_id'  => $manager,
                'employee_id'  => [$employee, null][rand(0,1)],
                'break'  => $faker->randomFloat(2,0.25,1.5), //decimals, min, max
                'start_time'  => $dt->toDateTimeString(),
                'end_time'  => $dt->addHours( rand(4,12) )->toDateTimeString()
            ]);
            $number--;
        }
        return $collection;
    }

    /***
     * Hard coded function to seed
     * users into the users table.
     * @param $number
     * @param null $role
     * @return static
     */
    private function addUsers($number, $role = null)
    {
        $faker = $this->faker;
        $role = empty($role) ? ['employee', 'manager'][rand(0,4) % 2] : $role;
        $collection = [];
        while($number){
            $user = User::create([
                'name'  => $faker->name,
                'role'  => $role,
                'email'  => $faker->email,
                'phone'  => $faker->phoneNumber,
                'password'  => password_hash( $role, 1 ),
            ]);
            $number--;
        }
        return $user;
    }

    public function makeManager()
    {
        return $this->addUsers(1, 'manager');
    }

    public function makeEmployee( $withShifts = false)
    {
        return $withShifts == false ?
            $this->addUsers(1, 'employee') :
            $this->addShifts(
                $withShifts, $this->addUsers(1, 'employee')->id, $this->makeManager()->id
            );
    }

    public function makeShifts($number, $forId = null, $byId = null)
    {
        return $this->addShifts($number, $forId, $byId);
    }
}