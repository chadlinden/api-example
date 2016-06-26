<?php
namespace ChadLinden\Api\Models;


use Carbon\Carbon;
use ChadLinden\Api\AuthHandler;

/**
 * Class Token
 * @package ChadLinden\Api\Models
 */
class Token extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'token',
        'expires',
        'user_id'
    ];

    /**
     * @var array
     */
    protected $dates = [
        'expires'
    ];


    /**
     * @param $input
     * @return mixed
     */
    public static function getInputToken(array $input)
    {
        $model = self::where('token', self::extract($input) )->latest()->first();

        return $model;
    }

    /**
     * @param $input
     * @return mixed
     */
    public static function extract(array $input )
    {
        return $input[AuthHandler::IDENTITY];
    }

    /**
     * @param $input
     */
    public static function getUser(array $input)
    {
        $model = self::where('token', self::extract($input))->latest()->first();
        return $model->user();
    }

    /**
     * @return mixed
     */
    public function expired()
    {
        return $this->expires->isPast();
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return User::find( $this->user_id );
    }

    public function refresh()
    {
        return $this->update(['expires' => Carbon::now()->addHour()->toDateTimeString() ]);
    }
}