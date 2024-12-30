<?php

namespace App\Enum;

enum ParticipantStatus : string
{
    case VIEWER = 'viewer'; // can see and add expenses
    case EDITOR = 'editor'; // can edit itinerary and Viewer actions
    case OWNER = 'owner'; // full access

}