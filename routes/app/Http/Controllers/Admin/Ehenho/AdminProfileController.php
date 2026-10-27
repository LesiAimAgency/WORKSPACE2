<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Ehenho;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Themes\Ehenho\AuthController;
use App\Models\Ehenho\Conversation;
use App\Models\Ehenho\Message;
use App\Models\Ehenho\Profile;
use App\Models\Ehenho\Province;
use App\Models\Ehenho\SocialConnection;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminProfileController extends Controller
{
    protected function resolveProject(Request $request, string $projectCode): ?Project
    {
        return $request->attributes->get('project')
            ?? Project::where('code', $projectCode)
                ->orWhere('code', 'DA010')
                ->orWhere('code', 'ehenho')
                ->orWhere('external_domain', 'ehenho.local')
                ->first();
    }

    public function index(Request $request, string $projectCode): View
    {
        $project = $this->resolveProject($request, $projectCode);

        $query = Profile::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('display_name', 'like', "%{$search}%")
                    ->orWhere('headline', 'like', "%{$search}%")
                    ->orWhere('province_name', 'like', "%{$search}%")
                    ->orWhere('district_name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($gender = $request->input('gender')) {
            $query->where('gender', $gender);
        }

        if ($province = $request->input('province')) {
            $query->where(function ($q) use ($province) {
                $q->where('province_name', 'like', "%{$province}%")
                    ->orWhere('province_id', $province);
            });
        }

        if ($country = $request->input('country')) {
            if ($country === 'nhat') {
                $query->where(function ($q) {
                    $q->where('province_id', 68)
                        ->orWhere('province_name', 'like', '%Nhật%')
                        ->orWhere('province_name', 'like', '%Japan%')
                        ->orWhere('district_name', 'like', '%Tokyo%')
                        ->orWhere('district_name', 'like', '%Osaka%')
                        ->orWhere('district_name', 'like', '%Nagoya%')
                        ->orWhere('about_me', 'like', '%Nhật%');
                });
            } elseif ($country === 'vietnam') {
                $query->where(function ($q) {
                    $q->whereNull('province_name')
                        ->orWhere(function ($sub) {
                            $sub->where('province_name', 'not like', '%Nhật%')
                                ->where('province_name', 'not like', '%Japan%')
                                ->where('province_name', 'not like', '%Mỹ%')
                                ->where('province_name', 'not like', '%Hoa Kỳ%')
                                ->where('province_name', 'not like', '%USA%')
                                ->where('province_name', 'not like', '%Úc%')
                                ->where('province_name', 'not like', '%Canada%')
                                ->where('province_name', 'not like', '%Đức%');
                        });
                });
            } elseif ($country === 'overseas') {
                $query->where(function ($q) {
                    $q->where('province_id', 68)
                        ->orWhere('province_name', 'like', '%Nhật%')
                        ->orWhere('province_name', 'like', '%Japan%')
                        ->orWhere('province_name', 'like', '%Mỹ%')
                        ->orWhere('province_name', 'like', '%Hoa Kỳ%')
                        ->orWhere('province_name', 'like', '%Úc%')
                        ->orWhere('province_name', 'like', '%Canada%')
                        ->orWhere('province_name', 'like', '%Đức%');
                });
            } elseif (in_array($country, ['my', 'uc', 'canada', 'duc'], true)) {
                $countryMap = ['my' => 'Mỹ', 'uc' => 'Úc', 'canada' => 'Canada', 'duc' => 'Đức'];
                $kw = $countryMap[$country];
                $query->where(function ($q) use ($kw) {
                    $q->where('province_name', 'like', "%{$kw}%")
                        ->orWhere('about_me', 'like', "%{$kw}%");
                });
            }
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $profiles = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total' => Profile::count(),
            'male' => Profile::where('gender', 'male')->count(),
            'female' => Profile::where('gender', 'female')->count(),
            'blocked' => Profile::where('status', 'blocked')->count(),
        ];

        $provinces = Province::orderBy('name')->get();

        return view('themes.ehenho.admin.profiles.index', compact(
            'profiles',
            'stats',
            'provinces',
            'project',
            'projectCode'
        ));
    }

    public function create(Request $request, string $projectCode): View
    {
        $project = $this->resolveProject($request, $projectCode);
        $provinces = Province::orderBy('name')->get();

        return view('themes.ehenho.admin.profiles.create', [
            'project' => $project,
            'projectCode' => $projectCode,
            'provinces' => $provinces,
            'maritalStatusMap' => AuthController::MARITAL_STATUS_MAP,
            'lookForMap' => AuthController::LOOK_FOR_MAP,
            'educationMap' => AuthController::EDUCATION_MAP,
            'appearanceMap' => AuthController::APPEARANCE_MAP,
            'interestMap' => AuthController::INTEREST_MAP,
            'personalityMap' => AuthController::PERSONALITY_MAP,
            'wayOfLifeMap' => AuthController::WAY_OF_LIFE_MAP,
            'mostValuedMap' => AuthController::MOST_VALUED_MAP,
            'occupationMap' => AuthController::OCCUPATION_MAP,
            'religionMap' => AuthController::RELIGION_MAP,
            'smokingMap' => AuthController::SMOKING_MAP,
            'drinkingMap' => AuthController::DRINKING_MAP,
            'childrenMap' => AuthController::CHILDREN_MAP,
            'overseasMap' => AuthController::OVERSEAS_MAP,
        ]);
    }

    public function store(Request $request, string $projectCode): RedirectResponse
    {
        $project = $this->resolveProject($request, $projectCode);

        $validated = $request->validate([
            // Account info
            'email' => 'required|email|max:191|unique:users,email',
            'password' => 'required|string|min:6',
            'username' => 'nullable|string|max:100|unique:users,username',

            // Profile identity & basic info
            'name' => 'required|string|min:2|max:150',
            'display_name' => 'nullable|string|max:150',
            'slug' => 'nullable|string|max:150|unique:profiles,slug',
            'gender' => 'required|in:male,female,other',
            'age' => 'nullable|integer|between:18,99',
            'birthday' => 'nullable|date',
            'dob_day' => 'nullable|integer|between:1,31',
            'dob_month' => 'nullable|integer|between:1,12',
            'dob_year' => 'nullable|integer|between:1940,2010',
            'marital_status' => 'nullable|string|max:100',
            'look_for' => 'nullable|string|max:100',
            'target_type' => 'nullable|string|max:100',
            'height' => 'nullable|string|max:50',
            'weight' => 'nullable|string|max:50',
            'education' => 'nullable|string|max:100',

            // Location
            'province' => 'nullable|string',
            'province_id' => 'nullable',
            'province_name' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:150',
            'district_name' => 'nullable|string|max:150',
            'is_in_japan' => 'nullable',

            // Content
            'headline' => 'nullable|string|max:255',
            'i_am' => 'nullable|string|max:10000',
            'about_me' => 'nullable|string|max:10000',
            'my_match' => 'nullable|string|max:10000',
            'looking_for' => 'nullable|string|max:10000',

            // Detailed characteristics (codes or direct strings)
            'appearance2_0' => 'nullable|string',
            'body_type' => 'nullable|string|max:100',
            'interest2_0' => 'nullable|string',
            'interests' => 'nullable|string|max:2000',
            'personality2_0' => 'nullable|string',
            'personality' => 'nullable|string|max:150',
            'way_of_life' => 'nullable|string',
            'lifestyle' => 'nullable|string|max:150',
            'most_valued' => 'nullable|string',
            'precious' => 'nullable|string|max:150',
            'occupation2_0' => 'nullable|string',
            'occupation' => 'nullable|string|max:150',
            'religion2_0' => 'nullable|string',
            'religion' => 'nullable|string|max:100',
            'smoking2_0' => 'nullable|string',
            'smoking' => 'nullable|string|max:100',
            'drinking2_0' => 'nullable|string',
            'drinking' => 'nullable|string|max:100',
            'children2_0' => 'nullable|string',
            'children' => 'nullable|string|max:100',
            'privacy_option' => 'nullable|string|max:255',

            // Admin fields & Avatar
            'status' => 'required|in:active,pending,blocked',
            'is_featured' => 'nullable',
            'avatar_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'avatar_url' => 'nullable|string|max:500',
        ]);

        $displayName = trim($validated['display_name'] ?? $validated['name']);

        // Birthday & Age resolution
        $birthday = $validated['birthday'] ?? null;
        $age = $validated['age'] ?? null;

        if (empty($birthday) && ! empty($validated['dob_year'])) {
            $year = (int) $validated['dob_year'];
            $month = (int) ($validated['dob_month'] ?? 1);
            $day = (int) ($validated['dob_day'] ?? 1);
            try {
                $birthCarbon = Carbon::createFromDate($year, $month, $day);
                $age = $birthCarbon->age;
                $birthday = $birthCarbon->toDateString();
            } catch (\Throwable) {
                // Keep default age
            }
        } elseif (! empty($birthday) && empty($age)) {
            try {
                $birthCarbon = Carbon::parse($birthday);
                $age = $birthCarbon->age;
            } catch (\Throwable) {
            }
        }

        if (empty($age) || $age < 18) {
            $age = 24;
        }

        // Location resolution
        $provinceId = null;
        $provinceName = $validated['province_name'] ?? null;
        $districtName = $validated['district_name'] ?? $validated['district'] ?? null;

        if ($request->boolean('is_in_japan')) {
            $japanProv = Province::where('name', 'like', '%Nhật%')->first();
            $provinceId = $japanProv?->id ?? 68;
            $provinceName = $japanProv?->name ?? 'Nhật Bản (Japan)';
            if (empty($districtName) || $districtName === 'Chưa cập nhật') {
                $districtName = 'Tokyo, Nhật Bản';
            }
        } else {
            $provinceInput = (string) ($request->input('province') ?: $request->input('province_id', ''));
            if (isset(AuthController::OVERSEAS_MAP[$provinceInput])) {
                $provinceName = AuthController::OVERSEAS_MAP[$provinceInput];
                $provinceId = null;
                $districtName = null;
            } elseif (isset(AuthController::PROVINCE_SLUG_MAP[$provinceInput])) {
                $provinceName = AuthController::PROVINCE_SLUG_MAP[$provinceInput];
                $p = Province::where('name', 'like', "%{$provinceName}%")->first();
                $provinceId = $p?->id;
            } elseif (is_numeric($provinceInput)) {
                $p = Province::find((int) $provinceInput);
                if ($p) {
                    $provinceId = $p->id;
                    $provinceName = preg_replace('/^(Tỉnh|Thành phố)\s+/u', '', $p->name);
                }
            } elseif (! empty($provinceInput)) {
                $cleanInput = preg_replace('/^(Tỉnh|Thành phố)\s+/u', '', $provinceInput);
                $p = Province::where('name', 'like', "%{$cleanInput}%")->first();
                if ($p) {
                    $provinceId = $p->id;
                    $provinceName = preg_replace('/^(Tỉnh|Thành phố)\s+/u', '', $p->name);
                } else {
                    $provinceName = $cleanInput;
                }
            }
        }

        if (! empty($districtName)) {
            $districtName = Profile::resolveDistrictCode($districtName) ?: $districtName;
        }

        // Map values if codes used
        $targetType = $validated['target_type'] ?? (AuthController::LOOK_FOR_MAP[$request->input('look_for')] ?? $request->input('look_for') ?? 'Tìm người yêu lâu dài');
        $maritalStatus = AuthController::MARITAL_STATUS_MAP[$request->input('marital_status')] ?? $request->input('marital_status') ?? 'Độc thân';
        $education = $validated['education'] ?? (AuthController::EDUCATION_MAP[$request->input('education')] ?? $request->input('education'));
        $bodyType = $validated['body_type'] ?? (AuthController::APPEARANCE_MAP[$request->input('appearance2_0')] ?? $request->input('appearance2_0'));
        $interests = $validated['interests'] ?? (AuthController::INTEREST_MAP[$request->input('interest2_0')] ?? $request->input('interest2_0'));
        $personality = $validated['personality'] ?? (AuthController::PERSONALITY_MAP[$request->input('personality2_0')] ?? $request->input('personality2_0'));
        $lifestyle = $validated['lifestyle'] ?? (AuthController::WAY_OF_LIFE_MAP[$request->input('way_of_life')] ?? $request->input('way_of_life'));
        $precious = $validated['precious'] ?? (AuthController::MOST_VALUED_MAP[$request->input('most_valued')] ?? $request->input('most_valued'));
        $occupation = $validated['occupation'] ?? (AuthController::OCCUPATION_MAP[$request->input('occupation2_0')] ?? $request->input('occupation2_0'));
        $religion = $validated['religion'] ?? (AuthController::RELIGION_MAP[$request->input('religion2_0')] ?? $request->input('religion2_0'));
        $smoking = $validated['smoking'] ?? (AuthController::SMOKING_MAP[$request->input('smoking2_0')] ?? $request->input('smoking2_0'));
        $drinking = $validated['drinking'] ?? (AuthController::DRINKING_MAP[$request->input('drinking2_0')] ?? $request->input('drinking2_0'));
        $children = $validated['children'] ?? (AuthController::CHILDREN_MAP[$request->input('children2_0')] ?? $request->input('children2_0'));

        $aboutMe = $validated['about_me'] ?? $request->input('i_am');
        $lookingFor = $validated['looking_for'] ?? ($request->input('my_match') ?: $targetType);

        // Avatar handling
        $avatarUrl = $validated['avatar_url'] ?? null;
        if ($request->hasFile('avatar_file')) {
            $file = $request->file('avatar_file');
            $destinationPath = public_path('themes/ehenho/images/avatars');
            if (! File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }
            $filename = 'avatar_admin_'.time().'_'.Str::random(6).'.'.$file->getClientOriginalExtension();
            $file->move($destinationPath, $filename);
            $avatarUrl = 'themes/ehenho/images/avatars/'.$filename;
        }

        // Database transaction for User + Profile
        [$user, $profile] = DB::transaction(function () use (
            $validated,
            $displayName,
            $birthday,
            $age,
            $provinceId,
            $provinceName,
            $districtName,
            $targetType,
            $maritalStatus,
            $education,
            $bodyType,
            $interests,
            $personality,
            $lifestyle,
            $precious,
            $occupation,
            $religion,
            $smoking,
            $drinking,
            $children,
            $aboutMe,
            $lookingFor,
            $avatarUrl,
            $project,
            $request
        ) {
            $username = $validated['username'] ?? null;
            if (empty($username)) {
                $baseUsername = Str::slug($displayName, '_');
                $candidate = $baseUsername ?: 'user_'.time();
                $counter = 1;
                while (User::where('username', $candidate)->exists()) {
                    $candidate = $baseUsername.'_'.$counter++;
                }
                $username = $candidate;
            }

            $user = User::create([
                'name' => $displayName,
                'email' => $validated['email'],
                'username' => $username,
                'password' => Hash::make($validated['password']),
                'role' => 'user',
                'level' => 2,
                'status' => $validated['status'] === 'blocked' ? 0 : 1,
                'avatar' => $avatarUrl,
                'tenant_id' => $project?->tenant_id,
                'project_ids' => $project ? [$project->id] : null,
            ]);

            $slug = ! empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($displayName).'-'.$user->id;
            if (Profile::where('slug', $slug)->exists()) {
                $slug .= '-'.Str::random(4);
            }

            $profile = Profile::create([
                'project_id' => $project?->id,
                'user_id' => $user->id,
                'display_name' => $displayName,
                'slug' => $slug,
                'headline' => $validated['headline'] ?? null,
                'target_type' => $targetType,
                'gender' => $validated['gender'],
                'birthday' => $birthday,
                'age' => $age,
                'province_id' => $provinceId,
                'province_name' => $provinceName,
                'district_name' => $districtName,
                'marital_status' => $maritalStatus,
                'occupation' => $occupation,
                'height' => $validated['height'] ?? null,
                'weight' => $validated['weight'] ?? null,
                'education' => $education,
                'body_type' => $bodyType,
                'about_me' => $aboutMe,
                'looking_for' => $lookingFor,
                'interests' => $interests,
                'personality' => $personality,
                'lifestyle' => $lifestyle,
                'precious' => $precious,
                'religion' => $religion,
                'smoking' => $smoking,
                'drinking' => $drinking,
                'children' => $children,
                'privacy_option' => $validated['privacy_option'] ?? null,
                'avatar_url' => $avatarUrl,
                'status' => $validated['status'] ?? 'active',
                'is_featured' => $request->boolean('is_featured'),
                'is_online' => true,
                'last_active_at' => now(),
            ]);

            return [$user, $profile];
        });

        return redirect()->route('project.admin.ehenho.profiles.index', $projectCode)
            ->with('success', "Đã thêm thành viên mới {$profile->display_name} thành công!");
    }

    public function edit(Request $request, string $projectCode, int $id): View
    {
        $project = $this->resolveProject($request, $projectCode);
        $profile = Profile::with('user')->findOrFail($id);
        $provinces = Province::orderBy('name')->get();

        return view('themes.ehenho.admin.profiles.edit', compact(
            'profile',
            'provinces',
            'project',
            'projectCode'
        ));
    }

    public function update(Request $request, string $projectCode, int $id): RedirectResponse
    {
        $profile = Profile::with('user')->findOrFail($id);

        $validated = $request->validate([
            'display_name' => 'required|string|max:150',
            'slug' => 'nullable|string|max:150|unique:profiles,slug,'.$profile->id,
            'headline' => 'nullable|string|max:255',
            'target_type' => 'nullable|string|max:100',
            'gender' => 'required|in:male,female,other',
            'age' => 'required|integer|between:18,99',
            'birthday' => 'nullable|date',
            'province_id' => 'nullable|exists:provinces,id',
            'province_name' => 'nullable|string|max:100',
            'district_name' => 'nullable|string|max:150',
            'marital_status' => 'nullable|string|max:100',
            'occupation' => 'nullable|string|max:150',
            'height' => 'nullable|string|max:50',
            'weight' => 'nullable|string|max:50',
            'education' => 'nullable|string|max:100',
            'body_type' => 'nullable|string|max:100',
            'interests' => 'nullable|string|max:2000',
            'personality' => 'nullable|string|max:150',
            'lifestyle' => 'nullable|string|max:150',
            'precious' => 'nullable|string|max:150',
            'religion' => 'nullable|string|max:100',
            'smoking' => 'nullable|string|max:100',
            'drinking' => 'nullable|string|max:100',
            'children' => 'nullable|string|max:100',
            'about_me' => 'nullable|string|max:10000',
            'looking_for' => 'nullable|string|max:10000',
            'privacy_option' => 'nullable|string|max:255',
            'status' => 'required|in:active,pending,blocked',
            'is_featured' => 'nullable',
            'last_active_at' => 'nullable|date',
            'avatar_url' => 'nullable|string|max:500',
            'avatar_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'user_email' => 'nullable|email|max:255',
            'user_username' => 'nullable|string|max:255',
            'user_password' => 'nullable|string|min:6',
        ]);

        if ($request->boolean('is_in_japan')) {
            $japanProv = Province::where('name', 'like', '%Nhật%')->first();
            $validated['province_id'] = $japanProv?->id ?? 68;
            $validated['province_name'] = $japanProv?->name ?? 'Nhật Bản (Japan)';
            if (empty($validated['district_name']) || $validated['district_name'] === 'Chưa cập nhật') {
                $validated['district_name'] = 'Tokyo, Nhật Bản';
            }
        } elseif (! empty($validated['province_id'])) {
            $province = Province::find($validated['province_id']);
            $validated['province_name'] = $province?->name ?? ($validated['province_name'] ?? null);
        } elseif (! empty($validated['province_name'])) {
            $matchedProv = Province::where('name', $validated['province_name'])->first();
            if ($matchedProv) {
                $validated['province_id'] = $matchedProv->id;
            }
        }

        if (! empty($validated['district_name'])) {
            $validated['district_name'] = Profile::resolveDistrictCode($validated['district_name']) ?: $validated['district_name'];
        }

        if (empty($validated['slug']) && ! empty($validated['display_name'])) {
            $validated['slug'] = Str::slug($validated['display_name']).'-'.$profile->id;
        }

        $validated['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('avatar_file')) {
            $file = $request->file('avatar_file');
            $destinationPath = public_path('themes/ehenho/images/avatars');
            if (! File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }
            $filename = 'avatar_'.($profile->user_id ?: 'admin_'.$profile->id).'_'.time().'.'.$file->getClientOriginalExtension();
            $file->move($destinationPath, $filename);
            $validated['avatar_url'] = 'themes/ehenho/images/avatars/'.$filename;
        }
        unset($validated['avatar_file']);

        // Update linked user account if exists
        if ($profile->user_id && $profile->user) {
            $user = $profile->user;
            if (! empty($validated['user_email']) && $validated['user_email'] !== $user->email) {
                $request->validate(['user_email' => 'unique:users,email,'.$user->id]);
                $user->email = $validated['user_email'];
            }
            if (! empty($validated['user_username']) && $validated['user_username'] !== $user->username) {
                $request->validate(['user_username' => 'unique:users,username,'.$user->id]);
                $user->username = $validated['user_username'];
            }
            if (! empty($validated['user_password'])) {
                $user->password = Hash::make($validated['user_password']);
            }
            $user->name = $validated['display_name'];
            $user->save();
        }
        unset($validated['user_email'], $validated['user_username'], $validated['user_password']);

        $profile->update($validated);

        return redirect()->route('project.admin.ehenho.profiles.edit', ['projectCode' => $projectCode, 'id' => $profile->id])
            ->with('success', 'Đã cập nhật toàn bộ thông tin hồ sơ và tài khoản thành công!');
    }

    public function toggleStatus(Request $request, string $projectCode, int $id): RedirectResponse
    {
        $profile = Profile::findOrFail($id);
        $profile->status = ($profile->status === 'blocked') ? 'active' : 'blocked';
        $profile->save();

        $statusText = ($profile->status === 'blocked') ? 'khóa' : 'kích hoạt lại';

        return back()->with('success', "Đã {$statusText} hồ sơ {$profile->display_name} thành công!");
    }

    public function destroy(string $projectCode, int $id): RedirectResponse
    {
        $profile = Profile::with('user')->findOrFail($id);
        $name = $profile->display_name;

        // Clean up local avatar file if exists
        if ($profile->avatar_url && str_starts_with($profile->avatar_url, 'themes/ehenho/images/avatars/')) {
            $avatarPath = public_path($profile->avatar_url);
            if (File::exists($avatarPath)) {
                @File::delete($avatarPath);
            }
        }

        $userId = $profile->user_id;
        $profile->delete();

        if ($userId) {
            User::withoutGlobalScopes()->where('id', $userId)->delete();
        }

        return redirect()->route('project.admin.ehenho.profiles.index', $projectCode)
            ->with('success', "Đã xóa hồ sơ thành viên {$name} và tài khoản liên kết thành công!");
    }

    public function interactions(Request $request, string $projectCode): View
    {
        $project = $this->resolveProject($request, $projectCode);

        $connections = SocialConnection::with(['user', 'targetProfile'])->latest('id')->paginate(20);
        $totalConnections = SocialConnection::count();
        $totalBlocks = SocialConnection::where('relation_type', 'block')->count();
        $totalLikes = SocialConnection::where('relation_type', 'like')->count();
        $totalConversations = Conversation::count();
        $totalMessages = Message::count();

        return view('themes.ehenho.admin.interactions.index', compact(
            'connections',
            'totalConnections',
            'totalBlocks',
            'totalLikes',
            'totalConversations',
            'totalMessages',
            'project',
            'projectCode'
        ));
    }
}
