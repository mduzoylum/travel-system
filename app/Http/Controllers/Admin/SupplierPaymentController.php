<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DDD\Modules\Contract\Models\SupplierPayment;
use App\DDD\Modules\Contract\Models\Hotel;
use Illuminate\Http\Request;

class SupplierPaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = SupplierPayment::with(['hotel', 'reservation']);
        
        // Filtreleme
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('hotel_id')) {
            $query->where('hotel_id', $request->hotel_id);
        }
        
        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('due_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('due_date', '<=', $request->date_to);
        }
        
        $payments = $query->orderBy('due_date', 'asc')->paginate(15);
        $hotels = Hotel::whereNull('supplier_id')->orderBy('name')->get();
        
        return view('admin.supplier-payments.index', compact('payments', 'hotels'));
    }

    public function updateStatus(Request $request, SupplierPayment $supplierPayment)
    {
        $request->validate([
            'status' => 'required|in:paid,cancelled',
            'notes' => 'nullable|string'
        ]);

        $supplierPayment->status = $request->status;
        
        if ($request->status === 'paid') {
            $supplierPayment->paid_at = now();
        }
        
        if ($request->has('notes')) {
            $supplierPayment->notes = $request->notes;
        }
        
        $supplierPayment->save();

        return redirect()->route('admin.supplier-payments.index')
            ->with('success', 'Ödeme durumu güncellendi.');
    }
}
