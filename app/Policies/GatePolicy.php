<?php

namespace App\Policies;

use App\Policies\Concerns\AdminOnlyPolicy;

class GatePolicy
{
    use AdminOnlyPolicy;
}
