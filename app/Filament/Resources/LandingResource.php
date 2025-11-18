<?php

namespace App\Filament\Resources;

use App\Filament\Fabricator\Layouts\DefaultLayout;
use Closure;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;
use SplFileInfo;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;
use Z3d0X\FilamentFabricator\Forms\Components\PageBuilder;
use Z3d0X\FilamentFabricator\Models\Contracts\Page as PageContract;
use Z3d0X\FilamentFabricator\Resources\PageResource;
use Z3d0X\FilamentFabricator\View\ResourceSchemaSlot;

class LandingResource extends PageResource
{
    #[\Override]
    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Group::make()
                    ->schema([
                        Group::make()->schema(FilamentFabricator::getSchemaSlot(ResourceSchemaSlot::BLOCKS_BEFORE)),

                        PageBuilder::make('blocks')
                            ->blocks(fn (Get $get): array => array_filter(FilamentFabricator::getPageBlocks(), fn ($block) => Str::startsWith($block->getName(), $get('layout').'.')))
                            ->label(__('filament-fabricator::page-resource.labels.blocks')),

                        Group::make()->schema(FilamentFabricator::getSchemaSlot(ResourceSchemaSlot::BLOCKS_AFTER)),
                    ])
                    ->columnSpan(2),

                Group::make()
                    ->columnSpan(1)
                    ->schema([
                        Group::make()->schema(FilamentFabricator::getSchemaSlot(ResourceSchemaSlot::SIDEBAR_BEFORE)),

                        Section::make()
                            ->schema([
                                Placeholder::make('page_url')
                                    ->label(__('filament-fabricator::page-resource.labels.url'))
                                    ->visible(fn (?PageContract $record): bool => config('filament-fabricator.routing.enabled') && filled($record))
                                    ->content(fn (?PageContract $record) => FilamentFabricator::getPageUrlFromId($record?->id)),

                                TextInput::make('title')
                                    ->label(__('filament-fabricator::page-resource.labels.title'))
                                    ->default(fn () => Filament::getTenant()->name)
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state, ?PageContract $record): void {
                                        if (! $get('is_slug_changed_manually') && filled($state) && blank($record)) {
                                            $set('slug', Str::slug($state, language: config('app.locale', 'en')));
                                        }
                                    })
                                    ->debounce('500ms')
                                    ->required(),

                                Hidden::make('is_slug_changed_manually')
                                    ->default(false)
                                    ->dehydrated(false),

                                TextInput::make('slug')
                                    ->label('Link')
                                    ->default(fn () => Filament::getTenant()->slug)
                                    ->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule, Get $get) => $rule->where('parent_id', $get('parent_id')))
                                    ->afterStateUpdated(function (Set $set): void {
                                        $set('is_slug_changed_manually', true);
                                    })
                                    ->rule(fn ($state): \Closure => function (string $attribute, $value, Closure $fail) use ($state): void {
                                        if ($state !== '/' && (Str::startsWith($value, '/') || Str::endsWith($value, '/'))) {
                                            $fail(__('filament-fabricator::page-resource.errors.slug_starts_or_ends_with_slash'));
                                        }
                                        if ($state !== '/' && ! preg_match('/^[a-zA-Z0-9-]+$/', $value)) {
                                            $fail('The link field may only contain letters, numbers, and hyphens. No spaces or special characters are allowed.');
                                        }
                                    })
                                    ->required(),

                                Select::make('layout')
                                    ->label(__('filament-fabricator::page-resource.labels.layout'))
                                    ->options(FilamentFabricator::getLayouts())
                                    ->default(fn () => FilamentFabricator::getDefaultLayoutName())
                                    ->live()
                                    ->required()
                                    ->disableOptionWhen(fn (string $value): bool => $value === FilamentFabricator::getDefaultLayoutName())
                                    ->afterStateUpdated(fn (?PageContract $record, Get $get, Set $set) => static::getPageBlocks($record, $get, $set)),

                                Select::make('parent_id')
                                    ->label(__('filament-fabricator::page-resource.labels.parent'))
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->suffixAction(
                                        fn ($get, $context): \Filament\Forms\Components\Actions\Action => FormAction::make($context.'-parent')
                                            ->icon('heroicon-o-arrow-top-right-on-square')
                                            ->url(fn (): string => PageResource::getUrl($context, ['record' => $get('parent_id')]))
                                            ->openUrlInNewTab()
                                            ->visible(fn (): bool => filled($get('parent_id')))
                                    )
                                    ->relationship(
                                        'parent',
                                        'title',
                                        function (Builder $query, ?PageContract $record): void {
                                            if (filled($record)) {
                                                $query->where('id', '!=', $record->id);
                                            }
                                        }
                                    )
                                    ->hidden(),
                            ]),

                        Group::make()->schema(FilamentFabricator::getSchemaSlot(ResourceSchemaSlot::SIDEBAR_AFTER)),
                    ]),

            ]);
    }

    public static function getPageBlocks(?PageContract $record, Get $get, Set $set)
    {
        $layoutName = collect(File::allFiles(app_path('Filament/Fabricator/Layouts')))
            ->filter(fn (SplFileInfo $file): bool => $file->getExtension() === 'php')
            ->first(fn (SplFileInfo $file) => Str::of($file->getFilename())->before('Layout.php')->kebab()->is($get('layout')))
            ->getFileNameWithoutExtension();

        if (! $get('is_slug_changed_manually') && filled($get('title'))) {
            $set('slug', Str::of($layoutName)->beforeLast('Layout')->prepend($get('title'))->slug('-', config('app.locale', 'en')));
        }

        $blocks = collect($record?->blocks)->filter(fn ($block) => Str::startsWith($block['type'], Str::of($layoutName)->replaceLast('Layout', '.')->kebab()));

        if ($blocks->isNotEmpty()) {
            return $set('blocks', $blocks->toArray());
        }

        $set('blocks', Str::of(DefaultLayout::class)
            ->replace('DefaultLayout', $layoutName)
            ->__toString()::getPageBlocks($record, $get, $set));
    }
}
