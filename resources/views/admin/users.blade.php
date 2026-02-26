@extends('layouts.admin')

@section('title', 'إدارة المستخدمين - جماعة وزان')

@push('styles')
    <style>
        .user-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        .user-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2c3e50, #4a6491);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 30px;
            font-weight: bold;
            margin: 0 auto 15px;
        }
        .role-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 600;
        }
        .badge-admin { background-color: #dc3545; color: white; }
        .badge-manager { background-color: #fd7e14; color: white; }
        .badge-employee { background-color: #28a745; color: white; }
        .badge-inactive { background-color: #6c757d; color: white; }
        .permissions-list {
            max-height: 150px;
            overflow-y: auto;
            font-size: 0.85em;
        }
        .permission-item {
            padding: 3px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .permission-item:last-child {
            border-bottom: none;
        }
        .action-buttons {
            position: absolute;
            top: 10px;
            left: 10px;
        }
        .user-stats {
            display: flex;
            justify-content: space-around;
            margin-top: 15px;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        .stat-item {
            text-align: center;
        }
        .stat-value {
            font-weight: bold;
            font-size: 18px;
            color: #2c3e50;
        }
        .stat-label {
            font-size: 12px;
            color: #6c757d;
        }
        .department-badge {
            background-color: #e8f4fd;
            color: #2c3e50;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.75em;
        }
    </style>
@endpush

@section('content')
    <div class="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-users-cog"></i> إدارة المستخدمين</h2>
                <p class="mb-0">إدارة حسابات موظفي النظام والصلاحيات</p>
            </div>
            <div>
                <button class="btn btn-light me-2" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="fas fa-user-plus"></i> إضافة مستخدم جديد
                </button>
                
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #36d1dc, #5b86e5);">
                <h3><i class="fas fa-users"></i> {{ \App\Models\AdminUser::count() }}</h3>
                <p>إجمالي المستخدمين</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #00b09b, #96c93d);">
                <h3><i class="fas fa-user-shield"></i> {{ \App\Models\AdminUser::where('role', 'admin')->count() }}</h3>
                <p>مديرو النظام</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #ff9966, #ff5e62);">
                <h3><i class="fas fa-user-tie"></i> {{ \App\Models\AdminUser::where('role', 'manager')->count() }}</h3>
                <p>مديرو الأقسام</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #7b4397, #dc2430);">
                <h3><i class="fas fa-user-check"></i> {{ \App\Models\AdminUser::where('is_active', true)->count() }}</h3>
                <p>مستخدمون نشطون</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="fas fa-list"></i> قائمة المستخدمين</h5>
                <div>
                    <div class="input-group input-group-sm" style="width: 300px;">
                        <input type="text" class="form-control" placeholder="بحث عن مستخدم..." id="searchUsers">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="usersTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>المستخدم</th>
                            <th>الدور</th>
                            <th>القسم</th>
                            <th>الحالة</th>
                            <th>آخر تسجيل دخول</th>
                            <th>تاريخ الإنشاء</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-3">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <strong>{{ $user->name }}</strong>
                                            <div class="text-muted small">{{ $user->email }}</div>
                                            @if($user->phone)
                                                <div class="text-muted small">
                                                    <i class="fas fa-phone"></i> {{ $user->phone }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $roles = \App\Models\AdminUser::getRoles();
                                    @endphp
                                    <span class="role-badge badge-{{ $user->role }}">
                                        {{ $roles[$user->role] ?? $user->role }}
                                    </span>
                                </td>
                                <td>
                                    @if($user->department)
                                        <span class="department-badge">{{ $user->department }}</span>
                                    @else
                                        <span class="text-muted">غير محدد</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle"></i> نشط
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times-circle"></i> غير نشط
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->last_login_at)
                                        {{ $user->last_login_at->format('d/m/Y H:i') }}
                                    @else
                                        <span class="text-muted">لم يسجل دخول بعد</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#viewUserModal{{ $user->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editUserModal{{ $user->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @if($user->id != auth()->id() && !($user->role == 'admin' && \App\Models\AdminUser::where('role', 'admin')->count() <= 1))
                                            <form action="{{ route('admin.users.delete', $user->id) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            <div class="modal fade" id="viewUserModal{{ $user->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">تفاصيل المستخدم: {{ $user->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-4 text-center">
                                                    <div class="user-avatar mb-3" style="width: 120px; height: 120px;">
                                                        {{ substr($user->name, 0, 1) }}
                                                    </div>
                                                    <h5>{{ $user->name }}</h5>
                                                    <p class="text-muted">{{ $user->email }}</p>
                                                    <span class="role-badge badge-{{ $user->role }} mb-2">
                                                        {{ $roles[$user->role] ?? $user->role }}
                                                    </span>
                                                    <div class="mt-2">
                                                        @if($user->is_active)
                                                            <span class="badge bg-success">نشط</span>
                                                        @else
                                                            <span class="badge bg-danger">غير نشط</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="mb-3">
                                                        <label class="form-label">معلومات الاتصال</label>
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <p><strong><i class="fas fa-phone"></i> الهاتف:</strong> 
                                                                    {{ $user->phone ?? 'غير محدد' }}
                                                                </p>
                                                            </div>
                                                            <div class="col-6">
                                                                <p><strong><i class="fas fa-building"></i> القسم:</strong> 
                                                                    {{ $user->department ?? 'غير محدد' }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">آخر نشاط</label>
                                                        <p>
                                                            @if($user->last_login_at)
                                                                <i class="fas fa-clock"></i> آخر تسجيل دخول: 
                                                                {{ $user->last_login_at->format('d/m/Y H:i') }}
                                                            @else
                                                                <i class="fas fa-clock"></i> لم يسجل دخول بعد
                                                            @endif
                                                        </p>
                                                        <p>
                                                            <i class="fas fa-calendar-plus"></i> تاريخ الإنشاء: 
                                                            {{ $user->created_at->format('d/m/Y') }}
                                                        </p>
                                                    </div>
                                                    
                                                    @if($user->permissions)
                                                        <div class="mb-3">
                                                            <label class="form-label">الصلاحيات</label>
                                                            <div class="permissions-list">
                                                                @php
                                                                    $allPermissions = \App\Models\AdminUser::getPermissions();
                                                                @endphp
                                                                @foreach($user->permissions as $permission)
                                                                    @php
                                                                        $found = false;
                                                                        foreach($allPermissions as $group => $perms) {
                                                                            if(isset($perms[$permission])) {
                                                                                echo '<div class="permission-item">'.$perms[$permission].'</div>';
                                                                                $found = true;
                                                                                break;
                                                                            }
                                                                        }
                                                                        if(!$found) {
                                                                            echo '<div class="permission-item">'.$permission.'</div>';
                                                                        }
                                                                    @endphp
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                                            <button type="button" class="btn btn-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editUserModal{{ $user->id }}"
                                                    data-bs-dismiss="modal">
                                                <i class="fas fa-edit"></i> تعديل
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">تعديل المستخدم: {{ $user->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                                            @csrf
                                            @method('POST')
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="name{{ $user->id }}" class="form-label">الاسم الكامل *</label>
                                                        <input type="text" class="form-control" id="name{{ $user->id }}" 
                                                               name="name" value="{{ $user->name }}" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="email{{ $user->id }}" class="form-label">البريد الإلكتروني *</label>
                                                        <input type="email" class="form-control" id="email{{ $user->id }}" 
                                                               name="email" value="{{ $user->email }}" required>
                                                    </div>
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="phone{{ $user->id }}" class="form-label">رقم الهاتف</label>
                                                        <input type="tel" class="form-control" id="phone{{ $user->id }}" 
                                                               name="phone" value="{{ $user->phone }}">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="department{{ $user->id }}" class="form-label">القسم</label>
                                                        <select class="form-select" id="department{{ $user->id }}" name="department">
                                                            <option value="">اختر القسم</option>
                                                            @foreach(\App\Models\AdminUser::getDepartments() as $key => $value)
                                                                <option value="{{ $key }}" {{ $user->department == $key ? 'selected' : '' }}>
                                                                    {{ $value }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="role{{ $user->id }}" class="form-label">الدور *</label>
                                                        <select class="form-select" id="role{{ $user->id }}" name="role" required>
                                                            @foreach(\App\Models\AdminUser::getRoles() as $key => $value)
                                                                <option value="{{ $key }}" {{ $user->role == $key ? 'selected' : '' }}>
                                                                    {{ $value }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">الحالة</label>
                                                        <div class="form-check form-switch mt-2">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   id="is_active{{ $user->id }}" name="is_active" 
                                                                   {{ $user->is_active ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="is_active{{ $user->id }}">
                                                                مستخدم نشط
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="password{{ $user->id }}" class="form-label">كلمة المرور (اتركها فارغة إذا لم ترد التغيير)</label>
                                                        <input type="password" class="form-control" id="password{{ $user->id }}" 
                                                               name="password">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="password_confirmation{{ $user->id }}" class="form-label">تأكيد كلمة المرور</label>
                                                        <input type="password" class="form-control" 
                                                               id="password_confirmation{{ $user->id }}" 
                                                               name="password_confirmation">
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">الصلاحيات</label>
                                                    <div class="permissions-list">
                                                        @php
                                                            $permissions = \App\Models\AdminUser::getPermissions();
                                                        @endphp
                                                        @foreach($permissions as $group => $groupPermissions)
                                                            <h6 class="mt-3">{{ ucfirst($group) }}</h6>
                                                            @foreach($groupPermissions as $key => $label)
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" 
                                                                           id="perm_{{ $user->id }}_{{ $key }}" 
                                                                           name="permissions[]" 
                                                                           value="{{ $key }}"
                                                                           {{ $user->permissions && in_array($key, $user->permissions) ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="perm_{{ $user->id }}_{{ $key }}">
                                                                        {{ $label }}
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
       
        document.getElementById('searchUsers').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('#usersTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchValue) ? '' : 'none';
            });
        });

        document.querySelectorAll('input[type="password"]').forEach(input => {
            input.addEventListener('blur', function() {
                const password = this.value;
                const confirmId = this.id.replace('password', 'password_confirmation');
                const confirmInput = document.getElementById(confirmId);
                
                if (password && confirmInput.value && password !== confirmInput.value) {
                    alert('كلمتا المرور غير متطابقتين');
                    this.focus();
                }
            });
        });

        document.querySelectorAll('select[name="role"]').forEach(select => {
            select.addEventListener('change', function() {
                const modal = this.closest('.modal');
                const permissionsDiv = modal.querySelector('.permissions-list');
                
                if (this.value === 'admin') {
                    
                    permissionsDiv.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                        checkbox.checked = true;
                        checkbox.disabled = true;
                    });
                } else {
                    permissionsDiv.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                        checkbox.disabled = false;
                    });
                }
            });
            
            select.dispatchEvent(new Event('change'));
        });

        document.addEventListener('DOMContentLoaded', function() {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                modal.addEventListener('shown.bs.modal', function() {
                    const select = this.querySelector('select[name="role"]');
                    if (select) {
                        select.dispatchEvent(new Event('change'));
                    }
                });
            });
        });
    </script>
@endpush

<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة مستخدم جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">الاسم الكامل *</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني *</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">رقم الهاتف</label>
                            <input type="tel" class="form-control" id="phone" name="phone">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="department" class="form-label">القسم</label>
                            <select class="form-select" id="department" name="department">
                                <option value="">اختر القسم</option>
                                @foreach(\App\Models\AdminUser::getDepartments() as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">كلمة المرور *</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">تأكيد كلمة المرور *</label>
                            <input type="password" class="form-control" id="password_confirmation" 
                                   name="password_confirmation" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">الدور *</label>
                            <select class="form-select" id="role" name="role" required>
                                @foreach(\App\Models\AdminUser::getRoles() as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الحالة</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" 
                                       id="is_active" name="is_active" checked>
                                <label class="form-check-label" for="is_active">
                                    مستخدم نشط
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">الصلاحيات</label>
                        <div class="permissions-list">
                            @php
                                $permissions = \App\Models\AdminUser::getPermissions();
                            @endphp
                            @foreach($permissions as $group => $groupPermissions)
                                <h6 class="mt-3">{{ ucfirst($group) }}</h6>
                                @foreach($groupPermissions as $key => $label)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               id="perm_{{ $key }}" 
                                               name="permissions[]" 
                                               value="{{ $key }}">
                                        <label class="form-check-label" for="perm_{{ $key }}">
                                            {{ $label }}
                                        </label>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة المستخدم</button>
                </div>
            </form>
        </div>
    </div>
</div>