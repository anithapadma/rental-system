<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    /**
     * Get all settings
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get settings from database or config
        $settings = DB::table('settings')->first();
        
        if (!$settings) {
            // Return default settings if none exist yet
            $settings = [
                'companyName' => 'Track New Services',
                'contactEmail' => 'info@tracknew.com',
                'phoneNumber' => '(555) 123-4567',
                'address' => '123 Main Street, City, State 12345',
                'defaultRentalPeriod' => 7,
                'lateFeePercent' => 5,
                'minimumRentalAmount' => 25,
                'securityDepositPercent' => 20,
            ];
        }
        
        return response()->json($settings);
    }
    
    /**
     * Update company information
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateCompanyInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'companyName' => 'required|string|max:255',
            'contactEmail' => 'required|email|max:255',
            'phoneNumber' => 'required|string|max:20',
            'address' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        // Update or create company settings
        $companyInfo = DB::table('settings')->updateOrInsert(
            ['id' => 1], // Use a single record for all settings
            [
                'companyName' => $request->companyName,
                'contactEmail' => $request->contactEmail,
                'phoneNumber' => $request->phoneNumber,
                'address' => $request->address,
                'updated_at' => now(),
            ]
        );
        
        return response()->json([
            'message' => 'Company information updated successfully',
            'data' => $request->all()
        ]);
    }
    
    /**
     * Update rental settings
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateRentalSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'defaultRentalPeriod' => 'required|integer|min:1',
            'lateFeePercent' => 'required|numeric|min:0|max:100',
            'minimumRentalAmount' => 'required|numeric|min:0',
            'securityDepositPercent' => 'required|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        // Update or create rental settings
        $rentalSettings = DB::table('settings')->updateOrInsert(
            ['id' => 1], // Use a single record for all settings
            [
                'defaultRentalPeriod' => $request->defaultRentalPeriod,
                'lateFeePercent' => $request->lateFeePercent,
                'minimumRentalAmount' => $request->minimumRentalAmount,
                'securityDepositPercent' => $request->securityDepositPercent,
                'updated_at' => now(),
            ]
        );
        
        return response()->json([
            'message' => 'Rental settings updated successfully',
            'data' => $request->all()
        ]);
    }
}