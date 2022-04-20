<?php

namespace App\Entity;

enum TournamentCategory: string
{
    case JUNIOR = 'Junior'; // >= 0 and < 18
    case SENIOR = 'Senior'; // >= 18 and < 60
    case VETERAN = 'Veteran'; // >= 60
}