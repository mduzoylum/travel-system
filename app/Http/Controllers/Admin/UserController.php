<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\DDD\Modules\Firm\Models\Firm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('firmUser.firm')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.users.index', compact('users'));
    }


    public function create()
    {
        $firms = Firm::where('is_active', true)->orderBy('name')->get();
        return view('admin.users.create', compact('firms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,user,manager,supplier',
            'phone' => 'nullable|string|max:20',
            'firm_id' => 'nullable|exists:firms,id'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'is_active' => $request->has('is_active'),
            'email_verified_at' => $request->has('email_verified_at') ? now() : null
        ]);

        // Eğer firma seçilmişse firm user ilişkisini oluştur
        if ($request->firm_id) {
            $user->firmUser()->create([
                'firm_id' => $request->firm_id,
                'is_active' => true
            ]);
        }

        Log::info('User created', ['user_id' => $user->id, 'email' => $user->email]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Kullanıcı başarıyla oluşturuldu.');
    }

    public function show(User $user)
    {
        $user->load(['firmUser.firm']);
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $firms = Firm::where('is_active', true)->orderBy('name')->get();
        $isOwnProfile = auth()->user()->id === $user->id;
        return view('admin.users.edit', compact('user', 'firms', 'isOwnProfile'));
    }

    public function update(Request $request, User $user)
    {
        $isOwnProfile = auth()->user()->id === $user->id;
        
        $validationRules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20'
        ];
        
        // Sadece admin kullanıcılar rol ve firma değiştirebilir
        if (!$isOwnProfile) {
            $validationRules['role'] = 'required|in:admin,user,manager,supplier';
            $validationRules['firm_id'] = 'nullable|exists:firms,id';
        }
        
        $request->validate($validationRules);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone
        ];
        
        // Sadece admin kullanıcılar rol ve firma değiştirebilir
        if (!$isOwnProfile) {
            $data['role'] = $request->role;
            $data['is_active'] = $request->has('is_active');
            $data['email_verified_at'] = $request->has('email_verified_at') ? now() : null;
        }

        // Şifre sadece değiştirilmişse güncelle
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Firma ilişkisini güncelle (sadece admin kullanıcılar)
        if (!$isOwnProfile && $request->firm_id) {
            $user->firmUser()->updateOrCreate(
                ['user_id' => $user->id],
                ['firm_id' => $request->firm_id, 'is_active' => true]
            );
        } elseif (!$isOwnProfile && !$request->firm_id) {
            $user->firmUser()->delete();
        }

        Log::info('User updated', ['user_id' => $user->id, 'email' => $user->email]);

        // Profil sayfasından geliyorsa geri dön
        if ($isOwnProfile) {
            return redirect()->back()
                ->with('success', 'Profiliniz başarıyla güncellendi.');
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Kullanıcı başarıyla güncellendi.');
    }

    public function destroy(User $user)
    {
        // Kendini silmeye çalışıyorsa engelle
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Kendi hesabınızı silemezsiniz.');
        }

        $userEmail = $user->email;
        $user->delete();

        Log::info('User deleted', ['user_email' => $userEmail]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Kullanıcı başarıyla silindi.');
    }

    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        
        $status = $user->is_active ? 'aktif' : 'pasif';
        
        return response()->json([
            'success' => true,
            'message' => "Kullanıcı {$status} hale getirildi.",
            'is_active' => $user->is_active
        ]);
    }

    public function resendVerification(User $user)
    {
        if ($user->email_verified_at) {
            return back()->with('error', 'Bu kullanıcının e-postası zaten doğrulanmış.');
        }

        // TODO: E-posta doğrulama e-postası gönder
        $user->update(['email_verified_at' => now()]);

        return back()->with('success', 'E-posta doğrulama başarıyla tamamlandı.');
    }
}
