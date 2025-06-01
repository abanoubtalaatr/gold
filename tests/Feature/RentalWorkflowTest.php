<?php

namespace Tests\Feature;

use App\Events\RentalStatusUpdatedEvent;
use App\Models\Branch;
use App\Models\GoldPiece;
use App\Models\OrderRental;
use App\Models\User;
use App\Notifications\RentalStatusNotification;
use App\Services\RentalWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RentalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected $rentalWorkflowService;
    protected $user;
    protected $vendor;
    protected $goldPiece;
    protected $branch;
    protected $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rentalWorkflowService = app(RentalWorkflowService::class);

        // Create test data
        $this->user = User::factory()->create();
        $this->vendor = User::factory()->create();
        $this->goldPiece = GoldPiece::factory()->create(['user_id' => $this->user->id]);
        $this->branch = Branch::factory()->create(['vendor_id' => $this->vendor->id]);
        
        $this->order = OrderRental::factory()->create([
            'user_id' => $this->user->id,
            'gold_piece_id' => $this->goldPiece->id,
            'branch_id' => $this->branch->id,
            'status' => OrderRental::STATUS_PENDING_APPROVAL,
            'type' => OrderRental::LEASE_TYPE,
        ]);
    }

    public function test_can_approve_rental_request()
    {
        Event::fake();
        Notification::fake();

        $result = $this->rentalWorkflowService->approve($this->order, $this->branch->id, $this->vendor);

        $this->assertTrue($result);
        $this->assertEquals(OrderRental::STATUS_APPROVED, $this->order->fresh()->status);
        $this->assertEquals($this->branch->id, $this->order->fresh()->branch_id);

        // Assert event was dispatched
        Event::assertDispatched(RentalStatusUpdatedEvent::class);

        // Assert notifications were sent
        Notification::assertSentTo($this->user, RentalStatusNotification::class);
    }

    public function test_can_reject_rental_request()
    {
        Event::fake();
        Notification::fake();

        $result = $this->rentalWorkflowService->reject($this->order, $this->vendor);

        $this->assertTrue($result);
        $this->assertEquals(OrderRental::STATUS_REJECTED, $this->order->fresh()->status);

        Event::assertDispatched(RentalStatusUpdatedEvent::class);
        Notification::assertSentTo($this->user, RentalStatusNotification::class);
    }

    public function test_can_mark_piece_as_sent()
    {
        // First approve the order
        $this->order->update(['status' => OrderRental::STATUS_APPROVED]);

        Event::fake();
        Notification::fake();

        $result = $this->rentalWorkflowService->markAsSent($this->order, $this->vendor);

        $this->assertTrue($result);
        $this->assertEquals(OrderRental::STATUS_PIECE_SENT, $this->order->fresh()->status);

        Event::assertDispatched(RentalStatusUpdatedEvent::class);
        Notification::assertSentTo($this->user, RentalStatusNotification::class);
    }

    public function test_can_confirm_rental()
    {
        // Set order to piece_sent status
        $this->order->update(['status' => OrderRental::STATUS_PIECE_SENT]);

        Event::fake();
        Notification::fake();

        $result = $this->rentalWorkflowService->confirmRental($this->order, $this->vendor);

        $this->assertTrue($result);
        $this->assertEquals(OrderRental::STATUS_RENTED, $this->order->fresh()->status);

        Event::assertDispatched(RentalStatusUpdatedEvent::class);
        Notification::assertSentTo($this->user, RentalStatusNotification::class);
    }

    public function test_can_complete_rental()
    {
        // Set order to rented status
        $this->order->update(['status' => OrderRental::STATUS_RENTED]);

        Event::fake();
        Notification::fake();

        $result = $this->rentalWorkflowService->completeRental($this->order, $this->vendor);

        $this->assertTrue($result);
        $this->assertEquals(OrderRental::STATUS_AVAILABLE, $this->order->fresh()->status);

        Event::assertDispatched(RentalStatusUpdatedEvent::class);
        Notification::assertSentTo($this->user, RentalStatusNotification::class);
    }

    public function test_get_allowed_transitions()
    {
        $pendingTransitions = $this->rentalWorkflowService->getAllowedTransitions(OrderRental::STATUS_PENDING_APPROVAL);
        $this->assertContains(OrderRental::STATUS_APPROVED, $pendingTransitions);
        $this->assertContains(OrderRental::STATUS_REJECTED, $pendingTransitions);

        $approvedTransitions = $this->rentalWorkflowService->getAllowedTransitions(OrderRental::STATUS_APPROVED);
        $this->assertContains(OrderRental::STATUS_PIECE_SENT, $approvedTransitions);
        $this->assertContains(OrderRental::STATUS_REJECTED, $approvedTransitions);

        $sentTransitions = $this->rentalWorkflowService->getAllowedTransitions(OrderRental::STATUS_PIECE_SENT);
        $this->assertContains(OrderRental::STATUS_RENTED, $sentTransitions);
        $this->assertContains(OrderRental::STATUS_AVAILABLE, $sentTransitions);

        $rentedTransitions = $this->rentalWorkflowService->getAllowedTransitions(OrderRental::STATUS_RENTED);
        $this->assertContains(OrderRental::STATUS_AVAILABLE, $rentedTransitions);
    }

    public function test_invalid_transition_validation()
    {
        $this->assertFalse($this->rentalWorkflowService->isValidTransition(
            OrderRental::STATUS_PENDING_APPROVAL,
            OrderRental::STATUS_RENTED
        ));

        $this->assertTrue($this->rentalWorkflowService->isValidTransition(
            OrderRental::STATUS_PENDING_APPROVAL,
            OrderRental::STATUS_APPROVED
        ));
    }

    public function test_status_workflow_updates_dates()
    {
        // Test approval sets start_date
        $this->rentalWorkflowService->approve($this->order, $this->branch->id, $this->vendor);
        $this->assertNotNull($this->order->fresh()->start_date);

        // Test confirming rental updates start_date to now
        $this->order->update(['status' => OrderRental::STATUS_PIECE_SENT]);
        $this->rentalWorkflowService->confirmRental($this->order, $this->vendor);
        
        $this->assertNotNull($this->order->fresh()->start_date);
        $this->assertLessThanOrEqual(now(), $this->order->fresh()->start_date);
    }
} 