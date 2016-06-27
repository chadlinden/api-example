<?php
/**
 * Created by PhpStorm.
 * User: Spock
 * Date: 6/26/2016
 * Time: 3:55 PM
 */

namespace ChadLinden\Api\Domains\Shift;

use Equip\Payload;
use Equip\Adr\DomainInterface;
use Equip\Adr\PayloadInterface;
use ChadLinden\Api\Models\Shift;
use ChadLinden\Api\Authenticator;
use ChadLinden\Api\Domains\Domain;
use ChadLinden\Api\Mutators\ShiftMutator;

class PutShift extends Domain implements DomainInterface
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
        if( ! $this->authorize( $input ) ){
            return $this->respondNotAuthorized('invalid or expired token');
        }

        // Validate input
        if( ! $this->validate( $input ) ){
            return $this->respondMissingField();
        }

        // Use the currently authenticated
        // manager id to create new shift
        $input['manager_id'] = $this->auth->getUser()->id;

        // Input has bee validated, create
        $this->shift = Shift::find($input['shift_id']);
        
        if( $this->shift == '' ){
            return $this->respondNotFound("shift with id {$input['shift_id']} not found");
        }

        $attributes = $this->shift->getAttributes();
        $this->shift->update( $input );

        $diff = array_diff($this->shift->getAttributes(), $attributes);

        return $this->payload->withOutput([
            'updated' => $this->shift,
            'diff' => $diff
        ]);
    }

    /**
     * @param $input
     * @return bool
     */
    private function validate($input)
    {
        // Minimum fields
        if( ! empty($input['shift_id']) ){
            return true;
        }
        return false;
    }
}