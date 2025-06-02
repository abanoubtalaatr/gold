<?php

namespace App\Http\Controllers;

use App\Services\GoldPriceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\OrderSale;
use App\Models\OrderRental;
use App\Models\Contact;
use App\Notifications\Admin\NewContactMessageAdmin;
use Illuminate\Support\Facades\Validator;

class LandingController extends Controller
{
    protected $goldPriceService;

    public function __construct(GoldPriceService $goldPriceService)
    {
        $this->goldPriceService = $goldPriceService;
    }

    /**
     * Display the landing page with dynamic gold trading data
     */
    public function index()
    {
        
        try {
            // Get current gold prices for display
            $goldPrices = $this->goldPriceService->getMobileFormattedPrices();
            
            // Get platform statistics
            $stats = $this->getPlatformStats();
            
            // Get featured services
            $features = $this->getFeaturedServices();
            
            // Get current locale information
            $currentLocale = app()->getLocale();
            $isRtl = $currentLocale === 'ar';
            
            return view('landing', compact('goldPrices', 'stats', 'features', 'currentLocale', 'isRtl'));
        } catch (\Exception $e) {
            // Fallback to static data if gold API is not available
            $goldPrices = $this->getFallbackPrices();
            $stats = $this->getPlatformStats();
            $features = $this->getFeaturedServices();
            
            // Get current locale information
            $currentLocale = app()->getLocale();
            $isRtl = $currentLocale === 'ar';
            
            return view('landing', compact('goldPrices', 'stats', 'features', 'currentLocale', 'isRtl'));
        }
    }

    /**
     * Get platform statistics
     */
    private function getPlatformStats(): array
    {
        return Cache::remember('landing_stats', now()->addHours(1), function () {
            $activeSaleOrders = OrderSale::where('status', '!=', 'completed')->count();
            $activeRentalOrders = OrderRental::where('status', '!=', 'completed')->count();
            
            return [
                'total_vendors' => User::role('vendor')->count(),
                'total_customers' =>12,
                'active_orders' => $activeSaleOrders + $activeRentalOrders,
                'supported_carats' => ['24K', '22K', '21K', '20K', '18K', '16K', '14K', '10K'],
            ];
        });
    }

    public function show($slug)
    {
        return view($slug);
    }

    /**
     * Handle contact form submission
     */
    public function contact(Request $request)
    {
        // Validate the form data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please check your input data.',
                'errors' => $validator->errors()
            ], 422);
        }

        // try {
            // Create contact entry
            $contact = Contact::create([
                
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'phone' => null, // Not required for landing page form
                'read' => false,
            ]);

            // Notify admins about new contact message
            // $admins = User::whereHas('roles', function ($query) {
            //     $query->where('name', 'admin')
            //         ->orWhere('name', 'superadmin');
            // })->get();

            // foreach ($admins as $admin) {
            //     $admin->notify(new NewContactMessageAdmin($contact));
            // }

            return response()->json([
                'success' => true,
                'message' => __('landing.contact.success_message', [], app()->getLocale())
            ]);

        // } catch (\Exception $e) {
        //     Log::error('Contact form error: ' . $e->getMessage());
            
        //     return response()->json([
        //         'success' => false,
        //         'message' => __('landing.contact.error_message', [], app()->getLocale())
        //     ], 500);
        // }
    }

    /**
     * Get featured services
     */
    private function getFeaturedServices(): array
    {
        return [
            [
                'title' => __('Gold Trading'),
                'description' => __('Buy and sell gold with real-time pricing and transparent transactions'),
                'icon' => 'gold',
                'features' => [
                    __('Real-time gold prices'),
                    __('Multiple carat options'),
                    __('Secure transactions'),
                    __('Transparent pricing')
                ]
            ],
            [
                'title' => __('Gold Rental'),
                'description' => __('Rent gold jewelry for special occasions with flexible terms'),
                'icon' => 'rental',
                'features' => [
                    __('Short-term rentals'),
                    __('Variety of pieces'),
                    __('Competitive rates'),
                    __('Insured items')
                ]
            ],
            [
                'title' => __('Vendor Network'),
                'description' => __('Connect with trusted gold vendors across the region'),
                'icon' => 'network',
                'features' => [
                    __('Verified vendors'),
                    __('Multiple locations'),
                    __('Quality assurance'),
                    __('Customer reviews')
                ]
            ]
        ];
    }

    /**
     * Get fallback prices when API is not available
     */
    private function getFallbackPrices(): array
    {
        return [
            'banner_info' => [
                'main_carat' => '24',
                'buy_price' => 0,
                'sell_price' => 0,
                'date' => now()->format('j F'),
                'currency' => 'SAR',
            ],
            'price_list' => [],
            'timestamp' => now()->timestamp,
            'api_available' => false
        ];
    }
} 