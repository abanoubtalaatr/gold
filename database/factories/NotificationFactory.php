<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $notificationTypes = [
            'App\Notifications\OrderRentalNotification',
            'App\Notifications\PaymentConfirmationNotification',
            'App\Notifications\ComplaintResponseNotification',
            'App\Notifications\RentalAcceptedNotification',
            'App\Notifications\PurchaseAcceptedNotification',
            'App\Notifications\NewRentalBookingNotification',
            'App\Notifications\PieceStatusUpdatedNotification',
            'App\Notifications\LiquidationAcceptedNotification',
            'App\Notifications\WalletProfitsNotification',
        ];

        $user = User::factory()->create();
        $notificationType = $this->faker->randomElement($notificationTypes);

        $data = match ($notificationType) {
            'App\Notifications\OrderRentalNotification' => [
                'order_id' => $this->faker->randomNumber(5),
                'user_id' => $user->id,
                'gold_piece_id' => $this->faker->randomNumber(3),
                'type' => 'rental',
                'status' => 'pending',
                'message' => __('notifications.new_rental_request', [
                    'user' => $user->name,
                    'piece' => 'Gold Piece #' . $this->faker->randomNumber(3)
                ])
            ],
            'App\Notifications\PaymentConfirmationNotification' => [
                'order_id' => $this->faker->randomNumber(5),
                'amount' => $this->faker->randomFloat(2, 100, 10000),
                'message' => __('notifications.payment_confirmed', [
                    'order_id' => $this->faker->randomNumber(5)
                ])
            ],
            'App\Notifications\WalletProfitsNotification' => [
                'amount' => $this->faker->randomFloat(2, 100, 5000),
                'message' => __('notifications.wallet_profits', [
                    'amount' => $this->faker->randomFloat(2, 100, 5000)
                ])
            ],
            default => [
                'message' => $this->faker->sentence(),
                'data' => $this->faker->sentence()
            ]
        };

        return [
            'id' => Str::uuid(),
            'type' => $notificationType,
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => $data,
            'read_at' => $this->faker->boolean(30) ? $this->faker->dateTimeBetween('-1 month') : null,
            'created_at' => $this->faker->dateTimeBetween('-3 months'),
            'updated_at' => function (array $attributes) {
                return $attributes['created_at'];
            },
        ];
    }

    /**
     * Indicate that the notification is unread.
     */
    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'read_at' => null,
        ]);
    }

    /**
     * Indicate that the notification is read.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'read_at' => $this->faker->dateTimeBetween('-1 month'),
        ]);
    }

    /**
     * Create a notification for a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'notifiable_id' => $user->id,
            'notifiable_type' => User::class,
        ]);
    }
} 