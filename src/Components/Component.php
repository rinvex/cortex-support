<?php

declare(strict_types=1);

namespace Cortex\Support\Components;

use Cortex\Support\Concerns\Macroable;
use Illuminate\Support\Traits\Tappable;
use Cortex\Support\Concerns\Configurable;
use Illuminate\Support\Traits\Conditionable;
use Cortex\Support\Concerns\EvaluatesClosures;

abstract class Component
{
    use Conditionable;
    use Configurable;
    use EvaluatesClosures;
    use Macroable;
    use Tappable;
}
