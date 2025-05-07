<?php

namespace Database\Seeders;

use App\Models\Inventory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $inventoryItems = [
            [
                'id' => 'INV-001',
                'name' => 'Power Generator',
                'category' => 'Power Tools',
                'status' => 'Available',
                'rate' => 45.00,
                'description' => 'Powerful 5000W generator ideal for construction sites and outdoor events.',
                'features' => ['Heavy Duty', 'Fuel Efficient', 'Low Noise'],
                'acquisition_date' => '2025-01-05',
                'rental_history' => [
                    ['id' => 1, 'date' => '2025-03-10', 'customer' => 'John Smith', 'duration' => '3 days', 'status' => 'Completed'],
                    ['id' => 2, 'date' => '2025-02-15', 'customer' => 'Sarah Johnson', 'duration' => '1 week', 'status' => 'Completed']
                ],
                'maintenance_records' => []
            ],
            [
                'id' => 'INV-002',
                'name' => 'Floor Sander',
                'category' => 'Power Tools',
                'status' => 'Available',
                'rate' => 35.00,
                'description' => 'Professional drum sander for efficient floor refinishing.',
                'features' => ['Easy Operation', 'Dust Collection', 'Adjustable Speed'],
                'acquisition_date' => '2025-02-12',
                'rental_history' => [],
                'maintenance_records' => []
            ],
            [
                'id' => 'INV-003',
                'name' => 'Concrete Mixer',
                'category' => 'Construction',
                'status' => 'Rented',
                'rate' => 40.00,
                'description' => 'Reliable mixer for small to medium concrete jobs.',
                'features' => ['Durable', 'Easy to Clean', 'Portable'],
                'acquisition_date' => '2024-11-20',
                'rental_history' => [
                    ['id' => 1, 'date' => '2025-05-01', 'customer' => 'Mike Williams', 'duration' => 'Ongoing', 'status' => 'Active']
                ],
                'maintenance_records' => []
            ],
            [
                'id' => 'INV-004',
                'name' => 'Pressure Washer',
                'category' => 'Power Tools',
                'status' => 'Rented',
                'rate' => 25.00,
                'description' => 'High-pressure cleaner for various surfaces including driveways and decks.',
                'features' => ['Powerful Spray', 'Multiple Nozzles', 'Compact Storage'],
                'acquisition_date' => '2025-01-10',
                'rental_history' => [
                    ['id' => 1, 'date' => '2025-04-28', 'customer' => 'Emily Brown', 'duration' => 'Ongoing', 'status' => 'Active']
                ],
                'maintenance_records' => []
            ],
            [
                'id' => 'INV-005',
                'name' => 'Scissor Lift',
                'category' => 'Construction',
                'status' => 'Maintenance',
                'rate' => 75.00,
                'description' => 'Electric scissor lift with 26ft reach, perfect for indoor and outdoor work.',
                'features' => ['Non-Marking Tires', 'Self-Leveling', 'Battery Powered'],
                'acquisition_date' => '2024-09-15',
                'rental_history' => [],
                'maintenance_records' => [
                    ['id' => 1, 'date' => '2025-04-25', 'type' => 'Regular Maintenance', 'description' => 'Hydraulic system check and oil change'],
                    ['id' => 2, 'date' => '2025-04-27', 'type' => 'Repair', 'description' => 'Control panel replacement needed']
                ]
            ],
            [
                'id' => 'INV-006',
                'name' => 'Jackhammer',
                'category' => 'Power Tools',
                'status' => 'Rented',
                'rate' => 30.00,
                'description' => 'Powerful electric jackhammer for breaking concrete and asphalt.',
                'features' => ['Anti-Vibration Handle', 'Quick-Change Bits', 'Ergonomic Design'],
                'acquisition_date' => '2024-12-03',
                'rental_history' => [
                    ['id' => 1, 'date' => '2025-05-03', 'customer' => 'Robert Taylor', 'duration' => 'Ongoing', 'status' => 'Active']
                ],
                'maintenance_records' => []
            ],
            [
                'id' => 'INV-007',
                'name' => 'Paint Sprayer',
                'category' => 'Power Tools',
                'status' => 'Rented',
                'rate' => 20.00,
                'description' => 'HVLP paint sprayer for smooth, even finishes on various surfaces.',
                'features' => ['Adjustable Flow', 'Easy Cleanup', 'Fine Finish'],
                'acquisition_date' => '2025-03-20',
                'rental_history' => [
                    ['id' => 1, 'date' => '2025-05-01', 'customer' => 'Lisa Anderson', 'duration' => 'Ongoing', 'status' => 'Active']
                ],
                'maintenance_records' => []
            ],
            [
                'id' => 'INV-008',
                'name' => 'Tile Cutter',
                'category' => 'Power Tools',
                'status' => 'Available',
                'rate' => 15.00,
                'description' => 'Wet tile saw for precise cutting of ceramic and porcelain tiles.',
                'features' => ['Water Cooling System', 'Diagonal Cutting', 'Stable Base'],
                'acquisition_date' => '2025-01-15',
                'rental_history' => [
                    ['id' => 1, 'date' => '2025-03-15', 'customer' => 'James Wilson', 'duration' => '2 days', 'status' => 'Completed']
                ],
                'maintenance_records' => []
            ],
            [
                'id' => 'INV-009',
                'name' => 'Chainsaw',
                'category' => 'Landscaping',
                'status' => 'Available',
                'rate' => 25.00,
                'description' => 'Gas-powered chainsaw for tree cutting and trimming.',
                'features' => ['Safety Brake', 'Anti-Vibration', 'Easy Start'],
                'acquisition_date' => '2024-10-10',
                'rental_history' => [],
                'maintenance_records' => [
                    ['id' => 1, 'date' => '2025-03-01', 'type' => 'Regular Maintenance', 'description' => 'Chain sharpening and oil change']
                ]
            ],
            [
                'id' => 'INV-010',
                'name' => 'Lawn Mower',
                'category' => 'Landscaping',
                'status' => 'Available',
                'rate' => 20.00,
                'description' => 'Self-propelled gas lawn mower with mulching capability.',
                'features' => ['Height Adjustment', 'Bag Included', 'Easy Start'],
                'acquisition_date' => '2025-02-20',
                'rental_history' => [],
                'maintenance_records' => []
            ],
            [
                'id' => 'INV-011',
                'name' => 'Pipe Cutter',
                'category' => 'Plumbing',
                'status' => 'Available',
                'rate' => 15.00,
                'description' => 'Professional pipe cutter for copper and PVC pipes.',
                'features' => ['Quick Cutting', 'Deburring Tool', 'Multiple Sizes'],
                'acquisition_date' => '2025-01-10',
                'rental_history' => [],
                'maintenance_records' => []
            ],
            [
                'id' => 'INV-012',
                'name' => 'Drain Snake',
                'category' => 'Plumbing',
                'status' => 'Maintenance',
                'rate' => 12.00,
                'description' => 'Electric drain auger for clearing stubborn clogs.',
                'features' => ['50ft Cable', 'Variable Speed', 'Foot Pedal Control'],
                'acquisition_date' => '2024-11-15',
                'rental_history' => [],
                'maintenance_records' => [
                    ['id' => 1, 'date' => '2025-05-02', 'type' => 'Repair', 'description' => 'Cable replacement needed']
                ]
            ]
        ];

        foreach ($inventoryItems as $item) {
            Inventory::create($item);
        }
    }
}