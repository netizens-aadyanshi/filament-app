<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Carbon;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use UnitEnum;
use App\Filament\Clusters\Settings\SettingsCluster;

class UserActivityReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = UserResource::class;

    protected static ?string $title = 'User Activity Report';

    protected static ?string $navigationLabel = 'Activity Report';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|UnitEnum|null $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = SettingsCluster::class;

    protected string $view = 'filament.resources.users.pages.user-activity-report';

    // protected string $view = 'filament-panels::resources.pages.UserActivityReport';

    public ?array $data = [];

    public function mount(): void
    {
        // Initialize the form with empty data
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([ // Note: V5 uses 'components' instead of 'schema' on the base object

                    DatePicker::make('date_from')
                        ->label('From')
                        ->default(Carbon::now()->startOfMonth())
                        ->native(false)
                        ->required(),

                    DatePicker::make('date_to')
                        ->label('To')
                        ->default(Carbon::now())
                        ->native(false)
                        ->required(),

                    Select::make('role')
                        ->options([
                            'all' => 'All',
                            'admin' => 'Admin',
                            'customer' => 'Customer',
                        ])
                        ->default('all')
                        ->selectablePlaceholder(false),

                    Select::make('status')
                        ->options([
                            'all' => 'All',
                            'active' => 'Active',
                            'suspended' => 'Suspended',
                            'banned' => 'Banned',
                        ])
                        ->default('all')
                        ->selectablePlaceholder(false),

        ])
            ->statePath('data');
    }

    public function create(): void
    {
        $formData = $this->form->getState();
        // Handle your logic here (e.g., generating a PDF or filtering a table)
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('generate')
                ->label('Generate Report')
                ->submit('create')
                ->color('primary'),
        ];
    }
}
