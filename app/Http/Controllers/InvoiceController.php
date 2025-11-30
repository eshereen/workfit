<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display invoice page
     */
    public function show(Order $order)
    {
        // Eager load relationships
        $order->load(['items.product', 'items.variant', 'country']);

        return view('admin.invoice', compact('order'));
    }

    /**
     * Export invoice as PDF
     */
    public function exportPdf(Order $order)
    {
        // Eager load relationships
        $order->load(['items.product', 'items.variant', 'country']);

        // Get currency info
        $currencyService = app(\App\Services\CountryCurrencyService::class);
        $currencyInfo = $currencyService->getCurrentCurrencyInfo();

        // Render the view to HTML
        $html = view('admin.invoice-pdf', [
            'order' => $order,
            'displayCurrency' => $order->currency ?? 'USD',
            'displaySymbol' => $currencyInfo['currency_symbol'],
        ])->render();

        // Configure DomPDF options
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        // Generate PDF
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'invoice-' . $order->order_number . '.pdf';

        return response()->streamDownload(function () use ($dompdf) {
            echo $dompdf->output();
        }, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}

