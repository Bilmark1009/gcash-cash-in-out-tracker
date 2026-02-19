<?php

namespace App\Filament\Admin\Resources\TransactionResource\Pages;

use App\Filament\Admin\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ExportAction::make()
                ->exports([
                    ExcelExport::make()
                        ->fromTable()
                        ->withFilename('transactions-' . date('Y-m-d')),
                ])
                ->label('Export to Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success'),
            Actions\Action::make('export_pdf')
                ->label('Export to PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action(function () {
                    $transactions = TransactionResource::getEloquentQuery()
                        ->orderBy('transaction_date', 'desc')
                        ->get();

                    $pdf = Pdf::loadView('pdf.transactions', [
                        'transactions' => $transactions,
                    ]);

                    return Response::streamDownload(function () use ($pdf) {
                        echo $pdf->stream();
                    }, 'transactions-' . date('Y-m-d') . '.pdf');
                }),
        ];
    }
}
