<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DDD\Modules\Credit\Domain\Entities\CreditAccount;
use App\DDD\Modules\Credit\Domain\Entities\CreditTransaction;
use App\DDD\Modules\Firm\Models\Firm;
use App\DDD\Modules\Credit\Domain\ValueObjects\Money;
use Illuminate\Http\Request;

class CreditController extends Controller
{
    public function index()
    {
        $creditAccounts = CreditAccount::with(['firm'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('admin.credits.index', compact('creditAccounts'));
    }

    public function create()
    {
        $firms = Firm::where('is_active', true)->get();
        return view('admin.credits.create', compact('firms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'firm_id' => 'required|exists:firms,id|unique:credit_accounts,firm_id',
            'credit_limit' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'is_active' => 'boolean'
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['balance'] = 0; // Başlangıç bakiyesi 0

        CreditAccount::create($data);

        return redirect()->route('admin.credits.index')
            ->with('success', 'Kredi hesabı başarıyla oluşturuldu.');
    }

    public function show(CreditAccount $creditAccount)
    {
        $creditAccount->load(['firm', 'transactions' => function($query) {
            $query->orderBy('created_at', 'desc')->limit(50);
        }]);
        
        return view('admin.credits.show', compact('creditAccount'));
    }

    public function edit(CreditAccount $creditAccount)
    {
        $firms = Firm::where('is_active', true)->get();
        return view('admin.credits.edit', compact('creditAccount', 'firms'));
    }

    public function update(Request $request, CreditAccount $creditAccount)
    {
        $request->validate([
            'firm_id' => 'required|exists:firms,id|unique:credit_accounts,firm_id,' . $creditAccount->id,
            'credit_limit' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'is_active' => 'boolean'
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        $creditAccount->update($data);

        return redirect()->route('admin.credits.index')
            ->with('success', 'Kredi hesabı başarıyla güncellendi.');
    }

    public function addCredit(Request $request, CreditAccount $creditAccount)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255'
        ]);

        try {
            $money = new Money($request->amount, $creditAccount->currency);
            $creditAccount->addCredit($money, $request->description);

            return redirect()->route('admin.credits.show', $creditAccount)
                ->with('success', 'Kredi başarıyla eklendi.');
        } catch (\Exception $e) {
            return back()->with('error', 'Kredi eklenirken hata oluştu: ' . $e->getMessage());
        }
    }

    public function useCredit(Request $request, CreditAccount $creditAccount)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255'
        ]);

        try {
            $money = new Money($request->amount, $creditAccount->currency);
            $creditAccount->useCredit($money, $request->description);

            return redirect()->route('admin.credits.show', $creditAccount)
                ->with('success', 'Kredi başarıyla kullanıldı.');
        } catch (\Exception $e) {
            return back()->with('error', 'Kredi kullanılırken hata oluştu: ' . $e->getMessage());
        }
    }

    public function transactions(CreditAccount $creditAccount)
    {
        $transactions = $creditAccount->transactions()
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('admin.credits.transactions', compact('creditAccount', 'transactions'));
    }

    public function destroy(CreditAccount $creditAccount)
    {
        if ($creditAccount->balance > 0) {
            return back()->with('error', 'Bakiyesi olan hesap silinemez.');
        }

        $creditAccount->delete();

        return redirect()->route('admin.credits.index')
            ->with('success', 'Kredi hesabı başarıyla silindi.');
    }
} 