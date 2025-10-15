<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DDD\Modules\Profit\Models\ProfitRule;
use App\DDD\Modules\Profit\Models\ServiceFee;
use App\DDD\Modules\Profit\Models\ProfitCalculation;
use App\DDD\Modules\Profit\Services\ProfitCalculationService;
use App\DDD\Modules\Firm\Models\Firm;
use App\DDD\Modules\Supplier\Domain\Entities\Supplier;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProfitController extends Controller
{
    protected $profitService;

    public function __construct(ProfitCalculationService $profitService)
    {
        $this->profitService = $profitService;
    }

    public function index()
    {
        $profitRules = ProfitRule::with(['firm', 'supplier'])
            ->orderBy('priority', 'desc')
            ->paginate(15);
        return view('admin.profits.index', compact('profitRules'));
    }

    public function create()
    {
        $firms = Firm::where('is_active', true)->get();
        $suppliers = Supplier::where('is_active', true)->get();
        return view('admin.profits.create', compact('firms', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'firm_id' => 'nullable|exists:firms,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'destination' => 'nullable|string|max:100',
            'trip_type' => 'required|in:domestic,international',
            'travel_type' => 'required|in:one_way,round_trip',
            'fee_type' => 'required|in:percentage,fixed,tiered',
            'fee_value' => 'required|numeric|min:0',
            'min_fee' => 'nullable|numeric|min:0',
            'max_fee' => 'nullable|numeric|min:0',
            'tier_rules' => 'nullable|array',
            'priority' => 'required|integer|min:0'
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        ProfitRule::create($data);

        return redirect()->route('admin.profits.index')
            ->with('success', 'Kar kuralı başarıyla oluşturuldu.');
    }

    public function show(ProfitRule $profitRule)
    {
        $profitRule->load(['firm', 'supplier']);
        return view('admin.profits.show', compact('profitRule'));
    }

    public function edit(ProfitRule $profitRule)
    {
        $firms = Firm::where('is_active', true)->get();
        $suppliers = Supplier::where('is_active', true)->get();
        return view('admin.profits.edit', compact('profitRule', 'firms', 'suppliers'));
    }

    public function update(Request $request, ProfitRule $profitRule)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'firm_id' => 'nullable|exists:firms,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'destination' => 'nullable|string|max:100',
            'trip_type' => 'required|in:domestic,international',
            'travel_type' => 'required|in:one_way,round_trip',
            'fee_type' => 'required|in:percentage,fixed,tiered',
            'fee_value' => 'required|numeric|min:0',
            'min_fee' => 'nullable|numeric|min:0',
            'max_fee' => 'nullable|numeric|min:0',
            'tier_rules' => 'nullable|array',
            'priority' => 'required|integer|min:0'
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        $profitRule->update($data);

        return redirect()->route('admin.profits.show', $profitRule)
            ->with('success', 'Kar kuralı başarıyla güncellendi.');
    }

    public function destroy(ProfitRule $profitRule)
    {
        $profitRule->delete();
        return redirect()->route('admin.profits.index')
            ->with('success', 'Kar kuralı başarıyla silindi.');
    }

    // Servis ücretleri yönetimi
    public function serviceFees()
    {
        $serviceFees = ServiceFee::with('firm')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('admin.profits.service-fees.index', compact('serviceFees'));
    }

    public function createServiceFee()
    {
        $firms = Firm::where('is_active', true)->get();
        return view('admin.profits.service-fees.create', compact('firms'));
    }

    public function storeServiceFee(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'firm_id' => 'nullable|exists:firms,id',
            'service_type' => 'required|in:reservation,cancellation,modification,booking',
            'fee_type' => 'required|in:percentage,fixed',
            'fee_value' => 'required|numeric|min:0',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
            'currency' => 'required|string|max:3',
            'priority' => 'nullable|integer|min:0'
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['is_mandatory'] = $request->has('is_mandatory');

        ServiceFee::create($data);

        return redirect()->route('admin.profits.service-fees')
            ->with('success', 'Servis ücreti başarıyla oluşturuldu.');
    }

    public function showServiceFee(ServiceFee $serviceFee)
    {
        $serviceFee->load('firm');
        return view('admin.profits.service-fees.show', compact('serviceFee'));
    }

    public function editServiceFee(ServiceFee $serviceFee)
    {
        $firms = Firm::where('is_active', true)->get();
        return view('admin.profits.service-fees.edit', compact('serviceFee', 'firms'));
    }

    public function updateServiceFee(Request $request, ServiceFee $serviceFee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'firm_id' => 'nullable|exists:firms,id',
            'service_type' => 'required|in:reservation,cancellation,modification,booking',
            'fee_type' => 'required|in:percentage,fixed',
            'fee_value' => 'required|numeric|min:0',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
            'currency' => 'required|string|max:3',
            'priority' => 'nullable|integer|min:0'
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['is_mandatory'] = $request->has('is_mandatory');

        $serviceFee->update($data);

        return redirect()->route('admin.profits.service-fees.show', $serviceFee)
            ->with('success', 'Servis ücreti başarıyla güncellendi.');
    }

    public function destroyServiceFee(ServiceFee $serviceFee)
    {
        $serviceFee->delete();
        return redirect()->route('admin.profits.service-fees')
            ->with('success', 'Servis ücreti başarıyla silindi.');
    }

    // Kar hesaplamaları
    public function calculations()
    {
        $calculations = ProfitCalculation::with(['firm', 'supplier', 'contract'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('admin.profits.calculations.index', compact('calculations'));
    }

    public function showCalculation(ProfitCalculation $calculation)
    {
        $calculation->load(['firm', 'supplier', 'contract']);
        return view('admin.profits.calculations.show', compact('calculation'));
    }

    public function calculate(Request $request)
    {
        $validationRules = [
            'firm_id' => 'required|exists:firms,id',
            'currency' => 'required|string|max:3',
            'service_type' => 'required|in:reservation,cancellation,modification,booking',
            'trip_type' => 'required|in:domestic,international',
            'travel_type' => 'required|in:one_way,round_trip',
            'calculation_mode' => 'required|in:estimated,existing'
        ];

        // Mod'a göre validation kuralları
        if ($request->calculation_mode === 'estimated') {
            // Ön görülen fiyat modu
            $validationRules['product_type'] = 'required|in:hotel,flight,car,activity,transfer';
            $validationRules['base_price'] = 'required|numeric|min:0';
        } else {
            // Mevcut ürün modu
            $validationRules['destination'] = 'required|string';
            $validationRules['hotel_id'] = 'required|exists:hotels,id';
            $validationRules['contract_id'] = 'nullable|exists:contracts,id';
            $validationRules['check_in'] = 'required|date';
            $validationRules['check_out'] = 'required|date|after:check_in';
            $validationRules['person_count'] = 'required|integer|min:1';
        }

        $request->validate($validationRules, [
            'firm_id.required' => 'Firma seçimi zorunludur.',
            'firm_id.exists' => 'Seçilen firma bulunamadı.',
            'product_type.required' => 'Ürün tipi seçimi zorunludur.',
            'product_type.in' => 'Geçersiz ürün tipi.',
            'base_price.required' => 'Temel fiyat zorunludur.',
            'base_price.numeric' => 'Temel fiyat sayı olmalıdır.',
            'base_price.min' => 'Temel fiyat 0\'dan küçük olamaz.',
            'destination.required' => 'Destinasyon seçimi zorunludur.',
            'hotel_id.required' => 'Otel seçimi zorunludur.',
            'hotel_id.exists' => 'Seçilen otel bulunamadı.',
            'check_in.required' => 'Giriş tarihi zorunludur.',
            'check_in.date' => 'Geçerli bir giriş tarihi giriniz.',
            'check_out.required' => 'Çıkış tarihi zorunludur.',
            'check_out.date' => 'Geçerli bir çıkış tarihi giriniz.',
            'check_out.after' => 'Çıkış tarihi giriş tarihinden sonra olmalıdır.',
            'person_count.required' => 'Kişi sayısı zorunludur.',
            'person_count.integer' => 'Kişi sayısı tam sayı olmalıdır.',
            'person_count.min' => 'Kişi sayısı en az 1 olmalıdır.'
        ]);

        $calculation = $this->profitService->calculateProfit($request->all());
        $calculation->save();

        return redirect()->route('admin.profits.calculations')
            ->with('success', 'Kar hesaplaması başarıyla oluşturuldu.')
            ->with('calculation_id', $calculation->id);
    }

    // Kar raporları
    public function reports()
    {
        $firms = Firm::where('is_active', true)->get();
        return view('admin.profits.reports.index', compact('firms'));
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'firm_id' => 'required|exists:firms,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date'
        ]);

        $report = $this->profitService->generateProfitReport(
            $request->firm_id,
            $request->start_date,
            $request->end_date
        );

        return view('admin.profits.reports.show', compact('report'));
    }
}
