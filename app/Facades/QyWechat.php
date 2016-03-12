<?php 

namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class QyWechat extends Facade{

    protected static function getFacadeAccessor() { return 'qywechat'; }
}