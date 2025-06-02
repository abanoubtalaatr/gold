<?php

namespace App\Http\Controllers;

use App\Services\GoldPriceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\OrderSale;
use App\Models\OrderRental;

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