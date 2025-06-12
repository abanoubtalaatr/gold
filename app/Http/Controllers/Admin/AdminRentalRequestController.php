<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Branch;
use App\Models\Wallet;
use App\Models\OrderRental;
use Illuminate\Http\Request;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\RentalWorkflowService;
use App\Events\OrderRentalStatusChangeEvent;
use App\Notifications\Client\ChangeOrderStatusNotification;

class AdminRentalRequestController extends Controller
{
    protected $rentalWorkflowService;

    public function __construct(RentalWorkflowService $rentalWorkflowService)
    {
        $this->rentalWorkflowService = $rentalWorkflowService;
    }

    public function index(Request $request)
    {
        $branches = Branch::select('id', 'name')->get();
        $statuses = [
            OrderRental::STATUS_PENDING_APPROVAL,
            OrderRental::STATUS_APPROVED,
            OrderRental::STATUS_PIECE_SENT,
            OrderRental::STATUS_RENTED,
            OrderRental::STATUS_REJECTED
        ];

        $filters = $request->only([
            'search',
            'branch_id',
            'service_type',
            'piece_name',
            'description',
            'price_min',
            'price_max',
            'date_filter',
            'date_from',
            'date_to',
            'status',
            'rental_status',
        ]);

        $ordersQuery = OrderRental::query()
            ->where('type', OrderRental::LEASE_TYPE)
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('goldPiece', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->when($filters['branch_id'] ?? null, function ($query, $branchId) {
                $query->where('branch_id', $branchId);
            })
            ->when($filters['service_type'] ?? null, function ($query, $serviceType) {
                $query->whereHas('goldPiece', function ($q) use ($serviceType) {
                    $q->where('type', $serviceType);
                });
            })
            ->when($filters['piece_name'] ?? null, function ($query, $pieceName) {
                $query->whereHas('goldPiece', function ($q) use ($pieceName) {
                    $q->where('name', 'like', "%{$pieceName}%");
                });
            })
            ->when($filters['description'] ?? null, function ($query, $description) {
                $query->whereHas('goldPiece', function ($q) use ($description) {
                    $q->where('description', 'like', "%{$description}%");
                });
            })
            ->when(isset($filters['price_min']) || isset($filters['price_max']), function ($query) use ($filters) {
                $query->whereBetween('total_price', [
                    $filters['price_min'] ?? 0,
                    $filters['price_max'] ?? PHP_INT_MAX,
                ]);
            })
            ->when($filters['date_filter'] ?? null, function ($query, $dateFilter) use ($filters) {
                if ($dateFilter === 'today') {
                    $query->whereDate('created_at', Carbon::today());
                } elseif ($dateFilter === 'week') {
                    $query->whereBetween('created_at', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek(),
                    ]);
                } elseif ($dateFilter === 'custom' && $filters['date_from'] && $filters['date_to']) {
                    $query->whereBetween('created_at', [
                        Carbon::parse($filters['date_from']),
                        Carbon::parse($filters['date_to']),
                    ]);
                }
            })
            ->when(isset($filters['status']) && in_array($filters['status'], OrderRental::statuses()), function ($query) use ($filters) {
                $query->where('status', $filters['status']);
                if ($filters['status'] === OrderRental::STATUS_RENTED) {
                    $query->where('start_date', '<=', Carbon::now())
                        ->where('end_date', '>=', Carbon::now());
                } elseif ($filters['status'] === OrderRental::STATUS_APPROVED) {
                    $query->where('start_date', '>', Carbon::now());
                } elseif ($filters['status'] === OrderRental::STATUS_AVAILABLE) {
                    $query->where('end_date', '<', Carbon::now());
                }
            })
            ->when($filters['rental_status'] ?? null, function ($query, $rentalStatus) {
                switch ($rentalStatus) {
                    case 'current':
                        $query->where('start_date', '<=', Carbon::now())
                            ->where('end_date', '>=', Carbon::now());
                        break;
                    case 'finished':
                        $query->where('end_date', '<', Carbon::now());
                        break;
                    case 'future':
                        $query->where('start_date', '>', Carbon::now());
                        break;
                }
            })
            ->with([
                'user',
                'goldPiece',
                'branch'
            ])
            ->select([
                'id',
                'user_id',
                'gold_piece_id',
                'branch_id',
                'total_price',
                'status',
                'created_at',
                'updated_at',
                'start_date',
                'end_date',
            ]);

        $orders = $ordersQuery->orderBy('start_date', 'desc')->paginate(10)->appends($filters);

        $orders->getCollection()->transform(function ($order) {
            $order->rental_days = $order->start_date && $order->end_date
                ? Carbon::parse($order->end_date)->diffInDays(Carbon::parse($order->start_date))
                : null;
            $order->invoice = $order->status === 'payment_confirmed' ? $this->generateInvoice($order) : null;
            $order->allowed_actions = $this->getAllowedActions($order);

            return $order;
        });

        return Inertia::render('Admin/RentalRequests/Index', [
            'orders' => $orders,
            'branches' => $branches,
            'filters' => $filters,
            'statuses' => $statuses,
        ]);
    }

    protected function generateInvoice($order)
    {
        return [
            'order_id' => $order->id,
            'user_name' => $order->user->name,
            'piece_name' => $order->goldPiece->name,
            'service_type' => $order->goldPiece->type,
            'rental_days' => $order->start_date && $order->end_date
                ? Carbon::parse($order->end_date)->diffInDays(Carbon::parse($order->start_date))
                : null,
            'total_price' => $order->total_price,
            'start_date' => $order->start_date,
            'end_date' => $order->end_date,
            'branch_name' => $order->branch->name,
            'created_at' => $order->created_at->toDateTimeString(),
            'status' => 'payment_confirmed',
        ];
    }

    protected function getAllowedActions($order)
    {
        $allowedTransitions = $this->rentalWorkflowService->getAllowedTransitions($order->status);
        $actions = [];

        foreach ($allowedTransitions as $transition) {
            switch ($transition) {
                case OrderRental::STATUS_APPROVED:
                    if ($order->status === OrderRental::STATUS_PENDING_APPROVAL) {
                        $actions[] = 'approve';
                    }
                    break;
                case OrderRental::STATUS_REJECTED:
                    if ($order->status === OrderRental::STATUS_PENDING_APPROVAL) {
                        $actions[] = 'reject';
                    }
                    break;
                case OrderRental::STATUS_PIECE_SENT:
                    if ($order->status === OrderRental::STATUS_APPROVED) {
                        $actions[] = 'mark_as_sent';
                    }
                    break;
                case OrderRental::STATUS_RENTED:
                    if ($order->status === OrderRental::STATUS_PIECE_SENT) {
                        $actions[] = 'confirm_rental';
                    }
                    break;
            }
        }

        return $actions;
    }

    public function accept(Request $request, $order)
    {
        $order = OrderRental::with(['goldPiece', 'goldPiece.user', 'user', 'branch'])->findOrFail($order);

        $order->status = OrderRental::STATUS_APPROVED;
        $order->save();

        event(new OrderRentalStatusChangeEvent($order, $request->user()));

        $order->user->notify(new ChangeOrderStatusNotification($order, OrderRental::STATUS_APPROVED));

        return back()->with('success', __('Order accepted successfully'));
    }

    public function reject(Request $request, $order)
    {
        $order = OrderRental::with(['goldPiece', 'goldPiece.user', 'user', 'branch'])->findOrFail($order);

        event(new OrderRentalStatusChangeEvent($order, $request->user()));

        $order->user->notify(new ChangeOrderStatusNotification($order, OrderRental::STATUS_REJECTED));

        $order->update(['status' => OrderRental::STATUS_REJECTED]);

        return back()->with('success', __('Order rejected successfully'));
    }

    public function markAsSent(Request $request, $order)
    {
        $order = OrderRental::with(['goldPiece', 'goldPiece.user', 'user', 'branch'])->findOrFail($order);

        if ($order->status !== OrderRental::STATUS_APPROVED) {
            return back()->with('error', __('Order must be approved before marking as sent.'));
        }

        $order->status = OrderRental::STATUS_PIECE_SENT;
        $order->save();

        event(new OrderRentalStatusChangeEvent($order, $request->user()));
        $order->user->notify(new ChangeOrderStatusNotification($order, OrderRental::STATUS_PIECE_SENT));

        return back()->with('success', __('Order marked as sent successfully'));
    }

    public function confirmRental(Request $request, $order)
    {
        $order = OrderRental::with(['goldPiece', 'goldPiece.user', 'user', 'branch'])->findOrFail($order);

        if ($order->status !== OrderRental::STATUS_PIECE_SENT) {
            return back()->with('error', __('Order must be marked as sent before confirming rental.'));
        }
        $totalPrice = $order->total_price;
        $settings = SystemSetting::first();
        $merchantCommission = $settings->merchant_commission_percentage ?? 0;
        $platformCommission = $settings->platform_commission_percentage ?? 0;

        $vendorAmount = ($totalPrice * $merchantCommission) / 100;
        $platformAmount = ($totalPrice * $platformCommission) / 100;

        $vendorWallet = Wallet::where('user_id', $order->user_id)->first();

        if (!$vendorWallet) {
            $vendorWallet = Wallet::create([
                'user_id' => $order->user_id,
            ]);
        }

        $vendorWallet->credit($vendorAmount, 'Vendor commission for order #' . $order->id);
        $vendorWallet->update(['balance' => $vendorWallet->balance + $vendorAmount, 'pending_balance' => $vendorWallet->pending_balance + $vendorAmount]);

        $platformWallet = Wallet::where('user_id', User::where('email', 'admin@admin.com')->first()->id)->first();

        $platformWallet->credit($platformAmount, 'Platform commission for order #' . $order->id);
        $platformWallet->update(['balance' => $platformWallet->balance + $platformAmount, 'total_earned' => $platformWallet->total_earned + $platformAmount]);

        $order->status = OrderRental::STATUS_RENTED;
        $order->save();

        event(new OrderRentalStatusChangeEvent($order, $request->user()));

        $order->user->notify(new ChangeOrderStatusNotification($order, OrderRental::STATUS_RENTED));

        return back()->with('success', __('Rental confirmed successfully'));
    }
}
