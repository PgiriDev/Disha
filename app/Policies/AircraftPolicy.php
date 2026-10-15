<?php

namespace App\Policies;

use App\Policies\Concerns\AdminOnlyPolicy;

class AircraftPolicy
{
    use AdminOnlyPolicy;
}
