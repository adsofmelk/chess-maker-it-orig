<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Partidas extends Model
{
    protected $fillable = ['user1','user2','status','channel_ably','moves_pieces','distribution','who_begin','winner','loser','score_winner','score_winner','comments', 'moves', 'board_data','type',];
}
