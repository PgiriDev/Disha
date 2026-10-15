<?php

namespace App\Policies;

use App\Policies\Concerns\AdminOnlyPolicy;

class AirlinePolicy
{
    use AdminOnlyPolicy;
}
