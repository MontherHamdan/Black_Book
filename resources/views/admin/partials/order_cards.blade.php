<style>
  .status-card {
    /* 8 كروت بالكامل */
    flex: 0 0 calc(100% / 8 - 10px);
    margin-right: 10px;
  }

  @media (max-width: 992px) {
    .status-card {
      flex: 0 0 calc(50% - 10px);
      /* موبايل كرتين بالسطر */
    }
  }

  @media (max-width: 576px) {
    .status-card {
      flex: 0 0 100%;
      /* شاشة صغيرة كرت واحد */
    }
  }
</style>

<div class="d-flex mt-2 flex-wrap">

  {{-- 1. طلب جديد --}}
  <div class="status-card">
    <div class="card card-enhanced">
      <div class="card-body d-flex align-items-center p-2">
        <div class="icon-circle bg-primary text-white me-2">
          <i class="mdi mdi-new-box" style="font-size:18px;"></i>
        </div>
        <div>
          <div class="card-title mb-0">طلب جديد</div>
          <div class="card-count">{{ $newOrderCount ?? 0 }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- 2. تم التصميم --}}
  <div class="status-card">
    <div class="card card-enhanced">
      <div class="card-body d-flex align-items-center p-2">
        <div class="icon-circle bg-warning text-white me-2">
          <i class="mdi mdi-clock-outline" style="font-size:18px;"></i>
        </div>
        <div>
          <div class="card-title mb-0">تم التصميم</div>
          <div class="card-count">{{ $pendingCount ?? 0 }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- 3. يوجد تعديل --}}
  <div class="status-card">
    <div class="card card-enhanced">
      <div class="card-body d-flex align-items-center p-2">
        <div class="icon-circle bg-danger text-white me-2">
          <i class="mdi mdi-alert-decagram" style="font-size:18px;"></i>
        </div>
        <div>
          <div class="card-title mb-0">يوجد تعديل</div>
          <div class="card-count">{{ $needsModificationCount ?? 0 }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- 4. تم الاعتماد --}}
  <div class="status-card">
    <div class="card card-enhanced">
      <div class="card-body d-flex align-items-center p-2">
        <div class="icon-circle bg-info text-white me-2">
          <i class="mdi mdi-check-circle-outline" style="font-size:18px;"></i>
        </div>
        <div>
          <div class="card-title mb-0">تم الاعتماد</div>
          <div class="card-count">{{ $completedCount ?? 0 }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- 5. قيد التجهيز --}}
  <div class="status-card">
    <div class="card card-enhanced">
      <div class="card-body d-flex align-items-center p-2">
        <div class="icon-circle bg-purple text-white me-2">
          <i class="mdi mdi-cog-outline" style="font-size:18px;"></i>
        </div>
        <div>
          <div class="card-title mb-0">قيد التجهيز</div>
          <div class="card-count">{{ $preparingCount ?? 0 }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- 6. تم التسليم --}}
  <div class="status-card">
    <div class="card card-enhanced">
      <div class="card-body d-flex align-items-center p-2">
        <div class="icon-circle bg-success text-white me-2">
          <i class="mdi mdi-package-variant-closed" style="font-size:18px;"></i>
        </div>
        <div>
          <div class="card-title mb-0">تم التسليم</div>
          <div class="card-count">{{ $receivedCount ?? 0 }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- 7. مرتجع --}}
  <div class="status-card">
    <div class="card card-enhanced">
      <div class="card-body d-flex align-items-center p-2">
        <div class="icon-circle text-white me-2" style="background-color:#fd7e14;">
          <i class="mdi mdi-keyboard-return" style="font-size:18px;"></i>
        </div>
        <div>
          <div class="card-title mb-0">مرتجع</div>
          <div class="card-count">{{ $outForDeliveryCount ?? 0 }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- 8. رفض الاستلام --}}
  <div class="status-card">
    <div class="card card-enhanced">
      <div class="card-body d-flex align-items-center p-2">
        <div class="icon-circle text-white me-2" style="background-color:#800000;">
          <i class="mdi mdi-cancel" style="font-size:18px;"></i>
        </div>
        <div>
          <div class="card-title mb-0">رفض الاستلام</div>
          <div class="card-count">{{ $canceledCount ?? 0 }}</div>
        </div>
      </div>
    </div>
  </div>

</div>