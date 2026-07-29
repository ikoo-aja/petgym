@extends('layouts.admin')

@section('title', 'Check-In Absensi &mdash; PetGym')
@section('page_title', 'Sistem Check-In / Absensi Member')
@section('page_subtitle', 'Antarmuka PIN Numpad kilat & pencarian manual log kedatangan harian')

@section('content')
<div class="row">
  <!-- Left Side: Numpad PIN & Manual Input -->
  <div class="col-md-5">
    <div class="card-custom text-center">
      <h5 class="font-weight-bold text-dark mb-1">Check-In Kode Akses (PIN)</h5>
      <p class="text-muted mb-3" style="font-size: 12.5px;">Masukkan 6 digit Kode PIN Unik Member</p>

      <form action="{{ route('admin.checkin.process') }}" method="POST" id="checkinForm">
        @csrf
        <div class="form-group mb-3">
          <input type="text" name="access_code" id="pinDisplay" class="form-control text-center font-weight-bold text-primary" placeholder="------" readonly style="font-size: 28px; letter-spacing: 6px; height: 56px; border-radius: 12px; background: #f8fafc;">
        </div>

        <!-- Virtual Numpad -->
        <div class="row no-gutters mb-3" style="max-width: 280px; margin: 0 auto;">
          <div class="col-4 p-1"><button type="button" class="btn btn-block btn-light font-weight-bold py-3 num-btn" onclick="pressNum('1')" style="font-size: 20px; border-radius: 10px;">1</button></div>
          <div class="col-4 p-1"><button type="button" class="btn btn-block btn-light font-weight-bold py-3 num-btn" onclick="pressNum('2')" style="font-size: 20px; border-radius: 10px;">2</button></div>
          <div class="col-4 p-1"><button type="button" class="btn btn-block btn-light font-weight-bold py-3 num-btn" onclick="pressNum('3')" style="font-size: 20px; border-radius: 10px;">3</button></div>
          <div class="col-4 p-1"><button type="button" class="btn btn-block btn-light font-weight-bold py-3 num-btn" onclick="pressNum('4')" style="font-size: 20px; border-radius: 10px;">4</button></div>
          <div class="col-4 p-1"><button type="button" class="btn btn-block btn-light font-weight-bold py-3 num-btn" onclick="pressNum('5')" style="font-size: 20px; border-radius: 10px;">5</button></div>
          <div class="col-4 p-1"><button type="button" class="btn btn-block btn-light font-weight-bold py-3 num-btn" onclick="pressNum('6')" style="font-size: 20px; border-radius: 10px;">6</button></div>
          <div class="col-4 p-1"><button type="button" class="btn btn-block btn-light font-weight-bold py-3 num-btn" onclick="pressNum('7')" style="font-size: 20px; border-radius: 10px;">7</button></div>
          <div class="col-4 p-1"><button type="button" class="btn btn-block btn-light font-weight-bold py-3 num-btn" onclick="pressNum('8')" style="font-size: 20px; border-radius: 10px;">8</button></div>
          <div class="col-4 p-1"><button type="button" class="btn btn-block btn-light font-weight-bold py-3 num-btn" onclick="pressNum('9')" style="font-size: 20px; border-radius: 10px;">9</button></div>
          <div class="col-4 p-1"><button type="button" class="btn btn-block btn-warning font-weight-bold py-3 text-dark" onclick="clearPin()" style="font-size: 14px; border-radius: 10px;">CLEAR</button></div>
          <div class="col-4 p-1"><button type="button" class="btn btn-block btn-light font-weight-bold py-3 num-btn" onclick="pressNum('0')" style="font-size: 20px; border-radius: 10px;">0</button></div>
          <div class="col-4 p-1"><button type="button" class="btn btn-block btn-danger font-weight-bold py-3" onclick="backspacePin()" style="font-size: 14px; border-radius: 10px;">DEL</button></div>
        </div>

        <button type="submit" class="btn btn-block btn-success font-weight-bold py-3" style="border-radius: 10px; font-size: 16px;">
          SUBMIT CHECK-IN
        </button>
      </form>

      <hr class="my-4">

      <!-- Manual Entry Fallback -->
      <h6 class="font-weight-bold text-dark mb-2 text-left" style="font-size: 13.5px;">Manual Entry oleh Admin (Cadangan)</h6>
      <form action="{{ route('admin.checkin.manual') }}" method="POST">
        @csrf
        <div class="form-group mb-2">
          <select name="member_id" class="form-control" required style="border-radius: 8px;">
            <option value="">-- Cari Nama Member --</option>
            @foreach($allActiveMembers as $mem)
              <option value="{{ $mem->id }}">{{ $mem->name }} (PIN: {{ $mem->access_code }})</option>
            @endforeach
          </select>
        </div>
        <button type="submit" class="btn btn-block btn-outline-primary font-weight-bold" style="border-radius: 8px;">
          Check-In Manual Name
        </button>
      </form>
    </div>
  </div>

  <!-- Right Side: Visit Logs Today -->
  <div class="col-md-7">
    <div class="card-custom">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="font-weight-bold text-dark mb-0">Log Kunjungan Hari Ini</h6>
        <span class="badge badge-primary font-weight-bold px-3 py-2" style="border-radius: 12px;">{{ count($todayCheckIns) }} Kunjungan</span>
      </div>

      <div class="table-responsive" style="max-height: 520px; overflow-y: auto;">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
            <tr>
              <th>Jam</th>
              <th>Member</th>
              <th>Kode PIN</th>
              <th>Metode</th>
              <th class="text-right">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($todayCheckIns as $ci)
              <tr>
                <td class="font-weight-bold text-dark" style="font-size: 13.5px;">
                  {{ $ci->checked_in_at ? $ci->checked_in_at->format('H:i:s') : '-' }}
                </td>
                <td>
                  <div class="font-weight-bold text-dark">{{ $ci->member ? $ci->member->name : 'Member Unknown' }}</div>
                  <small class="text-muted">Expired: {{ ($ci->member && $ci->member->expired_at) ? $ci->member->expired_at->format('d M Y') : '-' }}</small>
                </td>
                <td>
                  <span class="badge badge-secondary" style="font-size: 12px; letter-spacing: 1px;">{{ $ci->access_code }}</span>
                </td>
                <td>
                  @if($ci->check_in_method === 'code')
                    <span class="badge badge-success">PIN Code</span>
                  @else
                    <span class="badge badge-info">Manual Admin</span>
                  @endif
                </td>
                <td class="text-right">
                  <form action="{{ route('admin.checkin.destroy', $ci->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Batalkan/Hapus log check-in ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-xs btn-outline-danger" style="font-size: 11px;">Batal</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center py-4 text-muted">Belum ada kunjungan check-in hari ini.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  function pressNum(num) {
    var display = $('#pinDisplay');
    if (display.val().length < 6) {
      display.val(display.val() + num);
    }
  }

  function clearPin() {
    $('#pinDisplay').val('');
  }

  function backspacePin() {
    var val = $('#pinDisplay').val();
    $('#pinDisplay').val(val.substring(0, val.length - 1));
  }
</script>
@endsection
