<?php

namespace App\Entity;

enum MatchResultEnum : string
{
    case WHITE_WIN = 'White';
    case BLACK_WIN = 'Black';
    case TIE = 'Tie';
}