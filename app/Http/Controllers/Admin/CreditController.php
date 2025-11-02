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
        $creditAccounts = CreditAccount::with(['firm', 'transactions' => function($q) {
                $q->orderBy('created_at', 'desc')->limit(1);
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('admin.credits.index', compact('creditAccounts'));
    }

    public function show(CreditAccount $creditAccount)
    {
        $creditAccount->load(['firm', 'transactions.performer' => function($query) {
            $query->orderBy('created_at', 'desc')->limit(50);
        }]);
        
        return view('admin.credits.show', compact('creditAccount'));
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

        $creditAccount = CreditAccount::create($data);

        // Açılış işlemini kaydet
        CreditTransaction::create([
            'credit_account_id' => $creditAccount->id,
            'type' => 'credit',
            'amount' => 0,
            'description' => 'Kredi hesabı oluşturuldu - Limit: ' . number_format($data['credit_limit'], 2) . ' ' . $data['currency'],
            'reference_type' => 'manual',
            'reference_id' => null,
            'balance_after' => 0,
            'performed_by' => auth()->id(),
        ]);

        return redirect()->route('admin.credits.index')
            ->with('success', 'Kredi hesabı başarıyla oluşturuldu.');
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
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:2000'
        ]);

        $data = $request->only(['firm_id','credit_limit','currency','balance','is_active','notes']);
        $data['is_active'] = (bool) ($request->input('is_active') === '1' || $request->input('is_active') === 1 || $request->boolean('is_active'));

        // Orijinal değerleri sakla (karşılaştırma için)
        $original = $creditAccount->getOriginal();

        $creditAccount->update($data);

        // Değişiklikleri işlem geçmişine yaz
        $changes = [];
        if (isset($original['is_active']) && $original['is_active'] != $creditAccount->is_active) {
            $changes[] = 'Durum: ' . ($original['is_active'] ? 'Aktif' : 'Pasif') . ' → ' . ($creditAccount->is_active ? 'Aktif' : 'Pasif');
        }
        if (isset($original['credit_limit']) && (float)$original['credit_limit'] != (float)$creditAccount->credit_limit) {
            $changes[] = 'Limit: ' . number_format((float)$original['credit_limit'], 2) . ' → ' . number_format((float)$creditAccount->credit_limit, 2) . ' ' . $creditAccount->currency;
        }
        if (isset($original['currency']) && $original['currency'] !== $creditAccount->currency) {
            $changes[] = 'Para Birimi: ' . ($original['currency'] ?? '-') . ' → ' . $creditAccount->currency;
        }

        // Bakiye değişimi ayrı bir işlem olarak kaydedilsin
        if (isset($original['balance']) && (float)$original['balance'] != (float)$creditAccount->balance) {
            $diff = (float)$creditAccount->balance - (float)$original['balance'];
            $type = $diff >= 0 ? 'credit' : 'debit';
            $amount = abs($diff);
            CreditTransaction::create([
                'credit_account_id' => $creditAccount->id,
                'type' => $type,
                'amount' => $amount,
                'description' => 'Bakiye düzeltme (düzenleme ekranı)',
                'reference_type' => 'manual',
                'reference_id' => null,
                'balance_after' => $creditAccount->balance,
                'performed_by' => auth()->id(),
            ]);
        }

        if (!empty($changes)) {
            $desc = 'Hesap ayar güncellemesi: ' . implode(' | ', $changes);
            // 0 tutarlı bilgi amaçlı işlem kaydı
            CreditTransaction::create([
                'credit_account_id' => $creditAccount->id,
                'type' => 'credit',
                'amount' => 0,
                'description' => $desc,
                'reference_type' => 'manual',
                'reference_id' => null,
                'balance_after' => $creditAccount->balance,
                'performed_by' => auth()->id(),
            ]);
        }

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
            $creditAccount->addCredit($money, $request->description, auth()->id());

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
            $creditAccount->useCredit($money, $request->description, auth()->id());

            return redirect()->route('admin.credits.show', $creditAccount)
                ->with('success', 'Kredi başarıyla kullanıldı.');
        } catch (\Exception $e) {
            return back()->with('error', 'Kredi kullanılırken hata oluştu: ' . $e->getMessage());
        }
    }

    public function transactions(CreditAccount $creditAccount)
    {
        $transactions = $creditAccount->transactions()
            ->with('performer')
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