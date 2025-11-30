<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Collection;

class ExcelExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    use Exportable;

    protected Collection $data;
    protected array $columns;

    public function __construct(Collection $data, array $columns)
    {
        $this->data = $data;
        $this->columns = $columns;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return array_values($this->columns);
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        $mapped = [];

        foreach (array_keys($this->columns) as $attribute) {
            $value = $this->getValue($row, $attribute);
            $mapped[] = $value;
        }

        return $mapped;
    }

    /**
     * Get value from model using dot notation for relationships
     *
     * @param mixed $row
     * @param string $attribute
     * @return mixed
     */
    protected function getValue($row, string $attribute)
    {
        // Handle dot notation for relationships (e.g., 'user.name')
        if (str_contains($attribute, '.')) {
            $parts = explode('.', $attribute);
            $value = $row;

            foreach ($parts as $part) {
                if ($value && is_object($value)) {
                    $value = $value->{$part} ?? null;
                } else {
                    return null;
                }
            }

            return $value;
        }

        // Handle boolean values
        if (isset($row->{$attribute})) {
            $value = $row->{$attribute};

            if (is_bool($value)) {
                return $value ? 'Yes' : 'No';
            }

            // Handle dates
            if ($value instanceof \DateTime || $value instanceof \Carbon\Carbon) {
                return $value->format('Y-m-d H:i:s');
            }

            return $value;
        }

        return null;
    }

    /**
     * Apply styles to the worksheet
     *
     * @param Worksheet $sheet
     * @return void
     */
    public function styles(Worksheet $sheet)
    {
        // Style the header row
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Set row height for header
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Auto-size columns
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
}

