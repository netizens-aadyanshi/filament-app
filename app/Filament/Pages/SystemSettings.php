<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;
use App\Filament\Clusters\Settings\SettingsCluster;

class SystemSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'System Settings';

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 20;

    protected string $view = 'filament.pages.system-settings';

    protected static ?string $cluster = SettingsCluster::class;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            // General
            'site_name'         => Setting::get('site_name', 'StayDesk Admin'),
            'timezone'          => Setting::get('timezone', 'Asia/Kolkata'),
            'maintenance_mode'  => (bool) Setting::get('maintenance_mode', false),

            // Notifications
            'email_notifications' => (bool) Setting::get('email_notifications', true),
            'db_notifications'    => (bool) Setting::get('db_notifications', true),
            'admin_email'         => Setting::get('admin_email', ''),

            // Display
            'rows_per_page'       => Setting::get('rows_per_page', '25'),
            'brand_color'         => Setting::get('brand_color', '#6366f1'),
            'dark_mode_default'   => (bool) Setting::get('dark_mode_default', false),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->icon('heroicon-o-check')
                ->action('save'),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->statePath('data')
            ->components([
                Section::make('General')
                    ->description('Basic panel configuration')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('site_name')
                                    ->label('Panel Name')
                                    ->required()
                                    ->maxLength(100),

                                Select::make('timezone')
                                    ->label('Timezone')
                                    ->searchable()
                                    ->options(collect(timezone_identifiers_list())
                                        ->mapWithKeys(fn($tz) => [$tz => $tz])
                                        ->toArray()),

                                Toggle::make('maintenance_mode')
                                    ->label('Maintenance Mode')
                                    ->helperText('When enabled, only admins can access the panel'),
                            ]),
                    ]),

                Section::make('Notifications')
                    ->description('Control how and when notifications are sent')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Toggle::make('email_notifications')
                                    ->label('Email Notifications Enabled'),

                                Toggle::make('db_notifications')
                                    ->label('Database Notifications Enabled'),

                                TextInput::make('admin_email')
                                    ->label('Admin Alert Email')
                                    ->email()
                                    ->placeholder('admin@example.com'),
                            ]),
                    ]),

                Section::make('Display')
                    ->description('Appearance and layout preferences')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('rows_per_page')
                                    ->label('Default Rows Per Page')
                                    ->options([
                                        '10'  => '10 rows',
                                        '25'  => '25 rows',
                                        '50'  => '50 rows',
                                        '100' => '100 rows',
                                    ]),

                                ColorPicker::make('brand_color')
                                    ->label('Brand Color'),

                                Toggle::make('dark_mode_default')
                                    ->label('Default to Dark Mode'),
                            ]),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }
}
