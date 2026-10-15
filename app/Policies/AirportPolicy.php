<?php

namespace App\Policies;

use App\Policies\Concerns\AdminOnlyPolicy;

class AirportPolicy
{
    use AdminOnlyPolicy;
}
