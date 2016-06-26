<?php

namespace ChadLinden\Api\Domains\User;

use ChadLinden\Api\Authenticator;
use Equip\Payload;
use Carbon\Carbon;
use Equip\Adr\DomainInterface;
use Equip\Adr\PayloadInterface;
use ChadLinden\Api\Models\User;
use ChadLinden\Api\Models\Shift;
use ChadLinden\Api\Domains\Domain;
use ChadLinden\Api\Mutators\ShiftMutator;
use ChadLinden\Api\Mutators\UserMutator;

/**
 * Class GetUser
 * @package ChadLinden\Api\Domains\User
 */
class GetUser extends Domain implements DomainInterface
{
    /**
     * @var
     */
    protected $user;
    /**
     * @var
     */
    protected $output;

    /**
     * GetUser constructor.
     * @param Payload $payload
     * @param Authenticator $auth
     * @param UserMutator $mutator
     */
    public function __construct(
        Payload $payload,
        Authenticator $auth,
        UserMutator $mutator
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
    public function __invoke(array $input)
    {
        $this->authorize($input);

        // Validate input
        if( ! $this->validate( $input ) ){
            return $this->respondMissingField();
        }
        
        // Find user
        $this->user = User::find( $input['user_id'] );

        if( $this->user == '' )
        {
            $this->respondNotFound();
        }
        
        // Safe to say we're OK
        $this->payload = $this->payload->withStatus( Payload::STATUS_OK );

        // Apply mutator for output
        $this->output['user'] = $this->mutator->mutate( $this->user->getAttributes() );
        
        // Get the user's weekly work summary
        $this->output['summary'] = $this->workSummary( $input );

        // Release our beautiful data to the wild
        return  $this->payload->withOutput( $this->output );
    }

    /**
     * @param $input
     * @return bool
     */
    private function validate(array $input)
    {
        // To find user we need a user
        if( ! empty($input['user_id']) ){
            return true;
        }
        
        return false;
    }

    /**
     * @param array $input
     * @return array
     */
    private function workSummary(array $input)
    {
        // Use shift domain's own mutator
        $shiftMutator = new ShiftMutator;
        
        // Get user's shift's from start of week through now
        $shifts = $shiftMutator->mutateMany(Shift::where('employee_id', $this->user->id)
            ->whereDate('start_time', '>', Carbon::now()->startOfWeek()->toDateTimeString())
            ->whereDate('end_time', '<', Carbon::now()->toDateTimeString())
            ->get()
            ->toArray()
        );
        
        $sum = 0;
        $summary = [];
        
        // Add cumulative hours counter
        foreach($shifts as $shift){
            $sum += $shift['length'];
            $summary[] = [
                'shift' => $shift,
                'cumulative' => $sum
            ];
        }

        return $summary;
    }
}