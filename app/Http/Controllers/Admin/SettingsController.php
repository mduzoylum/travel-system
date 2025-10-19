<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DDD\Modules\Firm\Models\Firm;
use App\DDD\Modules\Supplier\Domain\Entities\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    /**
     * Ayarlar ana sayfası
     */
    public function index()
    {
        // Sadece admin kullanıcılar erişebilir
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Bu sayfaya erişim yetkiniz yok. Sadece admin kullanıcılar ayarlara erişebilir.');
        }

        $firms = Firm::where('is_active', true)->get();
        $suppliers = Supplier::with('group')->get();
        
        return view('admin.settings.index', compact('firms', 'suppliers'));
    }

    /**
     * Firma ayarlarını güncelle
     */
    public function updateFirmSettings(Request $request, Firm $firm)
    {
        // Sadece admin kullanıcılar erişebilir
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Bu işlemi yapma yetkiniz yok.');
        }

        $request->validate([
            'supplier_management_enabled' => 'nullable|in:on,1,true',
            'domestic_search_enabled' => 'nullable|in:on,1,true',
            'international_search_enabled' => 'nullable|in:on,1,true',
            'auto_approval_enabled' => 'nullable|in:on,1,true',
            'notification_email' => 'nullable|email',
            'max_credit_limit' => 'nullable|numeric|min:0',
            'default_currency' => 'required|string|max:3',
        ]);

        $settings = [
            'supplier_management_enabled' => $request->boolean('supplier_management_enabled'),
            'domestic_search_enabled' => $request->boolean('domestic_search_enabled'),
            'international_search_enabled' => $request->boolean('international_search_enabled'),
            'auto_approval_enabled' => $request->boolean('auto_approval_enabled'),
            'notification_email' => $request->notification_email,
            'max_credit_limit' => $request->max_credit_limit ?: null,
            'default_currency' => $request->default_currency,
        ];

        $firm->update(['settings' => $settings]);

        return redirect()->route('admin.settings')
            ->with('success', 'Firma ayarları başarıyla güncellendi.');
    }

    /**
     * Tedarikçi ayarlarını güncelle
     */
    public function updateSupplierSettings(Request $request, Supplier $supplier)
    {
        // Sadece admin kullanıcılar erişebilir
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Bu işlemi yapma yetkiniz yok.');
        }

        $request->validate([
            'is_active' => 'nullable|in:on,1,true',
            'auto_sync_enabled' => 'nullable|in:on,1,true',
            'sync_frequency' => 'nullable|integer|min:1|max:1440', // dakika cinsinden
            'notification_enabled' => 'nullable|in:on,1,true',
            'max_daily_bookings' => 'nullable|integer|min:1',
            'priority_level' => 'required|integer|min:1|max:5',
        ]);

        $supplier->update([
            'is_active' => $request->boolean('is_active'),
            'auto_sync_enabled' => $request->boolean('auto_sync_enabled'),
            'sync_frequency' => $request->sync_frequency,
            'notification_enabled' => $request->boolean('notification_enabled'),
            'max_daily_bookings' => $request->max_daily_bookings,
            'priority_level' => $request->priority_level,
        ]);

        return redirect()->route('admin.settings')
            ->with('success', 'Tedarikçi ayarları başarıyla güncellendi.');
    }

    /**
     * Genel sistem ayarlarını güncelle
     */
    public function updateSystemSettings(Request $request)
    {
        // Sadece admin kullanıcılar erişebilir
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Bu işlemi yapma yetkiniz yok.');
        }

        $request->validate([
            'system_name' => 'required|string|max:255',
            'system_email' => 'required|email',
            'maintenance_mode' => 'boolean',
            'debug_mode' => 'boolean',
            'backup_frequency' => 'required|in:daily,weekly,monthly',
            'session_timeout' => 'required|integer|min:15|max:480', // dakika cinsinden
            'max_file_upload_size' => 'required|integer|min:1|max:100', // MB cinsinden
        ]);

        // Bu ayarlar config dosyalarında veya veritabanında saklanabilir
        // Şimdilik session'da saklayalım (production'da veritabanı kullanılmalı)
        session([
            'system_settings' => [
                'system_name' => $request->system_name,
                'system_email' => $request->system_email,
                'maintenance_mode' => $request->boolean('maintenance_mode'),
                'debug_mode' => $request->boolean('debug_mode'),
                'backup_frequency' => $request->backup_frequency,
                'session_timeout' => $request->session_timeout,
                'max_file_upload_size' => $request->max_file_upload_size,
            ]
        ]);

        return redirect()->route('admin.settings')
            ->with('success', 'Sistem ayarları başarıyla güncellendi.');
    }
}