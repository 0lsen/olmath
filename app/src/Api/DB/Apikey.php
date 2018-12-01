<?php

namespace Api\DB;


use Illuminate\Database\Eloquent\Model;

class Apikey extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    protected $table = 'apikey';
    protected $keyType = 'string';
}