<?php

namespace Database\Factories;

use App\Models\Rental;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rental>
 */
class RentalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = Carbon::instance($this->faker->dateTimeBetween('-2 months', 'now'));
        $dueDate = (clone $startDate)->addDays($this->faker->numberBetween(1, 14));
        $status = $this->faker->randomElement(['Active', 'Returned', 'Overdue', 'Maintenance']);
        
        // If due date is past and status is active, mark as overdue
        if ($dueDate->isPast() && $status === 'Active') {
            $status = 'Overdue';
        }
        
        // Use static counter to ensure unique IDs
        static $rentalCount = 0;
        $rentalCount++;
        
        return [
            'id' => 'R-2025-' . str_pad($rentalCount, 3, '0', STR_PAD_LEFT),
            'customer' => $this->faker->name(),
            'items' => $this->faker->randomElement([
                'Power Generator', 
                'Floor Sander',
                'Concrete Mixer',
                'Pressure Washer',
                'Scissor Lift',
                'Jackhammer',
                'Paint Sprayer',
                'Tile Cutter',
                'Air Compressor',
                'Chainsaw'
            ]),
            'start_date' => $startDate,
            'due_date' => $dueDate,
            'status' => $status,
            'daily_rate' => $this->faker->randomFloat(2, 15, 75),
            'notes' => $this->faker->optional(0.7)->sentence(),
        ];
    }
}