<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CashFlowChart extends ChartWidget
{
    protected static ?string $heading = 'Cash Flow (Last 30 Days)';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 2;

    protected function getData(): array
    {
        $startDate = Carbon::now()->subDays(30);

        $cashInByDate = Transaction::where('type', 'cash-in')
            ->whereDate('transaction_date', '>=', $startDate)
            ->groupBy(DB::raw('DATE(transaction_date)'))
            ->orderBy(DB::raw('DATE(transaction_date)'))
            ->pluck(DB::raw('SUM(amount) as total'), DB::raw('DATE(transaction_date)'))
            ->toArray();

        $cashOutByDate = Transaction::where('type', 'cash-out')
            ->whereDate('transaction_date', '>=', $startDate)
            ->groupBy(DB::raw('DATE(transaction_date)'))
            ->orderBy(DB::raw('DATE(transaction_date)'))
            ->pluck(DB::raw('SUM(amount) as total'), DB::raw('DATE(transaction_date)'))
            ->toArray();

        $labels = collect($cashInByDate)->keys()->merge(collect($cashOutByDate)->keys())
            ->unique()
            ->sort()
            ->values()
            ->map(fn($date) => Carbon::parse($date)->format('M d'))
            ->toArray();

        $cashInData = [];
        $cashOutData = [];

        foreach ($labels as $label) {
            $date = Carbon::createFromFormat('M d', $label)->year(now()->year)->format('Y-m-d');
            $cashInData[] = $cashInByDate[$date] ?? 0;
            $cashOutData[] = $cashOutByDate[$date] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Cash In',
                    'data' => $cashInData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Cash Out',
                    'data' => $cashOutData,
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
