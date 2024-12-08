<?php

namespace App\Enums;

enum CodePermission: int
{
    case ADMIN = 1;
    case USER = 2;
    case CLIENT = 3;
}
