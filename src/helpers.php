<?php

if( ! function_exists('print_r2') )
{
    /**
     * @param $expression
     * @param bool $exit
     */
    function print_r2( $expression, $exit = true )
    {
        echo '<pre>';
        print_r( $expression );
        echo '</pre>';
        if($exit) exit();
    }
}

if( ! function_exists('var_dump2') )
{
    /**
     * @param $expression
     * @param bool $exit
     */
    function var_dump2( $expression, $exit = true )
    {
        var_dump( $expression );
        if($exit) exit();
    }
}