<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class JournalEntry extends Model
{
    use BelongsToTenant;
    use HasFactory;

    protected $fillable = [
        'entry_number',
        'entry_date',
        'reference',
        'description',
        'source_type',
        'source_id',
        'admin_id',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(JournalEntryItem::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function totalDebit(): float
    {
        return (float) $this->items()->sum('debit');
    }

    public function totalCredit(): float
    {
        return (float) $this->items()->sum('credit');
    }

    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
        ];
    }
}
