<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QyVoteNode extends Model
{
    protected $fillable = ['title','vid','type','status','percent'];
}
