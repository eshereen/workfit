<?php

namespace App\Exports;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Collection;

class PdfExport
{
    protected Collection $data;
    protected array $columns;
    protected string $title;

    public function __construct(Collection $data, array $columns, string $title = 'Export')
    {
        $this->data = $data;
        $this->columns = $columns;
        $this->title = $title;
    }

    /**
     * Download the PDF file
     *
     * @param string $filename
     * @return \Illuminate\Http\Response
     */
    public function download(string $filename)
    {
        $html = $this->generateHtml();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return response()->streamDownload(function () use ($dompdf) {
            echo $dompdf->output();
        }, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Generate HTML content for PDF
     *
     * @return string
     */
    protected function generateHtml(): string
    {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            margin: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #4472C4;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .header-info {
            margin-bottom: 20px;
            text-align: right;
            color: #666;
        }
    </style>
</head>
<body>
    <h1>' . htmlspecialchars($this->title) . ' Report</h1>
    <div class="header-info">
        Generated: ' . now()->format('Y-m-d H:i:s') . '<br>
        Total Records: ' . $this->data->count() . '
    </div>
    <table>
        <thead>
            <tr>';

        foreach ($this->columns as $label) {
            $html .= '<th>' . htmlspecialchars($label) . '</th>';
        }

        $html .= '</tr>
        </thead>
        <tbody>';

        foreach ($this->data as $row) {
            $html .= '<tr>';
            foreach (array_keys($this->columns) as $attribute) {
                $value = $this->getValue($row, $attribute);
                $html .= '<td>' . htmlspecialchars($this->formatValue($value)) . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody>
    </table>
</body>
</html>';

        return $html;
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

        return $row->{$attribute} ?? null;
    }

    /**
     * Format value for display
     *
     * @param mixed $value
     * @return string
     */
    protected function formatValue($value): string
    {
        if (is_null($value)) {
            return '-';
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if ($value instanceof \DateTime || $value instanceof \Carbon\Carbon) {
            return $value->format('Y-m-d H:i:s');
        }

        return (string) $value;
    }
}

