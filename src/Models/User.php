<?php
namespace ChadLinden\Api\Models;

class User extends Model
{
    protected $fillable = [
        'name',
        'role',
        'email',
        'phone',
    ];

    /**
     * 
     */
    public function shifts()
    {
        $fk = $this->role.'_id';
        return Shift::where($fk, $this->id)->get();
    }
    
}