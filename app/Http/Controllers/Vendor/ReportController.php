<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\OrderRental;
use App\Models\OrderSale;
use App\Models\Payment;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index()
    {
        return inertia('Vendor/Reports/Index');
    }
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:rentals,sales,payments,ratings',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:excel,pdf'
        ]);
        $data = $this->getReportData(
            $validated['type'],
            $validated['start_date'],
            $validated['end_date']
        );

        $fileName = $this->generateFileName(
            $validated['type'],
            $validated['start_date'],
            $validated['end_date'],
            $validated['format']
        );

        if ($validated['format'] === 'excel') {
            return $this->generateExcelResponse($data, $fileName);
        }

        return $this->generatePdfResponse($data, $validated, $fileName);
    }

    protected function generateFileName($type, $startDate, $endDate, $format): string
    {
        $extension = $format === 'excel' ? 'xlsx' : 'pdf';
        return sprintf(
            '%s_report_%s_to_%s.%s',
            $type,
            $startDate,
            $endDate,
            $extension
        );
    }

    protected function generateExcelResponse(array $data, string $fileName)
    {
        // Ensure the filename has .xlsx extension
        $fileName = str_replace('.excel', '.xlsx', $fileName);

        return Excel::download(
            new class ($data) implements \Maatwebsite\Excel\Concerns\FromArray {
            public function __construct(public array $data)
            {
            }
            public function array(): array
            {
                return $this->data;
            }
            },
            $fileName,
            \Maatwebsite\Excel\Excel::XLSX,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
            ]
        );
    }

    protected function generatePdfResponse(array $data, array $requestData, string $fileName)
    {
        // Ensure the filename has .pdf extension without underscore
        $fileName = str_replace(['.pdf_', '_'], ['', ''], $fileName);
        $fileName = preg_replace('/\.pdf.*$/', '.pdf', $fileName);

        $pdf = PDF::loadView('exports.vendor-report', [
            'data' => $data,
            'report' => (object) [
                'type' => $requestData['type'],
                'start_date' => $requestData['start_date'],
                'end_date' => $requestData['end_date']
            ]
        ]);

        return $pdf->download($fileName)
            ->headers([
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
            ]);
    }

    protected function getReportData(string $type, string $startDate, string $endDate)
    {
        $data = [];
        $headers = [];

        switch ($type) {
            case 'rentals':
                $query = OrderRental::whereBetween('created_at', [$startDate, $endDate])
                    ->whereHas('branch', fn($q) => $q->where('vendor_id', auth()->id()));

                $headers = ['ID', 'Customer', 'Piece', 'Branch', 'Start Date', 'End Date', 'Total Price', 'Status'];
                $data = $query->with(['user', 'goldPiece', 'branch'])
                    ->get()
                    ->map(fn($order) => [
                        $order->id,
                        $order->user->name,
                        $order->goldPiece->name,
                        $order->branch->name,
                        $order->start_date,
                        $order->end_date,
                        $order->total_price,
                        $order->status
                    ])->toArray();
                break;

            case 'sales':
                $query = OrderSale::whereBetween('created_at', [$startDate, $endDate])
                    ->whereHas('branch', fn($q) => $q->where('vendor_id', auth()->id()));

                $headers = ['ID', 'Customer', 'Piece', 'Branch', 'Order Date', 'Total Price', 'Status'];
                $data = $query->with(['user', 'goldPiece', 'branch'])
                    ->get()
                    ->map(fn($order) => [
                        $order->id,
                        $order->user->name,
                        $order->goldPiece->name,
                        $order->branch->name,
                        $order->created_at->format('Y-m-d'),
                        $order->total_price,
                        $order->status
                    ])->toArray();
                break;

            case 'payments':
                // $query = Payment::whereBetween('created_at', [$startDate, $endDate])
                //     ->whereHasMorph(
                //         'payable',
                //         [OrderRental::class, OrderSale::class],
                //         fn($q) => $q->whereHas('branch', fn($q) => $q->where('vendor_id', auth()->id()))
                //     );

                // $headers = ['ID', 'Transaction ID', 'Amount', 'Method', 'Status', 'Customer', 'Order Type', 'Order ID', 'Date'];
                // $data = $query->with(['user', 'payable'])
                //     ->get()
                //     ->map(fn($payment) => [
                //         $payment->id,
                //         $payment->transaction_id,
                //         $payment->amount,
                //         $payment->payment_method,
                //         $payment->status,
                //         $payment->user?->name ?? 'N/A',
                //         class_basename($payment->payable_type),
                //         $payment->payable_id,
                //         $payment->created_at->format('Y-m-d H:i')
                //     ])->toArray();
                break;

            case 'ratings':
                $query = Rating::whereBetween('created_at', [$startDate, $endDate])
                    ->whereHas('goldPiece', function ($q) {
                        $q->whereHas('branch', function ($q) {
                            $q->where('vendor_id', auth()->id());
                        });
                    });

                $headers = [
                    'ID',
                    'Customer',
                    'Rating (1-5)',
                    'Comment',
                    'Gold Piece',
                    'Branch',
                    'Rating Date'
                ];
                Log::info($query->with(['user', 'goldPiece.branch'])
                    ->get());
                $data = $query->with(['user', 'goldPiece.branch'])
                    ->get()
                    ->map(function ($rating) {
                        return [
                            $rating->id,
                            $rating->user?->name ?? 'Anonymous',
                            $rating->rating,
                            $rating->comment,
                            $rating->goldPiece?->name ?? 'N/A',
                            $rating->goldPiece?->branch?->name ?? 'N/A',
                            $rating->created_at->format('Y-m-d H:i')
                        ];
                    })->toArray();
                break;
        }

        array_unshift($data, $headers);
        return $data;
    }
}