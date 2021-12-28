<?php
/**
 * Created by PhpStorm.
 * User: snower
 * Date: 18/3/6
 * Time: 上午11:32
 */

namespace Snower\Phslock\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class Phslock extends Facade
{

    public static function getFacadeAccessor()
    {
        return 'phslock';
    }
}