<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    public function show()
    {
        $settings = SystemSetting::firstOrCreate([], [
            'store_name' => 'Waraqh',
            'contact_email' => 'admin@waraqh.com',
            'tax_rate' => 15,
            'default_currency' => 'SAR',
            'maintenance_mode' => false,
        ]);
        
        return response()->json([
            'data' => $settings
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'store_name' => 'nullable|string|max:255',
            'store_logo' => 'nullable|string|url',
            'contact_email' => 'nullable|string|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'tax_rate' => 'nullable|numeric|min:0',
            'default_currency' => 'nullable|string|max:10',
            'maintenance_mode' => 'nullable|boolean',
            'maintenance_message' => 'nullable|string',
        ]);
        
        $settings = SystemSetting::firstOrCreate([], [
            'store_name' => 'Waraqh',
            'contact_email' => 'admin@waraqh.com',
            'tax_rate' => 15,
            'default_currency' => 'SAR',
            'maintenance_mode' => false,
        ]);
        
        $settings->update($validated);
        
        return response()->json([
            'data' => $settings
        ]);
    }
}
