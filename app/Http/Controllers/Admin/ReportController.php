<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderRental;
use App\Models\OrderSale;
use App\Models\Payment;
use App\Models\Rating;
use App\Models\User;
use App\Models\Contact;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class ReportController extends Controller
{
    public function index()
    {
        return inertia('Admin/Reports/Index');
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:users_summary,users_details,financial_summary,financial_details,Contacts_summary,Contacts_details,ratings_summary,ratings_details',
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
        $fileName = str_replace(['.pdf_', '_'], ['', ''], $fileName);
        $fileName = preg_replace('/\.pdf.*$/', '.pdf', $fileName);

        $pdf = PDF::loadView('exports.admin-report', [
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
            case 'users_summary':
                $activeUsers = User::where('is_active', true)->count();
                $inactiveUsers = User::where('is_active', false)->count();
                $newUsers = User::whereBetween('created_at', [$startDate, $endDate])->count();

                $headers = ['Metric', 'Count'];
                $data = [
                    ['Active Users', $activeUsers],
                    ['Inactive Users', $inactiveUsers],
                    ['New Users (Period)', $newUsers]
                ];
                break;

            case 'users_details':
                $query = User::whereBetween('created_at', [$startDate, $endDate]);

                $headers = ['ID', 'Name', 'Email', 'Phone', 'Status', 'User Type', 'Last Login', 'Created At'];
                $data = $query->get()
                    ->map(fn($user) => [
                        $user->id,
                        $user->name,
                        $user->email,
                        $user->phone,
                        $user->is_active ? 'Active' : 'Inactive',
                        $user->type,
                        $user->last_login_at?->format('Y-m-d H:i') ?? 'Never',
                        $user->created_at->format('Y-m-d')
                    ])->toArray();
                break;

            // case 'financial_summary':
            //     $rentalOrders = OrderRental::whereBetween('created_at', [$startDate, $endDate])->count();
            //     $saleOrders = OrderSale::whereBetween('created_at', [$startDate, $endDate])->count();
            //     $totalRevenue = Payment::whereBetween('created_at', [$startDate, $endDate])
            //         ->where('status', 'completed')
            //         ->sum('amount');
            //     $adminProfit = $totalRevenue * 0.2; // Assuming 20% admin commission
            //     $vendorProfit = $totalRevenue * 0.8; // Assuming 80% vendor profit

            //     $headers = ['Metric', 'Count/Amount'];
            //     $data = [
            //         ['Rental Orders', $rentalOrders],
            //         ['Sale Orders', $saleOrders],
            //         ['Total Revenue', $totalRevenue],
            //         ['Admin Profit', $adminProfit],
            //         ['Vendor Profit', $vendorProfit]
            //     ];
            //     break;

            // case 'financial_details':
            //     $query = Payment::whereBetween('created_at', [$startDate, $endDate])
            //         ->where('status', 'completed')
            //         ->with(['user', 'payable']);

            //     $headers = ['ID', 'User', 'Amount', 'Order Type', 'Gold Piece', 'Vendor', 'Order Date'];
            //     $data = $query->get()
            //         ->map(function ($payment) {
            //             $order = $payment->payable;
            //             return [
            //                 $payment->id,
            //                 $payment->user?->name ?? 'N/A',
            //                 $payment->amount,
            //                 class_basename($payment->payable_type),
            //                 $order->goldPiece?->name ?? 'N/A',
            //                 $order->branch?->vendor?->name ?? 'N/A',
            //                 $payment->created_at->format('Y-m-d')
            //             ];
            //         })->toArray();
            //     break;

            case 'Contacts_summary':
                $openContacts = Contact::where('status', 'new')->count();
                $inProgressContacts = Contact::where('status', 'in_progress')->count();
                $closedContacts = Contact::where('status', 'resolved')->count();

                $headers = ['Status', 'Count'];
                $data = [
                    ['Open Contacts', $openContacts],
                    ['In Progress Contacts', $inProgressContacts],
                    ['Closed Contacts', $closedContacts]
                ];
                break;

            case 'Contacts_details':
                $query = Contact::whereBetween('created_at', [$startDate, $endDate])
                    ->with(['user', 'vendor']);

                $headers = ['ID', 'User', 'Vendor', 'Subject', 'Status', 'Created At', 'Updated At'];
                $data = $query->get()
                    ->map(fn($Contact) => [
                        $Contact->id,
                        $Contact->user?->name ?? 'N/A',
                        $Contact->vendor?->name ?? 'N/A',
                        $Contact->subject,
                        ucfirst(str_replace('_', ' ', $Contact->status)),
                        $Contact->created_at->format('Y-m-d'),
                        $Contact->updated_at->format('Y-m-d')
                    ])->toArray();
                break;

            case 'ratings_summary':
                try {
                    $baseQuery = Rating::whereBetween('created_at', [$startDate, $endDate]);

                    $averageRating = $baseQuery->avg('rating');
                    $ratingCount = $baseQuery->count();

                    $query = Rating::with(['goldPiece.branch.vendor'])
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->selectRaw('user_id, AVG(rating) as avg_rating, COUNT(*) as rating_count')
                        ->groupBy('user_id');

                    $headers = ['Vendor', 'Average Rating', 'Rating Count'];

                    $data = $query->get()
                        ->map(function ($rating) {
                            try {
                                $vendorName = optional(optional(optional($rating->goldPiece)->branch)->vendor)->name ?? 'N/A';
                                return [
                                    $vendorName,
                                    number_format($rating->avg_rating, 2),
                                    $rating->rating_count
                                ];
                            } catch (\Exception $e) {
                                \Log::warning('Error processing rating item', [
                                    'rating_id' => $rating->id ?? null,
                                    'error' => $e->getMessage()
                                ]);
                                return [
                                    'Error loading vendor',
                                    number_format($rating->avg_rating, 2),
                                    $rating->rating_count
                                ];
                            }
                        })->toArray();

                    array_unshift($data, ['Overall Average Rating', number_format($averageRating, 2), $ratingCount]);
                } catch (\Exception $e) {
                    \Log::error('Ratings summary generation failed', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e; // Re-throw to be caught by the outer try-catch
                }
                break;

            case 'ratings_details':
                $query = Rating::whereBetween('created_at', [$startDate, $endDate])
                    ->with(['user', 'goldPiece.branch.vendor']);

                $headers = ['ID', 'User', 'Rating', 'Comment', 'Gold Piece', 'Vendor', 'Date'];
                $data = $query->get()
                    ->map(fn($rating) => [
                        $rating->id,
                        $rating->user?->name ?? 'Anonymous',
                        $rating->rating,
                        $rating->comment,
                        $rating->goldPiece?->name ?? 'N/A',
                        $rating->goldPiece?->branch?->vendor?->name ?? 'N/A',
                        $rating->created_at->format('Y-m-d H:i')
                    ])->toArray();
                break;
        }

        array_unshift($data, $headers);
        return $data;
    }
}
