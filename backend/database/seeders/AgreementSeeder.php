<?php

namespace Database\Seeders;

use App\Models\Agreement;
use App\Models\AgreementItem;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AgreementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample agreements
        $agreements = [
            [
                'id' => 'AGR-001',
                'customer' => 'John Smith',
                'customerEmail' => 'john@example.com',
                'customerPhone' => '(555) 123-4567',
                'startDate' => '2025-04-28',
                'endDate' => '2025-05-04',
                'value' => 315,
                'status' => 'Active',
                'paymentMethod' => 'Credit Card',
                'paymentStatus' => 'Paid',
                'notes' => 'Weekend rental for home renovation project',
                'createdDate' => '2025-04-27',
                'updatedDate' => '2025-04-27',
                'items' => [
                    ['name' => 'Power Generator', 'quantity' => 1, 'rate' => 45, 'total' => 135],
                    ['name' => 'Floor Sander', 'quantity' => 1, 'rate' => 35, 'total' => 105],
                    ['name' => 'Pressure Washer', 'quantity' => 1, 'rate' => 25, 'total' => 75]
                ]
            ],
            [
                'id' => 'AGR-002',
                'customer' => 'Emily Brown',
                'customerEmail' => 'emily@example.com',
                'customerPhone' => '(555) 234-5678',
                'startDate' => '2025-04-28',
                'endDate' => '2025-05-05',
                'value' => 175,
                'status' => 'Active',
                'paymentMethod' => 'Debit Card',
                'paymentStatus' => 'Paid',
                'notes' => 'Garden preparation tools',
                'createdDate' => '2025-04-27',
                'updatedDate' => '2025-04-27',
                'items' => [
                    ['name' => 'Lawn Aerator', 'quantity' => 1, 'rate' => 25, 'total' => 50],
                    ['name' => 'Garden Tiller', 'quantity' => 1, 'rate' => 35, 'total' => 70],
                    ['name' => 'Hedge Trimmer', 'quantity' => 1, 'rate' => 15, 'total' => 30],
                    ['name' => 'Leaf Blower', 'quantity' => 1, 'rate' => 25, 'total' => 25]
                ]
            ],
            [
                'id' => 'AGR-003',
                'customer' => 'Mike Williams',
                'customerEmail' => 'mike@example.com',
                'customerPhone' => '(555) 345-6789',
                'startDate' => '2025-04-20',
                'endDate' => '2025-04-29',
                'value' => 400,
                'status' => 'Expired',
                'paymentMethod' => 'Cash',
                'paymentStatus' => 'Paid',
                'notes' => 'Construction equipment for garage build',
                'createdDate' => '2025-04-19',
                'updatedDate' => '2025-04-30',
                'items' => [
                    ['name' => 'Concrete Mixer', 'quantity' => 1, 'rate' => 40, 'total' => 200],
                    ['name' => 'Jackhammer', 'quantity' => 1, 'rate' => 30, 'total' => 150],
                    ['name' => 'Nail Gun', 'quantity' => 1, 'rate' => 10, 'total' => 50]
                ]
            ],
            [
                'id' => 'AGR-004',
                'customer' => 'Sarah Johnson',
                'customerEmail' => 'sarah@example.com',
                'customerPhone' => '(555) 456-7890',
                'startDate' => '2025-04-25',
                'endDate' => '2025-04-30',
                'value' => 175,
                'status' => 'Completed',
                'paymentMethod' => 'Credit Card',
                'paymentStatus' => 'Paid',
                'notes' => 'Weekend painting project',
                'createdDate' => '2025-04-24',
                'updatedDate' => '2025-05-01',
                'items' => [
                    ['name' => 'Paint Sprayer', 'quantity' => 1, 'rate' => 20, 'total' => 100],
                    ['name' => 'Scaffold', 'quantity' => 1, 'rate' => 15, 'total' => 75]
                ]
            ],
            [
                'id' => 'AGR-005',
                'customer' => 'David Miller',
                'customerEmail' => 'david@example.com',
                'customerPhone' => '(555) 567-8901',
                'startDate' => '2025-04-22',
                'endDate' => '2025-04-27',
                'value' => 375,
                'status' => 'Cancelled',
                'paymentMethod' => 'Bank Transfer',
                'paymentStatus' => 'Refunded',
                'notes' => 'Project cancelled due to weather',
                'createdDate' => '2025-04-21',
                'updatedDate' => '2025-04-22',
                'items' => [
                    ['name' => 'Scissor Lift', 'quantity' => 1, 'rate' => 75, 'total' => 375]
                ]
            ],
            [
                'id' => 'AGR-006',
                'customer' => 'Robert Jones',
                'customerEmail' => 'robert@example.com',
                'customerPhone' => '(555) 678-9012',
                'startDate' => '2025-05-01',
                'endDate' => '2025-05-03',
                'value' => 90,
                'status' => 'Active',
                'paymentMethod' => 'Credit Card',
                'paymentStatus' => 'Paid',
                'notes' => 'Weekend plumbing project',
                'createdDate' => '2025-04-30',
                'updatedDate' => '2025-04-30',
                'items' => [
                    ['name' => 'Pipe Cutter', 'quantity' => 1, 'rate' => 15, 'total' => 30],
                    ['name' => 'Drain Snake', 'quantity' => 1, 'rate' => 12, 'total' => 24],
                    ['name' => 'Pipe Threading Machine', 'quantity' => 1, 'rate' => 18, 'total' => 36]
                ]
            ],
            [
                'id' => 'AGR-007',
                'customer' => 'Lisa Davis',
                'customerEmail' => 'lisa@example.com',
                'customerPhone' => '(555) 789-0123',
                'startDate' => '2025-05-01',
                'endDate' => '2025-05-05',
                'value' => 80,
                'status' => 'Active',
                'paymentMethod' => 'Debit Card',
                'paymentStatus' => 'Paid',
                'notes' => 'Flooring installation',
                'createdDate' => '2025-04-29',
                'updatedDate' => '2025-04-29',
                'items' => [
                    ['name' => 'Floor Nailer', 'quantity' => 1, 'rate' => 20, 'total' => 80]
                ]
            ],
            [
                'id' => 'AGR-008',
                'customer' => 'James Wilson',
                'customerEmail' => 'james@example.com',
                'customerPhone' => '(555) 890-1234',
                'startDate' => '2025-04-29',
                'endDate' => '2025-05-02',
                'value' => 45,
                'status' => 'Active',
                'paymentMethod' => 'Cash',
                'paymentStatus' => 'Paid',
                'notes' => 'Tile cutting for bathroom remodel',
                'createdDate' => '2025-04-28',
                'updatedDate' => '2025-04-28',
                'items' => [
                    ['name' => 'Tile Cutter', 'quantity' => 1, 'rate' => 15, 'total' => 45]
                ]
            ]
        ];

        foreach ($agreements as $agreementData) {
            // Extract and remove items from agreement data
            $items = $agreementData['items'];
            unset($agreementData['items']);
            
            // Check if agreement already exists
            if (!Agreement::where('id', $agreementData['id'])->exists()) {
                // Create agreement
                $agreement = Agreement::create($agreementData);
                
                // Create related items with correctly calculated totals
                foreach ($items as $itemData) {
                    AgreementItem::create([
                        'agreement_id' => $agreement->id,
                        'name' => $itemData['name'],
                        'quantity' => $itemData['quantity'],
                        'rate' => $itemData['rate'],
                        'total' => $itemData['quantity'] * $itemData['rate'], // Calculate total explicitly
                    ]);
                }
            } else {
                $this->command->info("Agreement {$agreementData['id']} already exists. Skipping...");
            }
        }
    }
}