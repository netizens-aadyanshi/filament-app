<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Section::make('Account Information')
                    ->schema([
                        TextInput::make('name')->label('Full Name')->required()->maxLength(255),
                        TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
                        TextInput::make('password')->password()->dehydrated(fn ($state) => filled($state))->required(fn (string $operation) => $operation === 'create')->minLength(8),
                        TextInput::make('phone_number')->label('Phone Number')->tel()->mask('(999) 999-9999')->placeholder('(999) 999-9999'),
                    ])->collapsible(),

                Section::make('Roles & Permissions')
                    ->schema([
                        Select::make('role')->options([
                            'admin' => 'Admin',
                            'customer' => 'Customer',

                        ])->default('customer')->required()->searchable()->native(false),
                        Toggle::make('is_email_verified')
                            ->label('Mark Email as Verified')
                            ->inlineLabel()
                            ->dehydrated(false)
                            ->afterStateHydrated(function ($component, $record) {
                                $component->state($record?->email_verified_at !== null);
                            })
                            ->afterStateUpdated(function ($state, $record) {
                                $record->email_verified_at = $state ? now() : null;
                                $record->save();
                            }),
                        ToggleButtons::make('status')
                            ->options([
                                'active' => 'Success',
                                'suspended' => 'Warning',
                                'banned' => 'Danger',
                            ])->colors([
                            'active' => 'Success',
                            'suspended' => 'Warning',
                            'banned' => 'Danger',
                        ])->icons([
                            'active' => 'heroicon-o-check',
                            'suspended' => 'heroicon-o-pause',
                            'banned' => 'heroicon-o-no-symbol',
                        ])->inline(),
                    ])->collapsed(),

                Checkbox::make('agree_to_terms')
                    ->label('I agree to the terms and conditions')
                    ->required()
                    ->dehydrated(false),

                // CheckboxList::make('interests')
                //     ->label('Interests')
                //     ->options([
                //         'electronics' => 'Electronics',
                //         'clothing' => 'Clothing',
                //         'books' => 'Books',
                //         'food' => 'Food',
                //         'technology' => 'Technology'
                //     ])->columns(2)->searchable()->bulkToggleable(true),

                Grid::make()->gridContainer()
                    ->columns([
                        '@md' => 3,
                        '@xl' => 4,
                        '!@md' => 2,
                        '!@xl' => 3,
                    ])->schema([
                        DateTimePicker::make('email_verified_at')->label('Email Verified At')
                            ->displayFormat('d M Y H:i A')->minDate(now()->subYear(1))->maxDate(now())->native(false)->seconds(false),
                        FileUpload::make('profile_photo')->label('Profile Photo')->image()->imageEditor()->imagePreviewHeight(100)->circleCropper()->maxSize(1024),
                    ]),

                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Addresses')
                            ->schema([
                                Repeater::make('address')->label('Saved Addresses')->schema([
                                    TextInput::make('label')->placeholder('Home|Work|Other')->required(),
                                    TextInput::make('street')->label('Street')->required(),
                                    TextInput::make('city')->label('City')->required(),
                                    Select::make('type')->label('Address Type')->options([
                                        'residential' => 'Residential',
                                        'commercial' => 'Commercial',
                                    ])->required(),
                                ])->minItems(0)->maxItems(3)->collapsible()->cloneable(),
                            ]),
                        Tab::make('Status')
                            ->schema([
                                Radio::make('status')
                                    ->label('Account Status')
                                    ->options([
                                        'active' => 'Active',
                                        'suspended' => 'Suspended',
                                        'banned' => 'Banned',
                                    ])
                                    ->descriptions([
                                        'active' => 'User can log in normally',
                                        'suspended' => 'Temporary restriction',
                                        'banned' => 'Permanent restriction',
                                    ])
                                    ->live()
                                    ->default('active')
                                    ->inline(),
                            ]),
                    ]),

                Section::make('Preferences & Settings')
                    ->schema([
                        TagsInput::make('interests')
                            ->label('Interests')
                            ->suggestions([
                                'Electronics',
                                'Clothing',
                                'Books',
                                'Food',
                                'Technology',
                                'Gaming',
                                'Travel',
                            ])
                            ->separator(',')
                            ->placeholder('Add an interest...')
                            ->reorderable(),
                        KeyValue::make('preferences')->label('User Prefernces')->keyLabel('Setting')->valueLabel('Value')->reorderable()->addButtonLabel('Add Preference'),
                        ColorPicker::make('label_color')->label('Label Color')->rgba(),

                        Slider::make('trust_score1')
                            ->range(minValue: 0, maxValue: 100)->step(5)
                            ->pipsValues([0,  25,  50,  75,  100]),

                    ]),

                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('bio')
                            ->schema([
                                RichEditor::make('bio')->label('Biography')->toolbarButtons([
                                    'bold',
                                    'italic',
                                    'underline',
                                    'bulletList',
                                    'orderedList',
                                    'link',
                                ]),
                            ]),
                        Tab::make('notes')
                            ->schema([
                                MarkdownEditor::make('notes')->label('Internal Notes'),
                            ]),
                    ]),

                Fieldset::make('Restriction Details')
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                        'xl' => 3,
                    ])
                    ->schema([
                        Textarea::make('ban_reason')
                            ->label('Ban Reason')
                            ->nullable()
                            ->rows(4)
                            ->cols(20)
                            ->live()
                            ->visible(fn (Get $get) => $get('status') == 'banned')
                            ->minLength(10)
                            ->maxLength(500),

                    ]),

            ]);
    }
}
