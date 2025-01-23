<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BarChart;
use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use App\Filament\Widgets\PieChart;
use App\Livewire\HistoricChart;
use App\Livewire\RadarChart;
use App\Livewire\RequeridosPChart;
use App\Livewire\RotacionChart;

class Dashboardt extends Page
{

    use HasFiltersForm;
    protected static ?string $navigationGroup = 'Panel Administrativo';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';
    protected static ?string $navigationLabel = 'Panel Histórico';
    protected static ?string $title = 'Históricos';

    protected static string $view = 'filament.pages.dashboardt';

    public function filtersForm(Form $form): Form
    {
        return $form
        ->schema([
            Section::make()
                ->schema([
                    DatePicker::make('startDate')
                        ->label('Desde'),
                    DatePicker::make('endDate')
                        ->label('Hasta'),
                    Select::make('period')
                        ->label('Período')
                        ->options([
                            'year' => 'Año',
                            'month' => 'Mes',
                            'week' => 'Semana',
                            'day' => 'Día',
                        ])
                        ->default('month') // Default period
                        ->required(),
                ])
                ->columns(3),
        ]);
    }
    protected function getFooterWidgets(): array
    {
        return [
            HistoricChart::make(['filters' => $this->filters]),
            RadarChart::make(['filters'=> $this->filters]),
         //  RequeridosPChart::make(['filters' => $this->filters]),

        ];
    }
}
