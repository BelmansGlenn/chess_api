<?php

namespace App\Entity;

enum MatchResultEnum : string
{
    case NOT_PLAYED = 'Not played';
    case WHITE_WIN = 'White';
    case BLACK_WIN = 'Black';
    case TIE = 'Tie';
}