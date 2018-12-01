<?php

namespace Api\DB;


use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $guarded = [];
    protected $table = 'log';
    protected $keyType = 'integer';
}