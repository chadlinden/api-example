<?php

namespace ChadLinden\Api\Domains\Shift;

use Equip\Payload;
use Equip\Adr\DomainInterface;
use Equip\Adr\PayloadInterface;
use ChadLinden\Api\Models\Shift;
use ChadLinden\Api\Authenticator;
use ChadLinden\Api\Domains\Domain;
use ChadLinden\Api\Mutators\ShiftMutator;

/**
 * Class PostShift
 * @package ChadLinden\Api\Domains\Shift
 */
class PostShift extends Domain implements DomainInterface
{
    /**
     * @var
     */
    protected $shift;

    /**
     * PostShift constructor.
     * @param Payload $payload
     * @param Authenticator $auth
     * @param ShiftMutator $mutator
     */
    public function __construct(
        Payload $payload,
        Authenticator $auth,
        ShiftMutator $mutator
    )
    {
        $this->auth = $auth;
        $this->payload = $payload;
        $this->mutator = $mutator;
    }
    /**
     * @param array $input
     * @return PayloadInterface
     */
    public function __invoke(array $input)
    {
        $this->authorize( $input );

        // Validate input
        if( ! $this->validate( $input ) ){
            return $this->respondMissingField();
        }

        // Use the currently authenticated
        // manager id to create new shift
        $input['manager_id'] = $this->auth->getUser()->id;

        if( Shift::where('employee_id', $input['employee_id'])
                    ->where('start_time', '>=', $input['start_time'])
                    ->where('end_time', '<=', $input['end_time'])
                    ->count() > 0 ){
            return $this->respondNotAccepted('employee already schedule during that time');
        }

        // Input has bee validated, create
        $this->shift = Shift::create($input);

        return $this->payload->withOutput(['created' => $this->shift]);
    }

    /**
     * @param $input
     * @return bool
     */
    private function validate($input)
    {
        // Minimum fields
        if( ! empty($input['employee_id'])
            && ! empty($input['break'])
            && ! empty($input['start_time'])
            && ! empty($input['end_time'])
        ){
            return true;
        }

        return false;
    }
}