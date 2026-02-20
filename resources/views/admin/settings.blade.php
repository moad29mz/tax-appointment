@extends('layouts.admin')

@section('title', 'الإعدادات - المديرية العامة للضرائب')

@push('styles')
    <style>
        .settings-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .section-title {
            color: #2c3e50;
            border-bottom: 2px solid #4a6491;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .logo-preview {
            width: 150px;
            height: 150px;
            border: 2px dashed #ddd;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: 15px;
        }
        .logo-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .logo-placeholder {
            color: #6c757d;
            text-align: center;
        }
        .logo-placeholder i {
            font-size: 40px;
            margin-bottom: 10px;
        }
        .current-logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .current-logo img {
            max-width: 200px;
            max-height: 100px;
            object-fit: contain;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 5px;
            background: #f8f9fa;
        }
    </style>
@endpush

@section('content')
    <div class="header">
        <h2><i class="fas fa-sliders-h"></i> إعدادات النظام</h2>
        <p class="mb-0">تخصيص إعدادات نظام حجز المواعيد</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- قسم الإعدادات العامة -->
        <div class="settings-section">
            <h4 class="section-title"><i class="fas fa-building"></i> الإعدادات العامة</h4>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="organization_name" class="form-label">اسم المؤسسة / الجماعة *</label>
                    <input type="text" class="form-control" id="organization_name" 
                           name="organization_name" 
                           value="{{ $settings['general']['organization_name']['value'] ?? 'المديرية العامة للضرائب' }}"
                           required>
                    <div class="form-text">اسم المؤسسة أو الجماعة المحلية</div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="municipality_name" class="form-label">اسم البلدية *</label>
                    <input type="text" class="form-control" id="municipality_name" 
                           name="municipality_name" 
                           value="{{ $settings['general']['municipality_name']['value'] ?? 'بلدية الرباط' }}"
                           required>
                    <div class="form-text">اسم البلدية أو العمالة</div>
                </div>
            </div>
        </div>

        <!-- قسم الشعار والمظهر -->
        <div class="settings-section">
            <h4 class="section-title"><i class="fas fa-image"></i> الشعار والمظهر</h4>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="logo" class="form-label">شعار المؤسسة</label>
                        
                        <!-- عرض الشعار الحالي إذا كان موجوداً -->
                        @if(isset($settings['appearance']['logo']['value']) && $settings['appearance']['logo']['value'])
                            <div class="current-logo">
                                <p class="text-muted">الشعار الحالي:</p>
                                <img src="{{ asset($settings['appearance']['logo']['value']) }}" 
                                     alt="شعار المؤسسة" 
                                     onerror="this.style.display='none'">
                            </div>
                        @endif
                        
                        <div class="logo-preview" id="logoPreview">
                            @if(isset($settings['appearance']['logo']['value']) && $settings['appearance']['logo']['value'])
                                <img src="{{ asset($settings['appearance']['logo']['value']) }}" 
                                     alt="معاينة الشعار"
                                     id="previewImage">
                            @else
                                <div class="logo-placeholder">
                                    <i class="fas fa-image"></i>
                                    <p>لا يوجد شعار</p>
                                </div>
                            @endif
                        </div>
                        
                        <input type="file" class="form-control" id="logo" name="logo" 
                               accept="image/*" onchange="previewLogo(event)">
                        <div class="form-text">يفضل صورة بحجم 200×200 بكسل بصيغة PNG أو JPG</div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="language" class="form-label">لغة التطبيق *</label>
                        <select class="form-select" id="language" name="language" required>
                            <option value="ar" {{ ($settings['system']['language']['value'] ?? 'ar') == 'ar' ? 'selected' : '' }}>
                                العربية (AR)
                            </option>
                            <option value="fr" {{ ($settings['system']['language']['value'] ?? 'ar') == 'fr' ? 'selected' : '' }}>
                                الفرنسية (FR)
                            </option>
                        </select>
                        <div class="form-text">اللغة الافتراضية للتطبيق</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- قسم معلومات الاتصال -->
        <div class="settings-section">
            <h4 class="section-title"><i class="fas fa-address-book"></i> معلومات الاتصال</h4>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="official_email" class="form-label">البريد الإلكتروني الرسمي *</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" id="official_email" 
                               name="official_email" 
                               value="{{ $settings['contact']['official_email']['value'] ?? 'contact@tax.gov' }}"
                               required>
                    </div>
                    <div class="form-text">البريد الإلكتروني الرسمي للمؤسسة</div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="phone_number" class="form-label">رقم الهاتف *</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        <input type="tel" class="form-control" id="phone_number" 
                               name="phone_number" 
                               value="{{ $settings['contact']['phone_number']['value'] ?? '+212537200000' }}"
                               required>
                    </div>
                    <div class="form-text">رقم الهاتف الرسمي للمؤسسة</div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="address" class="form-label">عنوان المؤسسة *</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                    <textarea class="form-control" id="address" name="address" 
                              rows="3" required>{{ $settings['contact']['address']['value'] ?? 'شارع محمد الخامس، الرباط، المغرب' }}</textarea>
                </div>
                <div class="form-text">العنوان الكامل للمؤسسة</div>
            </div>
        </div>

        <!-- قسم إعدادات الوقت -->
        <div class="settings-section">
            <h4 class="section-title"><i class="fas fa-clock"></i> إعدادات الوقت والمواعيد</h4>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="working_hours_start" class="form-label">ساعة بداية العمل *</label>
                    <input type="time" class="form-control" id="working_hours_start" 
                           name="working_hours_start" 
                           value="{{ $settings['system']['working_hours_start']['value'] ?? '09:00' }}"
                           required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="working_hours_end" class="form-label">ساعة نهاية العمل *</label>
                    <input type="time" class="form-control" id="working_hours_end" 
                           name="working_hours_end" 
                           value="{{ $settings['system']['working_hours_end']['value'] ?? '16:30' }}"
                           required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="appointment_duration_payment" class="form-label">مدة موعد أداء الضريبة (بالدقائق)</label>
                    <input type="number" class="form-control" id="appointment_duration_payment" 
                           name="appointment_duration_payment" value="15" min="5" max="60">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="appointment_duration_consultation" class="form-label">مدة موعد الاستفسار (بالدقائق)</label>
                    <input type="number" class="form-control" id="appointment_duration_consultation" 
                           name="appointment_duration_consultation" value="15" min="5" max="60">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="max_appointments_per_day" class="form-label">الحد الأقصى للمواعيد اليومية</label>
                    <input type="number" class="form-control" id="max_appointments_per_day" 
                           name="max_appointments_per_day" value="30" min="1" max="100">
                    <div class="form-text">عدد المواعيد المسموح بها في اليوم الواحد</div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="days_in_advance" class="form-label">عدد الأيام المسموح بالحجز مسبقاً</label>
                    <input type="number" class="form-control" id="days_in_advance" 
                           name="days_in_advance" value="30" min="1" max="365">
                    <div class="form-text">يمكن للعملاء الحجز حتى 30 يوماً مقدماً</div>
                </div>
            </div>
        </div>

        
                    
                    

        <!-- أزرار الحفظ -->
        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary btn-lg px-5">
                <i class="fas fa-save"></i> حفظ جميع الإعدادات
            </button>
           
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        // معاينة الشعار قبل الرفع
        function previewLogo(event) {
            const input = event.target;
            const preview = document.getElementById('logoPreview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" id="previewImage" alt="معاينة الشعار">`;
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        // التحقق من صحة البريد الإلكتروني
        document.getElementById('official_email').addEventListener('blur', function() {
            const email = this.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email && !emailRegex.test(email)) {
                alert('يرجى إدخال بريد إلكتروني صحيح');
                this.focus();
            }
        });

        // التحقق من صحة رقم الهاتف
        document.getElementById('phone_number').addEventListener('blur', function() {
            const phone = this.value;
            const phoneRegex = /^[\+]?[0-9\s\-\(\)]{8,}$/;
            
            if (phone && !phoneRegex.test(phone)) {
                alert('يرجى إدخال رقم هاتف صحيح');
                this.focus();
            }
        });

        // التحقق من أن وقت النهاية بعد وقت البداية
        document.getElementById('working_hours_end').addEventListener('change', function() {
            const startTime = document.getElementById('working_hours_start').value;
            const endTime = this.value;
            
            if (startTime && endTime && startTime >= endTime) {
                alert('وقت نهاية العمل يجب أن يكون بعد وقت بداية العمل');
                this.value = '';
                this.focus();
            }
        });

        // التأكد من حفظ التغييرات عند الخروج
        let formChanged = false;
        const form = document.querySelector('form');
        const inputs = form.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            input.addEventListener('change', () => {
                formChanged = true;
            });
        });
        
        window.addEventListener('beforeunload', (e) => {
            if (formChanged) {
                e.preventDefault();
                e.returnValue = 'لديك تغييرات غير محفوظة. هل تريد المغادرة دون الحفظ؟';
            }
        });
        
        form.addEventListener('submit', () => {
            formChanged = false;
        });
    </script>
@endpush