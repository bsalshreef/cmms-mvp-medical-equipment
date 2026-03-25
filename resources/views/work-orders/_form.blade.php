{{--
    Shared form partial used by create.blade.php and edit.blade.php.
    Expects:
      $workOrder  – null on create, WorkOrder model on edit
      $devices    – collection of Device models
      $categories – collection of ServiceCategory models
--}}

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-3">

    {{-- Device --}}
    <div class="col-md-6">
        <label class="form-label">الجهاز <span class="text-danger">*</span></label>
        <select name="device_id"
                class="form-select @error('device_id') is-invalid @enderror"
                required>
            <option value="">-- اختر الجهاز --</option>
            @foreach($devices as $device)
                <option value="{{ $device->id }}"
                    {{ old('device_id', $workOrder?->device_id) == $device->id ? 'selected' : '' }}>
                    {{ $device->name }}
                    ({{ $device->device_code }})
                    @if($device->department) – {{ $device->department }} @endif
                </option>
            @endforeach
        </select>
        @error('device_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Service Category --}}
    <div class="col-md-6">
        <label class="form-label">نوع الخدمة</label>
        <select name="service_category_id"
                class="form-select @error('service_category_id') is-invalid @enderror">
            <option value="">-- اختر --</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}"
                    {{ old('service_category_id', $workOrder?->service_category_id) == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name_ar ?? $cat->name_en }}
                </option>
            @endforeach
        </select>
        @error('service_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Priority --}}
    <div class="col-md-4">
        <label class="form-label">الأولوية <span class="text-danger">*</span></label>
        <select name="priority"
                class="form-select @error('priority') is-invalid @enderror"
                required>
            @foreach(['LOW' => 'منخفضة','MEDIUM' => 'متوسطة','HIGH' => 'عالية','CRITICAL' => 'حرجة'] as $val => $label)
                <option value="{{ $val }}"
                    {{ old('priority', $workOrder?->priority ?? 'MEDIUM') === $val ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('priority') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Target Start Datetime --}}
    <div class="col-md-4">
        <label class="form-label">الموعد المستهدف</label>
        <input type="datetime-local"
               name="target_start_datetime"
               class="form-control @error('target_start_datetime') is-invalid @enderror"
               value="{{ old('target_start_datetime',
                   $workOrder?->target_start_datetime
                       ? \Carbon\Carbon::parse($workOrder->target_start_datetime)->format('Y-m-d\TH:i')
                       : '') }}">
        @error('target_start_datetime') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Description --}}
    <div class="col-12">
        <label class="form-label">وصف المشكلة <span class="text-danger">*</span></label>
        <textarea name="description"
                  rows="4"
                  class="form-control @error('description') is-invalid @enderror"
                  required
                  maxlength="2000">{{ old('description', $workOrder?->description) }}</textarea>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

</div>
