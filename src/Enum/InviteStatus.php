<?php

namespace App\Enum;

enum InviteStatus : string
{
    case ACCEPTED = 'accepted';
    case DECLINED = 'declined';
    case PENDING = 'pending';

}