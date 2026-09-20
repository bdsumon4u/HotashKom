<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransactionCategory extends Model
{
    use BelongsToTenant;
    use HasFactory;

    public const TYPE_EXPENSE = 'expense';

    public const TYPE_INCOME = 'income';

    public const TYPE_BOTH = 'both';

    protected $fillable = [
        'name',
        'type',
        'description',
        'is_active',
    ];

    public function journalEntryItems(): HasMany
    {
        return $this->hasMany(JournalEntryItem::class, 'category_id');
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
