<style>
  .status-card {
    flex: 0 0 calc(100% / 7 - 10px);
    /* 7 كروت بالكامل */
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

  {{-- تم التصميم --}}
  <div class="status-card">
    <div class="card card-enhanced">
      <div class="card-body d-flex align-items-center p-2">
        <div class="icon-circle bg-warning text-white me-2">
          <i class="mdi mdi-clock-outline" style="font-size:18px;"></i>
        </div>
        <div>
          <div class="card-title mb-0">تم التصميم</div>
          <div class="card-count">{{ $pendingCount }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- تم الاعتماد --}}
  <div class="status-card">
    <div class="card card-enhanced">
      <div class="card-body d-flex align-items-center p-2">
        <div class="icon-circle bg-info text-white me-2">
          <i class="mdi mdi-check-circle-outline" style="font-size:18px;"></i>
        </div>
        <div>
          <div class="card-title mb-0">تم الاعتماد</div>
          <div class="card-count">{{ $completedCount }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- قيد التجهيز --}}
  <div class="status-card">
    <div class="card card-enhanced">
      <div class="card-body d-flex align-items-center p-2">
        <div class="icon-circle bg-purple text-white me-2">
          <i class="mdi mdi-cog-outline" style="font-size:18px;"></i>
        </div>
        <div>
          <div class="card-title mb-0">قيد التجهيز</div>
          <div class="card-count">{{ $preparingCount }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- تم التسليم --}}
  <div class="status-card">
    <div class="card card-enhanced">
      <div class="card-body d-flex align-items-center p-2">
        <div class="icon-circle bg-success text-white me-2">
          <i class="mdi mdi-package-variant-closed" style="font-size:18px;"></i>
        </div>
        <div>
          <div class="card-title mb-0">تم التسليم</div>
          <div class="card-count">{{ $receivedCount }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- مرتجع --}}
  <div class="status-card">
    <div class="card card-enhanced">
      <div class="card-body d-flex align-items-center p-2">
        <div class="icon-circle text-white me-2" style="background-color:#fd7e14;">
          <i class="mdi mdi-keyboard-return" style="font-size:18px;"></i>
        </div>
        <div>
          <div class="card-title mb-0">مرتجع</div>
          <div class="card-count">{{ $outForDeliveryCount }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- رفض الاستلام --}}
  <div class="status-card">
    <div class="card card-enhanced">
      <div class="card-body d-flex align-items-center p-2">
        <div class="icon-circle text-white me-2" style="background-color:#800000;">
          <i class="mdi mdi-cancel" style="font-size:18px;"></i>
        </div>
        <div>
          <div class="card-title mb-0">رفض الاستلام</div>
          <div class="card-count">{{ $canceledCount }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- خطأ --}}
  <div class="status-card">
    <div class="card card-enhanced">
      <div class="card-body d-flex align-items-center p-2">
        <div class="icon-circle text-white me-2" style="background-color:#dc3545;">
          <i class="mdi mdi-alert-circle" style="font-size:18px;"></i>
        </div>
        <div>
          <div class="card-title mb-0">خطأ</div>
          <div class="card-count">{{ $errorCount }}</div>
        </div>
      </div>
    </div>
  </div>

</div>