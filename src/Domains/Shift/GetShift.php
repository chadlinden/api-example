<?php

namespace ChadLinden\Api\Domains\Shift;

use Equip\Payload;
use Equip\Adr\DomainInterface;
use Equip\Adr\PayloadInterface;
use ChadLinden\Api\AuthHandler;
use ChadLinden\Api\Models\Shift;
use ChadLinden\Api\Authenticator;
use ChadLinden\Api\Domains\Domain;
use ChadLinden\Api\Mutators\ShiftMutator;

/**
 * Class GetShift
 * @package ChadLinden\Api\Domains\Shift
 */
class GetShift extends Domain implements DomainInterface
{
    /**
     * @var Authenticator
     */
    protected $auth;
    /**
     * @var
     */
    protected $shifts;
    /**
     * @var Payload
     */
    protected $payload;
    /**
     * @var ShiftMutator
     */
    protected $mutator;

    /**
     * GetShift constructor.
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
     * Handle domain logic for an action.
     * @param array $input
     * @return PayloadInterface
     */
    public function __invoke( array $input )
    {
        if( ! $this->authorize( $input ) ){
            return $this->respondNotAuthorized('invalid or expired token');
        }

        // Validate input
        if( ! $this->validate( $input ) ){
            return $this->respondMissingField();
        }

        // Request is okay, it's on me from here
        $this->payload = $this->payload->withStatus( Payload::STATUS_OK );

        // Find all assigned shifts
        $this->shifts['assigned'] = $this->setAssignedList( $input );

        // If requested, add coworkers to each shift
        if( ! empty($input['coworkers']) && $input['coworkers'] == true ){
            $this->shifts['assigned'] = $this->mutator->withCoworkers( $this->shifts['assigned'] );
        }

        // If requested, add manager contact to each shift
        if( ! empty($input['manager']) && $input['manager'] == true ){
            $this->shifts['assigned'] = $this->mutator->withManager( $this->shifts['assigned'] );
        }

        // If requested, append list of available shifts
        $this->shifts['available'] = empty( $input['all_open'] ) ? [] :
            $this->setAvailableList( $input );

        // We return the input to the
        // caller so we remove this here
        unset($input[AuthHandler::IDENTITY]);

        return $this->payload->withOutput(
            array_merge( $input, $this->shifts )
        );
    }

    /**
     * Returns the shifts assigned to
     * userId between start and end
     * @param $input
     * @return array
     * @internal param $userId
     */
    private function setAssignedList( $input )
    {
        // Start querey with date requirements
        $query = Shift::whereDate('start_time', '>', $input['start'])
            ->whereDate('end_time', '<', $input['end']);

        // check if we're retrieving all
        // or only retrieving user shifts
        if ($input['user_id'] !== "all") {
            $query->where('employee_id', $input['user_id']);
        }

        // return mutated list
        return $this->mutator
            ->mutateMany(
                $query->get()
                    ->toArray()
            );
    }

    /**
     * Returns list of shifts between
     * start_time and end_time which
     * haven't been assigned to user
     * @param $input
     * @return array
     */
    private function setAvailableList( $input )
    {
        // return shifts without assignment
        return $this->mutator->mutateMany(
            Shift::where('employee_id', 0)
                ->whereDate( 'start_time', '>', $input['start'])
                ->whereDate( 'end_time', '<', $input['end'])
                ->get()
                ->toArray()
        );
    }

    /**
     * @param $input
     * @return bool
     */
    private function validate($input)
    {
        // Minimum required fields
        if( ! empty($input['user_id']) && ! empty($input['start']) && ! empty($input['end']) ){
            return true;
        }
        return false;
    }

}