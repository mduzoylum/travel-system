<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DDD\Modules\Approval\Models\ApprovalScenario;
use App\DDD\Modules\Approval\Models\ApprovalRule;
use App\DDD\Modules\Approval\Models\ApprovalApprover;
use App\DDD\Modules\Approval\Models\ApprovalRequest;
use App\DDD\Modules\Firm\Models\Firm;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ApprovalController extends Controller
{
    public function index()
    {
        $scenarios = ApprovalScenario::with(['firm', 'rules', 'approvers.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('admin.approvals.index', compact('scenarios'));
    }

    public function create()
    {
        $firms = Firm::where('is_active', true)->get();
        $users = User::where('is_active', true)->get();
        return view('admin.approvals.create', compact('firms', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'firm_id' => 'required|exists:firms,id',
            'approval_type' => 'required|in:single,multi_step,parallel',
            'max_approval_days' => 'required|integer|min:1|max:30',
            'require_all_approvers' => 'boolean',
            'notification_settings' => 'nullable|array',
            'approvers' => 'required|array|min:1',
            'approvers.*.user_id' => 'required|exists:users,id',
            'approvers.*.step_order' => 'required|integer|min:1',
            'approvers.*.approval_type' => 'required|in:required,optional,backup',
            'approvers.*.can_override' => 'boolean'
        ]);

        $scenario = ApprovalScenario::create([
            'name' => $request->name,
            'description' => $request->description,
            'firm_id' => $request->firm_id,
            'approval_type' => $request->approval_type,
            'max_approval_days' => $request->max_approval_days,
            'require_all_approvers' => $request->has('require_all_approvers'),
            'notification_settings' => $request->notification_settings,
            'is_active' => true
        ]);

        // Onaylayıcıları ekle
        foreach ($request->approvers as $approverData) {
            $scenario->approvers()->create([
                'user_id' => $approverData['user_id'],
                'step_order' => $approverData['step_order'],
                'approval_type' => $approverData['approval_type'],
                'can_override' => isset($approverData['can_override']),
                'is_active' => true
            ]);
        }

        return redirect()->route('admin.approvals.show', $scenario)
            ->with('success', 'Onay senaryosu başarıyla oluşturuldu.');
    }

    public function show(ApprovalScenario $scenario)
    {
        $scenario->load(['firm', 'rules', 'approvers.user', 'requests.requestedBy', 'requests.approvedBy']);
        return view('admin.approvals.show', compact('scenario'));
    }

    public function edit(ApprovalScenario $scenario)
    {
        $firms = Firm::where('is_active', true)->get();
        $users = User::where('is_active', true)->get();
        return view('admin.approvals.edit', compact('scenario', 'firms', 'users'));
    }

    public function update(Request $request, ApprovalScenario $scenario)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'firm_id' => 'required|exists:firms,id',
            'approval_type' => 'required|in:single,multi_step,parallel',
            'max_approval_days' => 'required|integer|min:1|max:30',
            'require_all_approvers' => 'boolean',
            'notification_settings' => 'nullable|array'
        ]);

        $scenario->update([
            'name' => $request->name,
            'description' => $request->description,
            'firm_id' => $request->firm_id,
            'approval_type' => $request->approval_type,
            'max_approval_days' => $request->max_approval_days,
            'require_all_approvers' => $request->has('require_all_approvers'),
            'notification_settings' => $request->notification_settings
        ]);

        return redirect()->route('admin.approvals.show', $scenario)
            ->with('success', 'Onay senaryosu başarıyla güncellendi.');
    }

    public function destroy(ApprovalScenario $scenario)
    {
        $scenario->delete();
        return redirect()->route('admin.approvals.index')
            ->with('success', 'Onay senaryosu başarıyla silindi.');
    }

    // Onay kuralları yönetimi
    public function rules(ApprovalScenario $scenario)
    {
        return view('admin.approvals.rules.index', compact('scenario'));
    }

    public function storeRule(Request $request, ApprovalScenario $scenario)
    {
        $request->validate([
            'rule_type' => 'required|in:price_range,destination,duration,amount,custom',
            'field_name' => 'required|string|max:100',
            'operator' => 'required|in:equals,not_equals,greater_than,less_than,between,in,not_in',
            'value' => 'required',
            'priority' => 'required|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $scenario->rules()->create([
            'rule_type' => $request->rule_type,
            'field_name' => $request->field_name,
            'operator' => $request->operator,
            'value' => $request->value,
            'priority' => $request->priority,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.approvals.rules', $scenario)
            ->with('success', 'Onay kuralı başarıyla eklendi.');
    }

    // Onay istekleri yönetimi
    public function requests()
    {
        $requests = ApprovalRequest::with(['scenario.firm', 'requestedBy', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('admin.approvals.requests.index', compact('requests'));
    }

    public function approveRequest(Request $request, ApprovalRequest $approvalRequest)
    {
        $request->validate([
            'notes' => 'nullable|string'
        ]);

        $approvalRequest->updateStatus('approved', auth()->user(), $request->notes);

        return redirect()->back()
            ->with('success', 'Onay isteği başarıyla onaylandı.');
    }

    public function rejectRequest(Request $request, ApprovalRequest $approvalRequest)
    {
        $request->validate([
            'reason' => 'required|string'
        ]);

        $approvalRequest->updateStatus('rejected', auth()->user(), $request->reason);

        return redirect()->back()
            ->with('success', 'Onay isteği reddedildi.');
    }
}
