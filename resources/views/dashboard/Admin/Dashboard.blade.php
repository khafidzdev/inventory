@extends('templates.dashboard')
@section('content')
<style>
  .stat-card {
    border-radius: 12px;
    background: #ffffff;
    border: none;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }
  .stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
  }
  .stat-icon {
    width: 48px;
    height: 48px;
    min-width: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
  }
</style>
 <main role="main" class="main-content">
        <div class="container-fluid">
          <div class="row justify-content-center">
            <div class="col-12">
              <div class="row align-items-center mb-4">
                <div class="col">
                  <h2 class="h4 font-weight-bold mb-1" style="color: #1b68ff;">Selamat datang, {{ auth()->user()->name ?? 'Admin' }}!</h2>
                </div>
              </div>

              <!-- Stat Cards: Total Barang dan Total User -->
              <div class="row mb-3">
                <div class="col-12 col-md-6 mb-4">
                  <div class="card stat-card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center justify-content-center py-4 px-3">
                      <div class="stat-icon text-white mr-4" style="background-color: #ffaa00;">
                        <i class="fe fe-box" style="font-size: 22px;"></i>
                      </div>
                      <div>
                        <div class="text-muted small font-weight-medium mb-1">Total Barang</div>
                        <div class="font-weight-bold" style="color: #1e293b; font-size: 1.75rem; line-height: 1.2;">{{ $totalProducts }}</div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-12 col-md-6 mb-4">
                  <div class="card stat-card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center justify-content-center py-4 px-3">
                      <div class="stat-icon text-white mr-4" style="background-color: #00cfde;">
                        <i class="fe fe-users" style="font-size: 22px;"></i>
                      </div>
                      <div>
                        <div class="text-muted small font-weight-medium mb-1">Total User</div>
                        <div class="font-weight-bold" style="color: #1e293b; font-size: 1.75rem; line-height: 1.2;">{{ $totalUsers }}</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div> <!-- .col-12 -->
          </div> <!-- .row -->
        </div> <!-- .container-fluid -->
      </main> <!-- main -->
@endsection