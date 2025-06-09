<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\GoldPiece;
use App\Models\OrderSale;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\Wallet;
use App\Services\SalesWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SalesWorkflowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $vendor;
    protected $customer;
    protected $goldPiece;
    protected $branch;
    protected $order;
    protected $salesWorkflowService;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles only if they don't exist
        if (!Role::where('name', 'vendor')->where('guard_name', 'web')->exists()) {
            Role::create(['name' => 'vendor', 'guard_name' => 'web']);
        }
        if (!Role::where('name', 'customer')->where('guard_name', 'web')->exists()) {
            Role::create(['name' => 'customer', 'guard_name' => 'web']);
        }
        if (!Role::where('name', 'admin')->where('guard_name', 'web')->exists()) {
            Role::create(['name' => 'admin', 'guard_name' => 'web']);
        }

        // Create system settings with commission percentages
        SystemSetting::create([
            'platform_commission_percentage' => 10.00,
            'merchant_commission_percentage' => 15.00,
            'tax_percentage' => 15.00,
            'gold_purchase_price' => 200.00,
            'minimum_payout_amount' => 100.00,
        ]);

        // Create vendor user
        $this->vendor = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $this->vendor->assignRole('vendor');

        // Create customer user
        $this->customer = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $this->customer->assignRole('customer');

        // Create branch for vendor
        $this->branch = Branch::factory()->create([
            'vendor_id' => $this->vendor->id,
            'is_active' => true,
        ]);

        // Create gold piece
        $this->goldPiece = GoldPiece::factory()->create([
            'user_id' => $this->customer->id,
            'type' => 'for_sale',
            'status' => 'available',
        ]);

        // Create sale order
        $this->order = OrderSale::create([
            'user_id' => $this->customer->id,
            'gold_piece_id' => $this->goldPiece->id,
            'total_price' => 1000.00,
            'status' => OrderSale::STATUS_PENDING_APPROVAL,
        ]);

        $this->salesWorkflowService = app(SalesWorkflowService::class);
    }

    /** @test */
    public function it_can_approve_a_sale_order()
    {
        $result = $this->salesWorkflowService->approve($this->order, $this->branch->id, $this->vendor);

        $this->assertTrue($result);
        $this->assertEquals(OrderSale::STATUS_APPROVED, $this->order->fresh()->status);
        $this->assertEquals($this->branch->id, $this->order->fresh()->branch_id);
    }

    /** @test */
    public function it_can_reject_a_sale_order()
    {
        $result = $this->salesWorkflowService->reject($this->order, $this->vendor);

        $this->assertTrue($result);
        $this->assertEquals(OrderSale::STATUS_REJECTED, $this->order->fresh()->status);
    }

    /** @test */
    public function it_can_mark_order_as_sent()
    {
        // First approve the order
        $this->order->update(['status' => OrderSale::STATUS_APPROVED, 'branch_id' => $this->branch->id]);

        $result = $this->salesWorkflowService->markAsSent($this->order, $this->vendor);

        $this->assertTrue($result);
        $this->assertEquals(OrderSale::STATUS_PIECE_SENT, $this->order->fresh()->status);
    }

    /** @test */
    public function it_can_mark_order_as_sold_with_wallet_transactions()
    {
        // Create an admin user for platform commission
        $admin = User::factory()->create(['email_verified_at' => now()]);
        $admin->assignRole('admin');

        // Set up the order in the correct state
        $this->order->update([
            'status' => OrderSale::STATUS_PIECE_SENT, 
            'branch_id' => $this->branch->id
        ]);

        // Ensure vendor has a wallet
        $vendorWallet = Wallet::factory()->create(['user_id' => $this->vendor->id]);
        $adminWallet = Wallet::factory()->create(['user_id' => $admin->id]);

        $initialVendorBalance = $vendorWallet->balance;
        $initialAdminBalance = $adminWallet->balance;

        $result = $this->salesWorkflowService->markAsSold($this->order, $this->vendor);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('commission_amount', $result);
        $this->assertArrayHasKey('vendor_commission', $result);
        $this->assertArrayHasKey('platform_commission', $result);
        $this->assertEquals(OrderSale::STATUS_SOLD, $this->order->fresh()->status);

        // Check vendor wallet transaction
        $vendorWallet->refresh();
        $expectedVendorCommission = ($this->order->total_price * 15) / 100; // 15% merchant commission
        $this->assertEquals($initialVendorBalance + $expectedVendorCommission, $vendorWallet->balance);
        $this->assertEquals($expectedVendorCommission, $result['vendor_commission']);

        // Check admin wallet transaction
        $adminWallet->refresh();
        $expectedPlatformCommission = ($this->order->total_price * 10) / 100; // 10% platform commission
        $this->assertEquals($initialAdminBalance + $expectedPlatformCommission, $adminWallet->balance);
        $this->assertEquals($expectedPlatformCommission, $result['platform_commission']);

        // Check that vendor transaction exists
        $this->assertDatabaseHas('wallet_transactions', [
            'wallet_id' => $vendorWallet->id,
            'amount' => $expectedVendorCommission,
            'type' => 'credit',
            'transactionable_type' => OrderSale::class,
            'transactionable_id' => $this->order->id,
        ]);

        // Check that admin transaction exists
        $this->assertDatabaseHas('wallet_transactions', [
            'wallet_id' => $adminWallet->id,
            'amount' => $expectedPlatformCommission,
            'type' => 'credit',
            'transactionable_type' => OrderSale::class,
            'transactionable_id' => $this->order->id,
        ]);
    }

    /** @test */
    public function it_returns_correct_allowed_transitions()
    {
        // Test pending approval transitions
        $pendingTransitions = $this->salesWorkflowService->getAllowedTransitions(OrderSale::STATUS_PENDING_APPROVAL);
        $this->assertContains(OrderSale::STATUS_APPROVED, $pendingTransitions);
        $this->assertContains(OrderSale::STATUS_REJECTED, $pendingTransitions);

        // Test approved transitions
        $approvedTransitions = $this->salesWorkflowService->getAllowedTransitions(OrderSale::STATUS_APPROVED);
        $this->assertContains(OrderSale::STATUS_PIECE_SENT, $approvedTransitions);

        // Test sent transitions
        $sentTransitions = $this->salesWorkflowService->getAllowedTransitions(OrderSale::STATUS_PIECE_SENT);
        $this->assertContains(OrderSale::STATUS_SOLD, $sentTransitions);
    }

    /** @test */
    public function it_returns_correct_available_actions()
    {
        // Test actions for pending approval order
        $this->order->update(['status' => OrderSale::STATUS_PENDING_APPROVAL]);
        $actions = $this->salesWorkflowService->getAvailableActions($this->order);
        $this->assertContains('approve', $actions);
        $this->assertContains('reject', $actions);

        // Test actions for approved order
        $this->order->update(['status' => OrderSale::STATUS_APPROVED]);
        $actions = $this->salesWorkflowService->getAvailableActions($this->order);
        $this->assertContains('mark_as_sent', $actions);

        // Test actions for sent order
        $this->order->update(['status' => OrderSale::STATUS_PIECE_SENT]);
        $actions = $this->salesWorkflowService->getAvailableActions($this->order);
        $this->assertContains('mark_as_sold', $actions);
    }

    /** @test */
    public function it_calculates_commission_correctly()
    {
        // Test with different commission percentages
        SystemSetting::first()->update([
            'platform_commission_percentage' => 5.00,
            'merchant_commission_percentage' => 20.00,
        ]);

        // Create an admin user for platform commission
        $admin = User::factory()->create(['email_verified_at' => now()]);
        $admin->assignRole('admin');

        $this->order->update([
            'status' => OrderSale::STATUS_PIECE_SENT,
            'branch_id' => $this->branch->id,
            'total_price' => 500.00
        ]);

        $vendorWallet = Wallet::factory()->create(['user_id' => $this->vendor->id, 'balance' => 0]);
        $adminWallet = Wallet::factory()->create(['user_id' => $admin->id, 'balance' => 0]);
        
        $result = $this->salesWorkflowService->markAsSold($this->order, $this->vendor);

        $vendorWallet->refresh();
        $adminWallet->refresh();
        
        $expectedVendorCommission = (500.00 * 20) / 100; // 20% of 500
        $expectedPlatformCommission = (500.00 * 5) / 100; // 5% of 500
        
        $this->assertEquals($expectedVendorCommission, $vendorWallet->balance);
        $this->assertEquals($expectedVendorCommission, $result['vendor_commission']);
        
        $this->assertEquals($expectedPlatformCommission, $adminWallet->balance);
        $this->assertEquals($expectedPlatformCommission, $result['platform_commission']);
    }
} 