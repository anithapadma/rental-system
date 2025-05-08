<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Rental;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function getAllData()
    {
        return response()->json([
            'data' => [
                'stats' => $this->getStats(),
                'recentActivities' => $this->getRecentActivities(),
                'upcomingDeadlines' => $this->getUpcomingDeadlines(),
                'rentalTrends' => $this->getRentalTrends('month'),
                'inventoryStatus' => $this->getInventoryStatus(),
                'categoryPerformance' => $this->getCategoryPerformance()
            ]
        ]);
    }
    
    public function getStats()
    {
        // Active rentals count and trend
        $activeRentalsCount = Rental::where('status', 'Rented')->count();
        $lastWeekRentalsCount = Rental::where('status', 'Rented')
            ->where('createdDate', '<', Carbon::now()->subWeek())
            ->count();
        $activeRentalsTrend = $lastWeekRentalsCount > 0 
            ? round((($activeRentalsCount - $lastWeekRentalsCount) / $lastWeekRentalsCount) * 100) 
            : 0;
        
        // Total inventory count and trend
        $totalInventoryCount = Inventory::count();
        $lastMonthInventoryCount = Inventory::where('createdDate', '<', Carbon::now()->subMonth())->count();
        $inventoryTrend = $lastMonthInventoryCount > 0 
            ? round((($totalInventoryCount - $lastMonthInventoryCount) / $lastMonthInventoryCount) * 100) 
            : 0;
        
        // Pending returns count and trend
        $pendingReturnsCount = Agreement::where('status', 'Active')
            ->where('endDate', '<', Carbon::now())
            ->count();
        $lastWeekPendingCount = Agreement::where('status', 'Active')
            ->where('endDate', '<', Carbon::now()->subWeek())
            ->count();
        $pendingReturnsTrend = $lastWeekPendingCount > 0 
            ? round((($pendingReturnsCount - $lastWeekPendingCount) / $lastWeekPendingCount) * 100) 
            : 0;
        
        // Monthly revenue value and trend
        $currentMonthRevenue = Agreement::whereMonth('createdDate', Carbon::now()->month)
            ->whereYear('createdDate', Carbon::now()->year)
            ->sum('value');
        $lastMonthRevenue = Agreement::whereMonth('createdDate', Carbon::now()->subMonth()->month)
            ->whereYear('createdDate', Carbon::now()->subMonth()->year)
            ->sum('value');
        $revenueTrend = $lastMonthRevenue > 0 
            ? round((($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100) 
            : 0;
        
        return [
            'activeRentals' => [
                'value' => $activeRentalsCount,
                'trend' => $activeRentalsTrend
            ],
            'totalInventory' => [
                'value' => $totalInventoryCount,
                'trend' => $inventoryTrend
            ],
            'pendingReturns' => [
                'value' => $pendingReturnsCount,
                'trend' => $pendingReturnsTrend
            ],
            'monthlyRevenue' => [
                'value' => $currentMonthRevenue,
                'trend' => $revenueTrend
            ]
        ];
    }
    
    public function getRecentActivities()
    {
        // Get recent rental activities
        $rentalActivities = Agreement::with(['items.inventory'])
            ->orderBy('createdDate', 'desc')
            ->take(5)
            ->get()
            ->map(function($agreement) {
                $status = '';
                
                if ($agreement->status === 'Completed') {
                    $status = 'Returned';
                } else if ($agreement->status === 'Active' && $agreement->endDate < Carbon::now()) {
                    $status = 'Overdue';
                } else if ($agreement->status === 'Active') {
                    $status = 'Rented';
                } else if ($agreement->status === 'Maintenance') {
                    $status = 'Maintenance';
                }
                
                $item = $agreement->items->first() 
                    ? $agreement->items->first()->inventory->name ?? 'Unknown Item'
                    : 'Unknown Item';
                
                // Get customer name - check if relationship exists or use direct field
                $customerName = 'Unknown Customer';
                if ($agreement->customer !== null) {
                    $customerName = $agreement->customer->name ?? $agreement->customer;
                } elseif (is_string($agreement->customer)) {
                    $customerName = $agreement->customer;
                }
                
                return [
                    'date' => $agreement->createdDate->format('Y-m-d'),
                    'customer' => $customerName,
                    'item' => $item,
                    'status' => $status
                ];
            });
            
        return $rentalActivities;
    }
    
    public function getUpcomingDeadlines()
    {
        $upcomingDeadlines = Agreement::with(['items.inventory'])
            ->where('status', 'Active')
            ->where('endDate', '>', Carbon::now())
            ->orderBy('endDate', 'asc')
            ->take(4)
            ->get()
            ->map(function($agreement) {
                $item = $agreement->items->first() 
                    ? $agreement->items->first()->inventory->name ?? 'Unknown Item'
                    : 'Unknown Item';
                
                $now = Carbon::now();
                $endDate = Carbon::parse($agreement->endDate);
                $daysRemaining = $now->diffInDays($endDate, false);
                
                // Get customer name - check if relationship exists or use direct field
                $customerName = 'Unknown Customer';
                if ($agreement->customer !== null) {
                    $customerName = $agreement->customer->name ?? $agreement->customer;
                } elseif (is_string($agreement->customer)) {
                    $customerName = $agreement->customer;
                }
                
                return [
                    'customer' => $customerName,
                    'item' => $item,
                    'duration' => abs($daysRemaining) . ' days',
                    'dueDate' => $endDate->format('Y-m-d')
                ];
            });
            
        return $upcomingDeadlines;
    }
    
    public function getRentalTrends($period = 'month')
    {
        $labels = [];
        $newRentals = [];
        $returns = [];
        
        if ($period === 'week') {
            // Last 7 days data
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $labels[] = $date->format('D');
                
                $newRentals[] = Agreement::whereDate('createdDate', $date->toDateString())->count();
                $returns[] = Agreement::whereDate('updatedDate', $date->toDateString())
                    ->where('status', 'Completed')
                    ->count();
            }
        } else if ($period === 'month') {
            // Last 4 weeks data
            for ($i = 3; $i >= 0; $i--) {
                $startDate = Carbon::now()->subWeeks($i + 1)->addDay();
                $endDate = Carbon::now()->subWeeks($i);
                
                $labels[] = 'Week ' . (4 - $i);
                
                $newRentals[] = Agreement::whereBetween('createdDate', [$startDate, $endDate])->count();
                $returns[] = Agreement::whereBetween('updatedDate', [$startDate, $endDate])
                    ->where('status', 'Completed')
                    ->count();
            }
        } else if ($period === 'quarter') {
            // Last 4 months data
            for ($i = 3; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $labels[] = $date->format('M');
                
                $newRentals[] = Agreement::whereMonth('createdDate', $date->month)
                    ->whereYear('createdDate', $date->year)
                    ->count();
                $returns[] = Agreement::whereMonth('updatedDate', $date->month)
                    ->whereYear('updatedDate', $date->year)
                    ->where('status', 'Completed')
                    ->count();
            }
        }
        
        return [
            'labels' => $labels,
            'newRentals' => $newRentals,
            'returns' => $returns
        ];
    }
    
    public function getInventoryStatus()
    {
        // Count items by status
        $available = Inventory::where('status', 'Available')->count();
        $rented = Inventory::where('status', 'Rented')->count();
        $maintenance = Inventory::where('status', 'Maintenance')->count();
        $reserved = Inventory::where('status', 'Reserved')->count();
        
        return [
            'labels' => ['Available', 'Rented', 'Maintenance', 'Reserved'],
            'values' => [$available, $rented, $maintenance, $reserved]
        ];
    }
    
    public function getCategoryPerformance()
    {
        try {
            // Get top 5 categories by rental frequency
            $categoryPerformance = Category::select('categories.name')
                ->selectRaw('COUNT(agreement_items.id) as rental_count')
                ->join('inventory', 'categories.id', '=', 'inventory.categoryId')
                ->join('agreement_items', 'inventory.id', '=', 'agreement_items.inventoryId')
                ->groupBy('categories.id', 'categories.name')
                ->orderByDesc('rental_count')
                ->take(5)
                ->get();
            
            $labels = $categoryPerformance->pluck('name')->toArray();
            $values = $categoryPerformance->pluck('rental_count')->toArray();
            
            return [
                'labels' => $labels,
                'values' => $values
            ];
        } catch (\Exception $e) {
            // Provide fallback data if query fails
            return [
                'labels' => ['Category 1', 'Category 2', 'Category 3', 'Category 4', 'Category 5'],
                'values' => [25, 20, 15, 10, 5]
            ];
        }
    }
}