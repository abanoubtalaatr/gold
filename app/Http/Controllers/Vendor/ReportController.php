<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\OrderRental;
use App\Models\OrderSale;
use App\Models\Payment;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index()
    {
        // Get today's sale orders with gold piece details
        $todaysOrders = $this->getTodaysSaleOrders();

        return inertia('Vendor/Reports/Index', [
            'todaysOrders' => $todaysOrders
        ]);
    }

    /**
     * Filter orders based on date range and type
     */
    public function filter(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:rentals,sales',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $orders = $this->getFilteredOrders(
            $validated['type'],
            $validated['start_date'],
            $validated['end_date']
        );

        return response()->json([
            'orders' => $orders,
            'success' => true
        ]);
    }

    /**
     * Get filtered orders based on type and date range
     */
    private function getFilteredOrders($type, $startDate, $endDate)
    {
        $vendorId = Auth::id();

        if ($type === 'sales') {
            return OrderSale::with(['goldPiece', 'user', 'branch'])
                ->whereHas('branch', function ($q) use ($vendorId) {
                    $q->where('vendor_id', $vendorId);
                })
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'customer_name' => $order->user->name ?? 'N/A',
                        'gold_piece_name' => $order->goldPiece->name ?? 'N/A',
                        'weight' => $order->goldPiece->weight ?? 'N/A',
                        'carat' => $order->goldPiece->carat ?? 'N/A',
                        'total_price' => $order->total_price,
                        'status' => $order->status,
                        'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                        'branch' => $order->branch->name ?? 'N/A',
                        'including_lobes' => $order->goldPiece->including_lobes ?? false,
                        'description' => $order->goldPiece->description ?? 'N/A',
                    ];
                });
        } else {
            return OrderRental::with(['goldPiece', 'user', 'branch'])
                ->whereHas('branch', function ($q) use ($vendorId) {
                    $q->where('vendor_id', $vendorId);
                })
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'customer_name' => $order->user->name ?? 'N/A',
                        'gold_piece_name' => $order->goldPiece->name ?? 'N/A',
                        'weight' => $order->goldPiece->weight ?? 'N/A',
                        'carat' => $order->goldPiece->carat ?? 'N/A',
                        'start_date' => $order->start_date,
                        'end_date' => $order->end_date,
                        'total_price' => $order->total_price,
                        'status' => $order->status,
                        'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                        'branch' => $order->branch->name ?? 'N/A',
                        'including_lobes' => $order->goldPiece->including_lobes ?? false,
                        'description' => $order->goldPiece->description ?? 'N/A',
                    ];
                });
        }
    }

    /**
     * Get today's sale orders with gold piece details
     */
    protected function getTodaysSaleOrders()
    {
        $today = Carbon::today();

        return OrderSale::whereDate('created_at', $today)
            ->whereHas('branch', function ($q) {
                $q->where('vendor_id', Auth::id());
            })
            ->with(['user', 'goldPiece', 'branch'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'customer_name' => $order->user->name ?? 'N/A',
                    'gold_piece_name' => $order->goldPiece->name ?? 'N/A',
                    'weight' => $order->goldPiece->weight ?? 'N/A',
                    'carat' => $order->goldPiece->carat ?? 'N/A',
                    'total_price' => $order->total_price,
                    'status' => $order->status,
                    'created_at' => $order->created_at->format('Y-m-d H:i'),
                    'branch_name' => $order->branch->name ?? 'N/A',
                    'is_including_lobes' => $order->is_including_lobes ? 'Yes' : 'No',
                    'description' => $order->goldPiece->description ?? 'N/A',
                ];
            });
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:rentals,sales',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:excel,pdf',
        ]);

        $data = $this->getReportData(
            $validated['type'],
            $validated['start_date'],
            $validated['end_date']
        );

        if ($validated['format'] === 'excel') {
            return $this->generateExcel($data, $validated);
        } else {
            // Redirect to a new route for the Blade view
            return redirect()->route('vendor.reports.view', $validated);
        }
    }

    private function generateView($data, $params)
    {
        if (empty($data) || !is_array($data)) {
            $data = [
                ['No Data Available'],
                ['No records found for the selected period']
            ];
        }

        $vendor = Auth::user() ?? (object) ['name' => 'Unknown Vendor'];

        $viewData = [
            'data' => $data,
            'type' => $params['type'],
            'start_date' => $params['start_date'],
            'end_date' => $params['end_date'],
            'vendor' => $vendor,
            'generated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ];

        return view('exports.vendor-report', $viewData);
    }

    public function viewReport(Request $request)
    {
        $params = $request->only(['type', 'start_date', 'end_date']);

        $validated = validator($params, [
            'type' => 'required|in:rentals,sales',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ])->validate();

        $data = $this->getReportData(
            $validated['type'],
            $validated['start_date'],
            $validated['end_date']
        );

        return $this->generateView($data, $validated);
    }

    private function generateExcel($data, $params)
    {
        try {
            $filename = $params['type'] . '_report_' . $params['start_date'] . '_to_' . $params['end_date'] . '.xlsx';

            return Excel::download(
                new class($data) implements \Maatwebsite\Excel\Concerns\FromArray {
                    public function __construct(public array $data) {}
                    public function array(): array
                    {
                        return $this->data;
                    }
                },
                $filename,
                \Maatwebsite\Excel\Excel::XLSX,
                [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"'
                ]
            );
        } catch (\Exception $e) {
            Log::error('Excel generation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate Excel: ' . $e->getMessage()], 500);
        }
    }

    protected function getReportData(string $type, string $startDate, string $endDate)
    {
        $data = [];
        $headers = [];

        switch ($type) {
            case 'rentals':
                $query = OrderRental::whereBetween('created_at', [$startDate, $endDate])
                    ->whereHas('branch', fn($q) => $q->where('vendor_id', Auth::id()));

                $headers = [
                    __('ID'),
                    __('Customer'),
                    __('Piece'),
                    __('Branch'),
                    __('Start Date'),
                    __('End Date'),
                    __('Total Price'),
                    __('Status')
                ];
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
                $query = OrderSale::whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->whereHas('branch', function ($q) {
                        $q->where('vendor_id', Auth::user()->vendor_id ?? Auth::id());
                    });

                $headers = [
                    __('ID'),
                    __('Customer'),
                    __('Gold Piece Name'),
                    __('Weight (g)'),
                    __('Carat'),
                    __('Total Price'),
                    __('Status'),
                    __('Created At'),
                    __('Branch'),
                    __('Description')
                ];
                
                $data = $query->with(['user', 'goldPiece', 'branch'])
                    ->get()
                    ->map(function ($order) {
                        return [
                            $order->id,
                            $order->user->name ?? 'N/A',
                            $order->goldPiece->name ?? 'N/A',
                            $order->goldPiece->weight ?? 'N/A',
                            $order->goldPiece->carat ?? 'N/A',
                            $order->total_price,
                            $order->status,
                            $order->created_at->format('Y-m-d H:i'),
                            $order->branch->name ?? 'N/A',
                            $order->goldPiece->description ?? 'N/A'
                        ];
                    })->toArray();
                break;

            case 'payments':
                // Keeping the payments section commented out as it was
                break;

            case 'ratings':
                $query = Rating::whereBetween('created_at', [$startDate, $endDate])
                    ->whereHas('goldPiece', function ($q) {
                        $q->whereHas('branch', function ($q) {
                            $q->where('vendor_id', Auth::id());
                        });
                    });

                $headers = [
                    __('ID'),
                    __('Customer'),
                    __('Rating (1-5)'),
                    __('Comment'),
                    __('Gold Piece'),
                    __('Branch'),
                    __('Rating Date')
                ];

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
