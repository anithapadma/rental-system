<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use App\Models\AgreementItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
use Carbon\Carbon;

class AgreementController extends Controller
{
    /**
     * Display a listing of agreements with optional filtering and pagination
     */
    public function index(Request $request)
    {
        $query = Agreement::query();
        
        // Handle search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%")
                  ->orWhere('customerEmail', 'like', "%{$search}%");
            });
        }
        
        // Handle status filter
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        // Handle sorting
        $sortBy = $request->sort_by ?? 'created_at';
        $sortDirection = $request->sort_direction ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);
        
        // Handle pagination
        $perPage = $request->per_page ?? 10;
        $agreements = $query->paginate($perPage);
        
        return response()->json($agreements);
    }

    /**
     * Store a newly created agreement
     */
    public function store(Request $request)
    {
        // Validate request
        $validatedData = $request->validate([
            'customer' => 'required|string|max:255',
            'customerEmail' => 'nullable|email|max:255',
            'customerPhone' => 'nullable|string|max:20',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'value' => 'required|numeric|min:0',
            'status' => 'required|in:Active,Completed,Expired,Cancelled',
            'paymentMethod' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
        ]);
        
        // Start transaction
        DB::beginTransaction();
        
        try {
            // Generate agreement ID
            $latestAgreement = Agreement::orderBy('id', 'desc')->first();
            $nextId = $latestAgreement ? (int)substr($latestAgreement->id, 4) + 1 : 1;
            $agreementId = 'AGR-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
            
            // Create agreement with current date as createdDate and updatedDate
            $today = Carbon::today()->format('Y-m-d');
            $agreement = Agreement::create([
                'id' => $agreementId,
                'customer' => $validatedData['customer'],
                'customerEmail' => $validatedData['customerEmail'] ?? null,
                'customerPhone' => $validatedData['customerPhone'] ?? null,
                'startDate' => $validatedData['startDate'],
                'endDate' => $validatedData['endDate'],
                'value' => $validatedData['value'],
                'status' => $validatedData['status'],
                'paymentMethod' => $validatedData['paymentMethod'] ?? 'Credit Card',
                'notes' => $validatedData['notes'] ?? null,
                'createdDate' => $today,
                'updatedDate' => $today,
            ]);
            
            // Create items
            foreach ($validatedData['items'] as $item) {
                AgreementItem::create([
                    'agreement_id' => $agreement->id,
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'rate' => $item['rate'],
                    'total' => $item['total'],
                ]);
            }
            
            // Commit transaction
            DB::commit();
            
            // Return created agreement with items
            $agreement->refresh();
            return response()->json($agreement, 201);
        } catch (\Exception $e) {
            // Roll back transaction on error
            DB::rollback();
            return response()->json(['message' => 'Failed to create agreement: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified agreement
     */
    public function show($id)
    {
        $agreement = Agreement::with('agreementItems')->find($id);
        
        if (!$agreement) {
            return response()->json(['message' => 'Agreement not found'], 404);
        }
        
        return response()->json($agreement);
    }

    /**
     * Update the specified agreement
     */
    public function update(Request $request, $id)
    {
        $agreement = Agreement::find($id);
        
        if (!$agreement) {
            return response()->json(['message' => 'Agreement not found'], 404);
        }
        
        // Validate request
        $validatedData = $request->validate([
            'customer' => 'required|string|max:255',
            'customerEmail' => 'nullable|email|max:255',
            'customerPhone' => 'nullable|string|max:20',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'value' => 'required|numeric|min:0',
            'status' => 'required|in:Active,Completed,Expired,Cancelled',
            'paymentMethod' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.total' => 'nullable|numeric|min:0',
        ]);
        
        // Start transaction
        DB::beginTransaction();
        
        try {
            // Update agreement
            $agreement->update([
                'customer' => $validatedData['customer'],
                'customerEmail' => $validatedData['customerEmail'] ?? null,
                'customerPhone' => $validatedData['customerPhone'] ?? null,
                'startDate' => $validatedData['startDate'],
                'endDate' => $validatedData['endDate'],
                'value' => $validatedData['value'],
                'status' => $validatedData['status'],
                'paymentMethod' => $validatedData['paymentMethod'] ?? $agreement->paymentMethod,
                'notes' => $validatedData['notes'] ?? $agreement->notes,
                'updatedDate' => Carbon::today()->format('Y-m-d'),
            ]);
            
            // Delete existing items and recreate
            $agreement->agreementItems()->delete();
            
            // Create new items
            foreach ($validatedData['items'] as $item) {
                AgreementItem::create([
                    'agreement_id' => $agreement->id,
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'rate' => $item['rate'],
                    'total' => isset($item['total']) ? $item['total'] : ($item['quantity'] * $item['rate']),
                ]);
            }
            
            // Commit transaction
            DB::commit();
            
            // Return updated agreement with items
            $agreement->refresh();
            return response()->json($agreement);
        } catch (\Exception $e) {
            // Roll back transaction on error
            DB::rollback();
            return response()->json(['message' => 'Failed to update agreement: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update agreement status
     */
    public function updateStatus(Request $request, $id)
    {
        $agreement = Agreement::find($id);
        
        if (!$agreement) {
            return response()->json(['message' => 'Agreement not found'], 404);
        }
        
        // Validate request
        $validatedData = $request->validate([
            'status' => 'required|in:Active,Completed,Expired,Cancelled',
        ]);
        
        try {
            // Update status
            $agreement->update([
                'status' => $validatedData['status'],
                'updatedDate' => Carbon::today()->format('Y-m-d'),
            ]);
            
            return response()->json($agreement);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update status: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified agreement
     */
    public function destroy($id)
    {
        $agreement = Agreement::find($id);
        
        if (!$agreement) {
            return response()->json(['message' => 'Agreement not found'], 404);
        }
        
        try {
            // Delete agreement (will cascade delete items due to foreign key)
            $agreement->delete();
            
            return response()->json(['message' => 'Agreement deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete agreement: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Generate a PDF for the specified agreement
     */
    public function generatePdf($id)
    {
        $agreement = Agreement::with('agreementItems')->find($id);
        
        if (!$agreement) {
            return response()->json(['message' => 'Agreement not found'], 404);
        }
        
        try {
            // Convert to PDF (this is a placeholder - you'll need to implement PDF generation)
            // For now, just return a placeholder PDF response
            $headers = [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="agreement-' . $agreement->id . '.pdf"'
            ];
            
            // Return a dummy PDF for now, you'll need to implement real PDF generation
            return response('PDF content for agreement ' . $agreement->id, 200, $headers);
            
            // Uncomment and implement if using a PDF library like Laravel-DomPDF
            /*
            $pdf = PDF::loadView('agreements.pdf', ['agreement' => $agreement]);
            return $pdf->stream('agreement-' . $agreement->id . '.pdf');
            */
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
        }
    }
}