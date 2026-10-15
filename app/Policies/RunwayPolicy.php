<?php

namespace App\Policies;

use App\Policies\Concerns\AdminOnlyPolicy;

class RunwayPolicy
{
    use AdminOnlyPolicy;
}
