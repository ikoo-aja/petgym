@extends('layouts.admin')

@section('title', 'Check-In Absensi &mdash; PetGym')
@section('page_title', 'Sistem Check-In / Absensi Member')
@section('page_subtitle', 'Antarmuka PIN Numpad kilat & pencarian manual log kedatangan harian')

@section('content')
<div class="row">
  @if(!Auth::user() || !Auth::user()->isOwner())
  <!-- Left Side: Numpad PIN & Manual Input (Khusus Staf/Admin) -->
  <div class="col-md-5">
    <div class="card-custom text-center">
      <h5 class="font-weight-bold text-dark mb-1">Check-In Kode Akses (PIN)</h5>
      <p class="text-muted mb-3" style="font-size: 12.5px;">Masukkan 6 digit Kode PIN Unik Member</p>

      <form action="{{ route('receptionist.checkin.process') }}" method="POST" id="checkinForm">
        @csrf
        <div class="form-group mb-3">
          <input type="password" name="access_code" id="pinDisplay" class="form-control text-center font-weight-bold text-primary" placeholder="••••••" readonly style="font-size: 28px; letter-spacing: 8px; height: 56px; border-radius: 12px; background: #f8fafc;">
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
      <form action="{{ route('receptionist.checkin.manual') }}" method="POST">
        @csrf
        <div class="form-group mb-2 position-relative" id="checkinMemberSearchWrapper">
          <input type="hidden" name="member_id" id="manualCheckinMemberId" required>
          <div class="input-group">
            <input type="text" id="manualMemberSearchInput" class="form-control" placeholder="Ketik nama atau no. HP member..." autocomplete="off" style="border-radius: 8px;">
            <div class="input-group-append" id="manualClearMemberBtnGroup" style="display: none;">
              <button class="btn btn-outline-secondary font-weight-bold" type="button" id="manualBtnClearMember" title="Hapus Pilihan">&times;</button>
            </div>
          </div>
          <div id="manualMemberSearchResults" class="list-group shadow position-absolute w-100 mt-1" style="display: none; max-height: 200px; overflow-y: auto; z-index: 1050; border-radius: 8px; text-align: left; left: 0;">
          </div>
        </div>
        <button type="submit" id="btnManualSubmit" class="btn btn-block btn-outline-primary font-weight-bold" style="border-radius: 8px;" disabled>
          Check-In Manual Member
        </button>
      </form>
    </div>
  </div>
  @endif

  <!-- Visit Logs Table -->
  <div class="{{ Auth::user() && Auth::user()->isOwner() ? 'col-md-12' : 'col-md-7' }}">
    <div class="card-custom">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="font-weight-bold text-dark mb-0"><i class="icon-clock-o mr-1 text-primary"></i> Rekap Log Kunjungan & Absensi Hari Ini</h6>
        <span class="badge badge-primary font-weight-bold px-3 py-2" style="border-radius: 8px;">Total: {{ count($todayCheckIns) }} Kunjungan</span>
      </div>

      <div class="table-responsive" style="max-height: 520px; overflow-y: auto;">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
            <tr>
              <th>Jam</th>
              <th>Member</th>
              <th>Kontak</th>
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
                <td style="font-size: 13px;" class="text-dark">
                  {{ $ci->member && $ci->member->phone ? \App\Helpers\PrivacyHelper::maskPhone($ci->member->phone) : '-' }}
                </td>
                <td>
                  @if($ci->check_in_method === 'code')
                    <span class="badge badge-success">Kode Akses (PIN)</span>
                  @else
                    <span class="badge badge-info">Manual Admin</span>
                  @endif
                </td>
                <td class="text-right">
                  @if(!Auth::user() || !Auth::user()->isOwner())
                  <form action="{{ route('receptionist.checkin.destroy', $ci->id) }}" method="POST" class="d-inline" data-confirm="Batalkan presensi kunjungan ini?">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;">Batalkan</button>
                  </form>
                  @else
                  <span class="text-muted small">Read-Only</span>
                  @endif
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

  // Live search untuk manual checkin member
  var checkinMemberList = [
    @foreach($allActiveMembers as $mem)
      {
        id: {{ $mem->id }},
        name: "{{ addslashes($mem->name) }}",
        phone: "{{ $mem->phone ?? '' }}",
        status: "Aktif"
      },
    @endforeach
  ];

  $(document).ready(function() {
    var $input = $('#manualMemberSearchInput');
    var $results = $('#manualMemberSearchResults');
    var $idInput = $('#manualCheckinMemberId');
    var $clearBtn = $('#manualClearMemberBtnGroup');
    var $submitBtn = $('#btnManualSubmit');

    function renderCheckinSearchResults(query) {
      $results.empty();
      query = (query || '').trim().toLowerCase();

      var matches = checkinMemberList.filter(function(item) {
        if (!query) return true;
        return item.name.toLowerCase().indexOf(query) !== -1 || item.phone.toLowerCase().indexOf(query) !== -1;
      });

      if (matches.length > 0) {
        $.each(matches.slice(0, 15), function(i, item) {
          var el = $('<a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2">')
            .html('<div><strong class="text-dark">' + item.name + '</strong></div><span class="badge badge-success">' + item.status + '</span>')
            .on('click', function(e) {
              e.preventDefault();
              $idInput.val(item.id);
              $input.val(item.name + ' — ' + item.status).prop('readonly', true);
              $clearBtn.show();
              $submitBtn.prop('disabled', false);
              $results.hide();
            });
          $results.append(el);
        });
      } else {
        $results.append('<div class="list-group-item text-muted py-2 text-center" style="font-size: 13px;">Member tidak ditemukan</div>');
      }

      $results.show();
    }

    $input.on('focus input', function() {
      if (!$input.prop('readonly')) {
        renderCheckinSearchResults($(this).val());
      }
    });

    $('#manualBtnClearMember').on('click', function() {
      $idInput.val('');
      $input.val('').prop('readonly', false).focus();
      $clearBtn.hide();
      $submitBtn.prop('disabled', true);
      renderCheckinSearchResults('');
    });

    $(document).on('click', function(e) {
      if (!$(e.target).closest('#checkinMemberSearchWrapper').length) {
        $results.hide();
      }
    });
  });
</script>
@endsection
