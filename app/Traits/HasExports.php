<?php

namespace App\Traits;

use App\Exports\ExcelExport;
use App\Exports\PdfExport;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

trait HasExports
{
    /**
     * Get export bulk actions for selected records
     *
     * @param string|null $modelClass The model class name (e.g., Newsletter::class)
     * @param array|null $columns Array of column definitions ['attribute' => 'Label']
     * @param string|null $fileName Custom filename (without extension)
     * @return array
     */
    public static function getExportBulkActions(?string $modelClass = null, ?array $columns = null, ?string $fileName = null): array
    {
        // Safely access the model property from the Resource class
        if ($modelClass === null) {
            $reflection = new \ReflectionClass(static::class);
            if ($reflection->hasProperty('model') && $reflection->getProperty('model')->isStatic()) {
                $modelProperty = $reflection->getProperty('model');
                $modelProperty->setAccessible(true);
                $modelClass = $modelProperty->getValue();
            }
        }

        if ($modelClass === null) {
            throw new \RuntimeException('Model class must be provided or defined in the resource.');
        }

        $resourceName = class_basename($modelClass);
        $fileName = $fileName ?? strtolower($resourceName);
        $exportColumns = $columns ?? static::getDefaultExportColumns();

        return [
            BulkAction::make('exportExcel')
                ->label('Export Selected to Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function (Collection $records) use ($exportColumns, $fileName) {
                    $export = new ExcelExport($records, $exportColumns);
                    return $export->download($fileName . '_selected_' . now()->format('Y-m-d') . '.xlsx');
                })
                ->deselectRecordsAfterCompletion(),

            BulkAction::make('exportPdf')
                ->label('Export Selected to PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action(function (Collection $records) use ($exportColumns, $fileName, $resourceName) {
                    $export = new PdfExport($records, $exportColumns, $resourceName);
                    return $export->download($fileName . '_selected_' . now()->format('Y-m-d') . '.pdf');
                })
                ->deselectRecordsAfterCompletion(),
        ];
    }

    /**
     * Get default export columns based on table columns
     * Override this method in your resource to customize columns
     *
     * @return array Format: ['attribute' => 'Label', 'relationship.attribute' => 'Label']
     */
    public static function getDefaultExportColumns(): array
    {
        // This will be overridden by resources that use this trait
        return [];
    }
}

