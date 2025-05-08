<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmployeeDashboardController extends Controller
{
    /**
     * Get all dashboard data for the employee in a single request
     */
    public function getAll()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'tasks' => $this->getTasks()->original['data'],
                'schedule' => $this->getSchedule()->original['data'],
                'rentals' => $this->getRentals()->original['data'],
                'performance' => $this->getPerformance()->original['data'],
                'taskDistribution' => $this->getTaskDistribution()->original['data'],
                'rentalStats' => $this->getRentalStats()->original['data'],
                'team' => $this->getTeam()->original['data'],
            ]
        ]);
    }

    /**
     * Get employee's tasks for dashboard
     */
    public function getTasks()
    {
        $tasks = [
            [
                'name' => 'Process rental requests',
                'status' => 'pending'
            ],
            [
                'name' => 'Update inventory items',
                'status' => 'completed'
            ],
            [
                'name' => 'Contact customer #12458',
                'status' => 'pending'
            ],
            [
                'name' => 'Prepare monthly equipment report',
                'status' => 'in-progress'
            ]
        ];

        return response()->json([
            'status' => 'success',
            'data' => $tasks
        ]);
    }

    /**
     * Get employee's schedule for dashboard
     */
    public function getSchedule()
    {
        $schedule = [
            [
                'time' => '9:00 AM',
                'title' => 'Morning check-in'
            ],
            [
                'time' => '11:30 AM',
                'title' => 'Inventory check'
            ],
            [
                'time' => '2:00 PM',
                'title' => 'Customer follow-ups'
            ],
            [
                'time' => '4:30 PM',
                'title' => 'Team meeting'
            ]
        ];

        return response()->json([
            'status' => 'success',
            'data' => $schedule
        ]);
    }

    /**
     * Get recent rentals for dashboard
     */
    public function getRentals()
    {
        $rentals = [
            [
                'id' => '#45782',
                'customer' => 'John Smith',
                'date' => 'May 4, 2025'
            ],
            [
                'id' => '#45780',
                'customer' => 'Sarah Johnson',
                'date' => 'May 3, 2025'
            ],
            [
                'id' => '#45779',
                'customer' => 'Mike Davis',
                'date' => 'May 3, 2025'
            ],
            [
                'id' => '#45775',
                'customer' => 'Emily Brown',
                'date' => 'May 2, 2025'
            ]
        ];

        return response()->json([
            'status' => 'success',
            'data' => $rentals
        ]);
    }

    /**
     * Get performance chart data
     */
    public function getPerformance()
    {
        $performance = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
            'datasets' => [
                [
                    'label' => 'Performance Score',
                    'data' => [85, 88, 92, 90, 94],
                    'borderColor' => '#3490dc',
                    'backgroundColor' => 'rgba(52, 144, 220, 0.1)',
                    'tension' => 0.3,
                    'fill' => true
                ]
            ]
        ];

        return response()->json([
            'status' => 'success',
            'data' => $performance
        ]);
    }

    /**
     * Get task distribution data
     */
    public function getTaskDistribution()
    {
        $taskDistribution = [
            'labels' => ['Pending', 'In Progress', 'Completed', 'Overdue'],
            'datasets' => [
                [
                    'data' => [12, 8, 18, 3],
                    'backgroundColor' => [
                        '#fcd34d', // Pending (amber)
                        '#60a5fa', // In Progress (blue)
                        '#34d399', // Completed (green)
                        '#f87171'  // Overdue (red)
                    ],
                    'borderColor' => '#ffffff',
                ]
            ]
        ];

        return response()->json([
            'status' => 'success',
            'data' => $taskDistribution
        ]);
    }

    /**
     * Get rental statistics data
     */
    public function getRentalStats()
    {
        $rentalStats = [
            'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            'datasets' => [
                [
                    'label' => 'New Rentals',
                    'data' => [5, 9, 7, 8, 12, 15, 6],
                    'backgroundColor' => '#8b5cf6', // Purple
                ]
            ]
        ];

        return response()->json([
            'status' => 'success',
            'data' => $rentalStats
        ]);
    }

    /**
     * Get team members (employees)
     */
    public function getTeam()
    {
        $team = [
            [
                'id' => 1,
                'name' => 'John Doe',
                'role' => 'Senior Rental Agent',
                'department' => 'Rental Operations',
                'image' => 'https://randomuser.me/api/portraits/men/1.jpg',
                'email' => 'john.doe@example.com',
                'phone' => '(555) 123-4567',
                'joinDate' => '2023-03-15',
                'isActive' => true,
                'stats' => [
                    'tasksCompleted' => 124,
                    'avgRating' => 4.8,
                    'efficiency' => 94
                ]
            ],
            [
                'id' => 2,
                'name' => 'Sarah Johnson',
                'role' => 'Inventory Specialist',
                'department' => 'Inventory Management',
                'image' => 'https://randomuser.me/api/portraits/women/2.jpg',
                'email' => 'sarah.johnson@example.com',
                'phone' => '(555) 234-5678',
                'joinDate' => '2023-05-22',
                'isActive' => true,
                'stats' => [
                    'tasksCompleted' => 98,
                    'avgRating' => 4.6,
                    'efficiency' => 91
                ]
            ],
            [
                'id' => 3,
                'name' => 'Michael Chen',
                'role' => 'Customer Service Rep',
                'department' => 'Customer Support',
                'image' => 'https://randomuser.me/api/portraits/men/3.jpg',
                'email' => 'michael.chen@example.com',
                'phone' => '(555) 345-6789',
                'joinDate' => '2024-01-10',
                'isActive' => true,
                'stats' => [
                    'tasksCompleted' => 45,
                    'avgRating' => 4.9,
                    'efficiency' => 88
                ]
            ],
            [
                'id' => 4,
                'name' => 'Amanda Williams',
                'role' => 'Team Lead',
                'department' => 'Rental Operations',
                'image' => 'https://randomuser.me/api/portraits/women/4.jpg',
                'email' => 'amanda.williams@example.com',
                'phone' => '(555) 456-7890',
                'joinDate' => '2022-11-05',
                'isActive' => true,
                'stats' => [
                    'tasksCompleted' => 187,
                    'avgRating' => 4.7,
                    'efficiency' => 96
                ]
            ]
        ];

        return response()->json([
            'status' => 'success',
            'data' => $team
        ]);
    }
}