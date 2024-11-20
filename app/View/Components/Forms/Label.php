<?php

namespace App\View\Components\Forms;

use BladeUIKit\Components\Forms\Label as OriginalLabel;

class Label extends OriginalLabel
{
    public function fallback(): string
    {
        return ucwords(str_replace('_', ' ', $this->for));
    }
}
