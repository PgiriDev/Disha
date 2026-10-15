<?php

namespace App\Policies;

use App\Policies\Concerns\AdminOnlyPolicy;

class TerminalPolicy
{
    use AdminOnlyPolicy;
}
