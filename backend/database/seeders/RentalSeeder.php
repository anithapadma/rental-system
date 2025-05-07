<?php

namespace Database\Seeders;

use App\Models\Rental;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RentalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rentals = [
            [
                'id' => 'RNT-'.Str::random(6),
                'customer' => 'John Smith',
                'items' => 'Power Generator XL-5000',
                'start_date' => '2025-05-01',
                'due_date' => '2025-05-08',
                'status' => 'Active',
                'daily_rate' => 75.00,
                'notes' => 'Customer requested delivery to construction site',
            ],
            [
                'id' => 'RNT-'.Str::random(6),
                'customer' => 'Sarah Johnson',
                'items' => 'Floor Sander Pro, Dust Collector',
                'start_date' => '2025-04-28',
                'due_date' => '2025-05-05',
                'status' => 'Returned',
                'daily_rate' => 45.00,
                'notes' => 'Returned one day early',
            ],
            [
                'id' => 'RNT-'.Str::random(6),
                'customer' => 'Mike Williams',
                'items' => 'Concrete Mixer Heavy Duty',
                'start_date' => '2025-04-20',
                'due_date' => '2025-04-30',
                'status' => 'Overdue',
                'daily_rate' => 60.00,
                'notes' => 'Customer called to extend rental period',
            ],
            [
                'id' => 'RNT-'.Str::random(6),
                'customer' => 'Emily Brown',
                'items' => 'Pressure Washer 3000PSI, Extension Wand',
                'start_date' => '2025-05-03',
                'due_date' => '2025-05-10',
                'status' => 'Active',
                'daily_rate' => 35.00,
                'notes' => null,
            ],
            [
                'id' => 'RNT-'.Str::random(6),
                'customer' => 'David Miller',
                'items' => 'Scissor Lift 20ft',
                'start_date' => '2025-04-25',
                'due_date' => '2025-05-15',
                'status' => 'Maintenance',
                'daily_rate' => 120.00,
                'notes' => 'Maintenance scheduled for hydraulic system',
            ],
            [
                'id' => 'RNT-'.Str::random(6),
                'customer' => 'Jennifer Wilson',
                'items' => 'Tile Cutter, Grout Float Set',
                'start_date' => '2025-05-02',
                'due_date' => '2025-05-09',
                'status' => 'Active',
                'daily_rate' => 25.00,
                'notes' => 'Customer paid deposit in cash',
            ],
            [
                'id' => 'RNT-'.Str::random(6),
                'customer' => 'Robert Garcia',
                'items' => 'Jackhammer with Compressor',
                'start_date' => '2025-04-15',
                'due_date' => '2025-04-29',
                'status' => 'Returned',
                'daily_rate' => 85.00,
                'notes' => 'Equipment returned with minor damage, repair fee applied',
            ],
            [
                'id' => 'RNT-'.Str::random(6),
                'customer' => 'Lisa Martinez',
                'items' => 'Boom Lift 30ft',
                'start_date' => '2025-04-30',
                'due_date' => '2025-05-14',
                'status' => 'Active',
                'daily_rate' => 150.00,
                'notes' => 'Requires certified operator',
            ],
            [
                'id' => 'RNT-'.Str::random(6),
                'customer' => 'Kevin Taylor',
                'items' => 'Rotary Hammer Drill Kit',
                'start_date' => '2025-04-22',
                'due_date' => '2025-04-24',
                'status' => 'Returned',
                'daily_rate' => 30.00,
                'notes' => null,
            ],
            [
                'id' => 'RNT-'.Str::random(6),
                'customer' => 'Amanda White',
                'items' => 'Portable Air Conditioner 12,000 BTU',
                'start_date' => '2025-05-04',
                'due_date' => '2025-05-11',
                'status' => 'Active',
                'daily_rate' => 40.00,
                'notes' => 'For office event',
            ],
        ];

        foreach ($rentals as $rental) {
            Rental::create($rental);
        }
    }
}