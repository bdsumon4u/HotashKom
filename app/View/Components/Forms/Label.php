<?php

namespace App\View\Components\Forms;

use BladeUIKit\Components\Forms\Label as OriginalLabel;

class Label extends OriginalLabel
{
    #[\Override]
    public function fallback(): string
    {
        return ucwords(str_replace('_', ' ', $this->for));
    }
}
