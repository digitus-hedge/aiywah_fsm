{{-- resources/views/user_directory.blade.php --}}
@extends('layouts.layout')

@section('title', 'User Directory | MATTER MIND')
@section('page_title', 'User Directory')
@section('page_icon', 'database')

@push('styles')
<style>
/* ═══════════════════════════════════════
   PAGE HEADER  (matches Inquiry Approval)
═══════════════════════════════════════ */
.ud-header{background:linear-gradient(135deg,#9A7B4F 0%,#7A6140 100%);border-radius:10px;padding:20px 24px;margin-bottom:20px;color:#fff;position:relative;overflow:hidden;}
.ud-header::before{content:'';position:absolute;left:-40px;bottom:-40px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.05);}
.ud-header::after{content:'';position:absolute;right:-30px;top:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.07);}
.ud-header h4{font-family:var(--font-body);font-weight:700;font-size:1rem;margin:0 0 3px;position:relative;z-index:1;}
.ud-header p{font-size:.78rem;margin:0;opacity:.85;position:relative;z-index:1;}
.ud-header .meta-row{display:flex;align-items:center;gap:8px;margin-top:10px;position:relative;z-index:1;flex-wrap:wrap;}
.ud-header .meta-badge{background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);border-radius:20px;font-size:.6875rem;padding:2px 10px;font-weight:500;}
@media(max-width:575.98px){.ud-header{padding:14px 16px;}.ud-header h4{font-size:.9rem;}}

/* ═══════════════════════════════════════
   STATS STRIP
═══════════════════════════════════════ */
.ud-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:18px;}
.ud-stat{background:var(--card-bg);border:1px solid var(--card-border);border-radius:8px;padding:14px 16px;display:flex;align-items:center;gap:12px;box-shadow:var(--card-shadow);}
.ud-stat-icon{width:40px;height:40px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;}
.ud-stat-num{font-size:1.5rem;font-weight:700;line-height:1;}
.ud-stat-lbl{font-size:.72rem;color:var(--text-muted);margin-top:2px;}
@media(max-width:767.98px){.ud-stats{grid-template-columns:repeat(2,1fr);}}
@media(max-width:399px){.ud-stats{grid-template-columns:1fr 1fr;gap:8px;}}

/* ═══════════════════════════════════════
   FILTER BAR
═══════════════════════════════════════ */
.ud-filter{background:var(--card-bg);border:1px solid var(--card-border);border-radius:8px;padding:12px 14px;margin-bottom:12px;box-shadow:var(--card-shadow);display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
.ud-search{position:relative;flex:1;min-width:180px;}
.ud-search i{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:.85rem;}
.ud-search input{padding-left:32px;width:100%;}
.ud-filter .form-control-sm,.ud-filter .form-select-sm{font-size:.78rem;border:1px solid var(--border-color);border-radius:6px;padding:.35rem .7rem;color:var(--text-primary);background:var(--input-bg);height:34px;}
.ud-filter .form-control-sm:focus,.ud-filter .form-select-sm:focus{border-color:rgba(101,113,255,.5);box-shadow:0 0 0 3px rgba(101,113,255,.12);outline:none;}
.ud-filter .form-control-sm::placeholder{color:var(--text-light);}
html[data-theme="dark"] .ud-filter .form-select-sm option{background:#101e33;color:#c8d4e8;}
.ud-actions{margin-left:auto;display:flex;gap:8px;flex-wrap:wrap;}
@media(max-width:767.98px){.ud-filter{flex-direction:column;align-items:stretch;gap:8px;}.ud-filter .form-select-sm{width:100%!important;}.ud-search{min-width:100%;}.ud-actions{margin-left:0;}}

/* Buttons — gold primary, ghost secondary (theme-consistent) */
.ud-btn{display:inline-flex;align-items:center;gap:6px;border-radius:6px;font-size:.78rem;font-weight:500;padding:.4rem .9rem;cursor:pointer;white-space:nowrap;text-decoration:none;height:34px;border:1px solid transparent;transition:all .15s;}
.ud-btn i{font-size:.9rem;}
.ud-btn-gold{background:linear-gradient(135deg,#9A7B4F,#7A6140);color:#fff;}
.ud-btn-gold:hover{box-shadow:0 4px 16px rgba(154,123,79,.35);color:#fff;}
.ud-btn-ghost{background:var(--surface-2);color:var(--text-muted);border-color:var(--border-color);}
.ud-btn-ghost:hover{border-color:var(--text-muted);color:var(--text-heading);}

/* ═══════════════════════════════════════
   GRID / TABLE CARD
═══════════════════════════════════════ */
.ud-card{background:var(--card-bg);border:1px solid var(--card-border);border-radius:8px;box-shadow:var(--card-shadow);overflow:hidden;}
.ud-card-header{padding:13px 16px;border-bottom:1px solid var(--card-border);display:flex;align-items:center;justify-content:space-between;gap:10px; font-family: var(--font-header);}
.ud-card-header h6{font-family:var(--font-body);font-weight:700;margin:0;font-size:.875rem;color:var(--text-heading);display:flex;align-items:center;gap:8px;}
.ud-rec-badge{background:rgba(154,123,79,.12);color:#9A7B4F;font-size:.65rem;font-weight:700;padding:2px 8px;border-radius:10px;}
.ud-table-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch;}
.ud-table{width:100%;border-collapse:collapse;min-width:720px;}
.ud-table thead tr{background:var(--table-header);}
.ud-table thead th{font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);padding:10px 12px;border-bottom:1px solid var(--card-border);white-space:nowrap;text-align:left;}
.ud-table thead th:first-child{padding-left:14px;}
.ud-table tbody tr{border-bottom:1px solid var(--card-border);transition:background .15s;}
.ud-table tbody tr:last-child{border-bottom:none;}
.ud-table tbody tr:hover{background:var(--table-hover);}
.ud-table tbody td{padding:11px 12px;font-size:.8rem;vertical-align:middle;color:var(--text-primary);}
.ud-table tbody td:first-child{padding-left:14px;}
.ud-muted{color:var(--text-muted);font-size:.75rem;}

/* User cell */
.ud-user-cell{display:flex;align-items:center;gap:10px;}
.ud-av{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.72rem;font-weight:700;color:#fff;flex-shrink:0;}
.ud-uname{font-weight:500;color:var(--text-heading);font-size:.8rem;}
.ud-uemail{font-size:.68rem;color:var(--text-muted);}

/* Role + status chips (match priority/warranty chip sizing) */
.ud-role-pill{display:inline-flex;align-items:center;gap:5px;font-size:.62rem;font-weight:700;padding:2px 8px;border-radius:10px;white-space:nowrap;}
.ud-badge{display:inline-flex;align-items:center;gap:5px;font-size:.62rem;font-weight:700;padding:2px 8px;border-radius:10px;white-space:nowrap;}
.ud-badge i{font-size:.5rem;}
.ud-active{background:rgba(5,163,74,.12);color:#05a34a;}
.ud-pending{background:rgba(251,188,6,.12);color:#a8802a;}
.ud-inactive{background:rgba(174,183,197,.15);color:#5a6a7e;}

/* Row action buttons */
.ud-row-actions{display:flex;gap:5px;}
.ud-xs{padding:4px 9px;border-radius:5px;font-size:.72rem;font-weight:600;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:4px;text-decoration:none;transition:background .15s;}
.ud-xs i{font-size:.8rem;}
.ud-xs-edit{background:rgba(154,123,79,.12);color:#9A7B4F;}
.ud-xs-edit:hover{background:rgba(154,123,79,.22);color:#9A7B4F;}
.ud-xs-key{background:rgba(101,113,255,.1);color:#6571ff;}
.ud-xs-key:hover{background:rgba(101,113,255,.2);}
.ud-xs-off{background:rgba(255,51,102,.08);color:#ff3366;}
.ud-xs-off:hover{background:rgba(255,51,102,.16);}

/* Empty state */
.ud-empty{text-align:center;padding:40px 20px;color:var(--text-muted);}
.ud-empty i{font-size:2.2rem;display:block;margin-bottom:8px;opacity:.3;}

/* Footer / pagination */
.ud-footer{padding:10px 14px;border-top:1px solid var(--card-border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;}
.ud-footer .info{font-size:.72rem;color:var(--text-muted);}
.ud-pager .pagination{margin:0;gap:4px;flex-wrap:wrap;}
.ud-pager .page-link{background:var(--surface-2);border:1px solid var(--border-color);border-radius:5px;min-width:28px;height:28px;display:flex;align-items:center;justify-content:center;font-size:.75rem;color:var(--text-muted);padding:0 6px;transition:all .15s;}
.ud-pager .page-link:hover{background:rgba(154,123,79,.1);border-color:#9A7B4F;color:#9A7B4F;}
.ud-pager .active .page-link{background:rgba(154,123,79,.12);border-color:#9A7B4F;color:#9A7B4F;}
.ud-pager .disabled .page-link{opacity:.45;pointer-events:none;}

.ud-modal-overlay{display:none;position:fixed;inset:0;background:rgba(9,15,35,.6);z-index:9998;align-items:flex-start;justify-content:center;padding:40px 16px;overflow-y:auto;}
.ud-modal-overlay.show{display:flex;}
.ud-modal{background:var(--card-bg);border:1px solid var(--card-border);border-radius:12px;width:100%;max-width:460px;box-shadow:0 20px 60px rgba(0,0,0,.3);}
.ud-modal-hdr{display:flex;align-items:center;justify-content:space-between;padding:15px 18px;border-bottom:1px solid var(--card-border);}
.ud-modal-hdr h6{margin:0;font-size:.9rem;font-weight:700;color:var(--text-heading);display:flex;align-items:center;gap:8px;}
.ud-modal-x{background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:1rem;padding:4px;border-radius:6px;}
.ud-modal-x:hover{background:var(--surface-2);color:var(--text-heading);}
.ud-modal-body{padding:16px 18px;}
.ud-lbl{display:block;font-size:.72rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin:12px 0 5px;}
.ud-lbl:first-child{margin-top:0;}
.ud-inp{width:100%;font-size:.85rem;border:1px solid var(--border-color);border-radius:7px;padding:.5rem .7rem;background:var(--input-bg);color:var(--text-primary);}
.ud-inp:focus{outline:none;border-color:#9A7B4F;box-shadow:0 0 0 3px rgba(154,123,79,.12);}
.ud-err{font-size:.7rem;color:#ff3366;min-height:14px;margin-top:3px;}
.ud-domains{display:flex;flex-wrap:wrap;gap:6px;}
.ud-dom-chip{font-size:.72rem;padding:5px 10px;border-radius:16px;border:1px solid var(--border-color);background:var(--surface-2);color:var(--text-muted);cursor:pointer;user-select:none;}
.ud-dom-chip.on{background:rgba(154,123,79,.14);border-color:#9A7B4F;color:#9A7B4F;font-weight:600;}
.ud-modal-ftr{display:flex;justify-content:flex-end;gap:8px;padding:13px 18px;border-top:1px solid var(--card-border);}
/* ═══════════════════════════════════════
   TOAST (matches Inquiry Approval)
═══════════════════════════════════════ */
.ud-toast-wrap{position:fixed;top:70px;right:16px;z-index:9999;display:flex;flex-direction:column;gap:8px;max-width:310px;}
.ud-toast{background:var(--card-bg);border-left:4px solid #6571ff;border-radius:7px;padding:12px 14px;box-shadow:0 6px 24px rgba(0,0,0,.18);display:flex;align-items:flex-start;gap:10px;animation:udToastIn .3s ease;}
.ud-toast.success{border-color:#05a34a;}.ud-toast.error{border-color:#ff3366;}.ud-toast.warning{border-color:#fbbc06;}
@keyframes udToastIn{from{transform:translateX(40px);opacity:0;}to{transform:none;opacity:1;}}
.ud-toast .ti-icon{font-size:1.1rem;flex-shrink:0;margin-top:1px;}
.ud-toast.primary .ti-icon{color:#6571ff;}.ud-toast.success .ti-icon{color:#05a34a;}.ud-toast.error .ti-icon{color:#ff3366;}.ud-toast.warning .ti-icon{color:#fbbc06;}
.ud-toast .ti-title{font-size:.8125rem;font-weight:600;margin:0 0 2px;color:var(--text-heading);}
.ud-toast .ti-body{font-size:.72rem;margin:0;color:var(--text-muted);}
@media(max-width:575.98px){.ud-toast-wrap{left:12px;right:12px;max-width:none;}}

/* ═══════════════════════════════════════
   PHONE: stack table rows into cards
═══════════════════════════════════════ */
@media(max-width:575.98px){
  .ud-table{min-width:0;}
  .ud-table-wrap{overflow-x:visible;}
  .ud-table thead{display:none;}
  .ud-table,.ud-table tbody,.ud-table tr,.ud-table td{display:block;width:100%;}
  .ud-table tbody tr{border:1px solid var(--card-border);border-radius:8px;margin-bottom:10px;padding:8px 4px;background:var(--card-bg);}
  .ud-table tbody td{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:7px 12px;border:none;text-align:right;}
  .ud-table tbody td::before{content:attr(data-label);font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);flex-shrink:0;}
  .ud-table tbody td.cell-user{justify-content:flex-start;text-align:left;}
  .ud-table tbody td.cell-user::before{content:'';}
  .ud-row-actions{justify-content:flex-end;}
}
</style>
@endpush

@section('content')

  {{-- Page Header --}}
  <div class="ud-header">
    <h4><i class="bi bi-people-fill me-2"></i>User Directory</h4>
    <p>Browse and manage all portal user accounts. Review role assignments, domain expertise tags, and account status. Use User Provisioning to create new accounts.</p>
    <div class="meta-row">
      @foreach ($roles as $role)
        <span class="meta-badge"><i class="bi bi-shield-fill-check me-1"></i>{{ $role->name }}</span>
      @endforeach
    </div>
  </div>

  {{-- Stats --}}
  <div class="ud-stats">
    <div class="ud-stat">
      <div class="ud-stat-icon" style="background:rgba(101,113,255,.1);"><i class="bi bi-people-fill" style="color:#6571ff;"></i></div>
      <div><div class="ud-stat-num" style="color:#6571ff;">{{ $stats['total'] }}</div><div class="ud-stat-lbl">Total Users</div></div>
    </div>
    <div class="ud-stat">
      <div class="ud-stat-icon" style="background:rgba(5,163,74,.1);"><i class="bi bi-check2-circle" style="color:#05a34a;"></i></div>
      <div><div class="ud-stat-num" style="color:#05a34a;">{{ $stats['active'] }}</div><div class="ud-stat-lbl">Active Accounts</div></div>
    </div>
    <div class="ud-stat">
      <div class="ud-stat-icon" style="background:rgba(251,188,6,.1);"><i class="bi bi-hourglass-split" style="color:#fbbc06;"></i></div>
      <div><div class="ud-stat-num" style="color:#a8802a;">{{ $stats['pending'] }}</div><div class="ud-stat-lbl">Pending</div></div>
    </div>
    <div class="ud-stat">
      <div class="ud-stat-icon" style="background:rgba(255,51,102,.1);"><i class="bi bi-slash-circle" style="color:#ff3366;"></i></div>
      <div><div class="ud-stat-num" style="color:#ff3366;">{{ $stats['inactive'] }}</div><div class="ud-stat-lbl">Inactive Accounts</div></div>
    </div>
  </div>

  {{-- Filter Bar --}}
  <form method="GET" action="{{ route('user_directory') }}" class="ud-filter">
    <div class="ud-search">
      <i class="bi bi-search"></i>
      <input type="text" class="form-control-sm" name="q" value="{{ request('q') }}" placeholder="Search name or email…" 
      style="padding: .35rem 2.12rem;">
    </div>
    <select class="form-select-sm" name="role" style="width:150px;">
      <option value="">All Roles</option>
      @foreach ($roles as $role)
        <option value="{{ $role->code }}" @selected(request('role') === $role->code)>{{ $role->name }}</option>
      @endforeach
    </select>
    <select class="form-select-sm" name="status" style="width:130px;">
      <option value="">All Status</option>
      <option value="active" @selected(request('status')==='active')>Active</option>
      <option value="pending" @selected(request('status')==='pending')>Pending</option>
      <option value="inactive" @selected(request('status')==='inactive')>Inactive</option>
    </select>
    <div class="ud-actions">
      <a href="{{ route('user_directory') }}" class="ud-btn ud-btn-ghost"><i class="bi bi-x-circle"></i>Reset</a>
      <button type="submit" class="ud-btn ud-btn-gold"><i class="bi bi-funnel"></i>Apply</button>
      @if (auth()->user() && auth()->user()->hasAccess('user_provisioning')  && auth()->user()?->role?->code !== 'SE')
        <a href="{{ route('user_provisioning') }}" class="ud-btn ud-btn-gold"><i class="bi bi-person-plus"></i>New User</a>
      @endif
    </div>
  </form>

  {{-- Table Card --}}
  <div class="ud-card">
    <div class="ud-card-header">
      <h6><i class="bi bi-table" style="color:#9A7B4F;"></i>All Users <span class="ud-rec-badge">{{ $users->total() }} records</span></h6>
      <span style="font-size:.72rem;color:var(--text-muted);">Role &amp; domain assignments</span>
    </div>
    <div class="ud-table-wrap">

    @php
  $showCategory = $users->contains(fn($u) => optional($u->role)->code === 'SE');
@endphp
      <table class="ud-table">
        <thead>
          <tr>
            <th>User</th>
            <th>Role</th>
            @if($showCategory)<th>Category</th>@endif

            <th>Domain Expertise</th>
            <th>Status</th>
            <th>Created</th>
            <th style="width:150px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($users as $user)
            @php
  $initials = collect(explode(' ', trim($user->name)))
      ->map(fn($w) => mb_substr($w, 0, 1))
      ->take(2)->implode('');
  $palette   = ['#9A7B4F','#6571ff','#05a34a','#4895ef','#fbbc06','#ec4899','#8b5cf6','#ff3366'];
  $avColor   = $palette[$user->id % count($palette)];
  $roleColor = optional($user->role)->color_code ?? '#9A7B4F';

  $isSE       = optional($user->role)->code === 'SE';
  $categories = $isSE
      ? $user->serviceCategories->pluck('category_name')->filter()->unique()->take(3)->implode(', ')
      : '';

  $domains   = $user->serviceDomains->pluck('domain_name')->filter()->take(3)->implode(', ');
  $status    = $user->status ?? 'active';
  $sClass    = match($status){ 'active'=>'ud-active','pending'=>'ud-pending', default=>'ud-inactive' };
@endphp
            <tr>
              <td class="cell-user" data-label="User">
                <div class="ud-user-cell">
                  <div class="ud-av" style="background:{{ $avColor }};">{{ strtoupper($initials) }}</div>
                  <div>
                    <div class="ud-uname">{{ $user->name }}</div>
                    <div class="ud-uemail">{{ $user->email }}</div>
                  </div>
                </div>
              </td>
              <td data-label="Role">
                <span class="ud-role-pill" style="color:{{ $roleColor }};background:{{ $roleColor }}1f;">{{ optional($user->role)->name ?? '—' }}</span>
              </td>


              @if($showCategory)
  <td data-label="Category" class="ud-muted">
    @if(!$isSE)
      —
    @elseif($categories !== '')
      {{ $categories }}
    @else
      <span style="color:#f97316;font-size:.72rem;">
        <i class="bi bi-exclamation-triangle"></i> Not assigned
      </span>
    @endif
  </td>
@endif


              <td data-label="Domain" class="ud-muted">{{ $domains !== '' ? $domains : '—' }}</td>
              <td data-label="Status">
                <span class="ud-badge {{ $sClass }}"><i class="bi bi-circle-fill"></i>{{ ucfirst($status) }}</span>
              </td>
              <td data-label="Created" class="ud-muted">{{ optional($user->created_at)->format('d M Y') ?? '—' }}</td>
              <td data-label="Actions">
                <div class="ud-row-actions">
                  {{-- <a href="{{ route('user_provisioning') }}?edit={{ $user->id }}" class="ud-xs ud-xs-edit"><i class="bi bi-pencil"></i>Edit</a>--}} 
                  <button type="button" class="ud-xs ud-xs-edit"
                          onclick="udOpenEdit({{ $user->id }})">
                    <i class="bi bi-pencil"></i>Edit
                  </button>
                  {{-- <button type="button" class="ud-xs ud-xs-key"
                    onclick="udPost('{{ route('user_directory.reset', $user->id) }}','primary','Reset Sent','Password reset email sent to {{ $user->email }}')">
                    <i class="bi bi-key"></i>  --}}
                  </button>
                  <button type="button" class="ud-xs ud-xs-off"
                    onclick="udPost('{{ route('user_directory.toggle', $user->id) }}','warning','Status Toggled','Account status changed for {{ $user->name }}')">
                    <i class="bi bi-slash-circle"></i>
                  </button>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6">
                <div class="ud-empty"><i class="bi bi-inbox"></i>No users match your filter.</div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="ud-footer">
      <span class="info">Showing {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users</span>
      <div class="ud-pager">{{ $users->onEachSide(1)->links('pagination::bootstrap-5') }}</div>
    </div>
  </div>

  {{-- Toast --}}
  <div class="ud-toast-wrap" id="udToastWrap"></div>
{{-- Edit User Modal --}}
  <div class="ud-modal-overlay" id="udModalOverlay">
    <div class="ud-modal">
      <div class="ud-modal-hdr">
        <h6><i class="bi bi-pencil-square" style="color:#9A7B4F;"></i>Edit User</h6>
        <button type="button" class="ud-modal-x" onclick="udCloseEdit()"><i class="bi bi-x-lg"></i></button>
      </div>
      <div class="ud-modal-body">
        <input type="hidden" id="edit-id">

        <label class="ud-lbl">Name</label>
        <input type="text" class="ud-inp" id="edit-name">
        <div class="ud-err" id="err-name"></div>

        <label class="ud-lbl">Email</label>
        <input type="email" class="ud-inp" id="edit-email">
        <div class="ud-err" id="err-email"></div>

        <label class="ud-lbl">Role</label>
        <select class="ud-inp" id="edit-role"></select>
        <div class="ud-err" id="err-roleId"></div>

        <label class="ud-lbl">Password <span style="font-weight:400;color:var(--text-muted);">— leave blank to keep current</span></label>
        <input type="password" class="ud-inp" id="edit-password" autocomplete="new-password">
        <div class="ud-err" id="err-password"></div>

                {{-- Domain Expertise — non-SE roles --}}
        <div id="wrap-domains">
          <label class="ud-lbl">Domain Expertise</label>
          <div class="ud-domains" id="edit-domains"></div>
        </div>

        {{-- Categories — Service Engineer only --}}
        <div id="wrap-categories" style="display:none;">
          <label class="ud-lbl">Service Categories</label>
          <div class="ud-domains" id="edit-categories"></div>
        </div>

        
      </div>
      <div class="ud-modal-ftr">
        <button type="button" class="ud-btn ud-btn-ghost" onclick="udCloseEdit()">Cancel</button>
        <button type="button" class="ud-btn ud-btn-gold" id="udSaveBtn" onclick="udSaveEdit()">
          <i class="bi bi-check2"></i>Save Changes
        </button>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
function udToast(type, title, body){
  const w = document.getElementById('udToastWrap');
  const icons = { success:'bi-check-circle-fill', error:'bi-x-circle-fill', primary:'bi-info-circle-fill', warning:'bi-exclamation-circle-fill' };
  const t = document.createElement('div');
  t.className = 'ud-toast ' + type;
  t.innerHTML = '<i class="bi ' + (icons[type]||icons.primary) + ' ti-icon ' + type + '"></i>'
              + '<div><p class="ti-title">' + title + '</p><p class="ti-body">' + body + '</p></div>';
  w.appendChild(t);
  setTimeout(function(){ t.style.opacity='0'; t.style.transition='opacity .3s'; setTimeout(function(){ t.remove(); }, 300); }, 4000);
}





async function udPost(url, type, title, body){
  try{
    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }
    });
    if (res.ok){ udToast(type, title, body); setTimeout(function(){ location.reload(); }, 1000); }
    else { udToast('error','Failed','Something went wrong (' + res.status + ').'); }
  } catch(e){ udToast('error','Network Error','Could not reach the server.'); }
}

let udRoute = "{{ url('/user-directory') }}";
let udUpdateBase = "{{ url('/user-provisioning') }}";
let udCsrf = document.querySelector('meta[name="csrf-token"]').content;
let udSelectedDomains = new Set();

let udSelectedCats    = new Set();     // ← add
let udAllCats         = [];            // ← add

function udToggleRoleFields(){
  const code  = document.getElementById('edit-role').value;
  const wCats = document.getElementById('wrap-categories');
  const wDoms = document.getElementById('wrap-domains');

  if (wCats) wCats.style.display = code === 'SE' ? '' : 'none';
  if (wDoms) wDoms.style.display = code === 'ML' ? '' : 'none';
}

function udToggleCat(el){
  const id = Number(el.dataset.id);
  if (udSelectedCats.has(id)) { udSelectedCats.delete(id); el.classList.remove('on'); }
  else { udSelectedCats.add(id); el.classList.add('on'); }
}



async function udOpenEdit(id){
  try {
    const res = await fetch(`${udRoute}/${id}`, { headers: { 'Accept':'application/json' } });
    if (!res.ok) throw new Error(res.status);
    const data = await res.json();

    document.getElementById('edit-id').value = data.user.id;
    document.getElementById('edit-name').value = data.user.name;
    document.getElementById('edit-email').value = data.user.email;
    document.getElementById('edit-password').value = '';

    // roles
    const roleSel = document.getElementById('edit-role');
    roleSel.innerHTML = data.roles.map(r =>
      `<option value="${r.code}" ${r.code === data.user.roleId ? 'selected' : ''}>${r.name}</option>`
    ).join('');

    // domains
    udSelectedDomains = new Set(data.user.domains);
    const wrap = document.getElementById('edit-domains');
    wrap.innerHTML = data.domainCats.flatMap(c => c.skills).map(s =>
      `<span class="ud-dom-chip ${udSelectedDomains.has(s.id) ? 'on' : ''}" data-id="${s.id}" onclick="udToggleDomain(this)">${s.label}</span>`
    ).join('');


    // categories
    udAllCats = data.categories || [];
    udSelectedCats = new Set(data.user.categories || []);
    document.getElementById('edit-categories').innerHTML = udAllCats.map(c =>
      `<span class="ud-dom-chip ${udSelectedCats.has(c.id) ? 'on' : ''}" data-id="${c.id}" onclick="udToggleCat(this)">${c.name}</span>`
    ).join('');

    udToggleRoleFields();

    udClearErrors();
    document.getElementById('udModalOverlay').classList.add('show');
  } catch(e){
    udToast('error','Could not load','Failed to fetch user (' + e.message + ').');
  }
}

function udToggleDomain(el){
  const id = Number(el.dataset.id);
  if (udSelectedDomains.has(id)) { udSelectedDomains.delete(id); el.classList.remove('on'); }
  else { udSelectedDomains.add(id); el.classList.add('on'); }
}

function udCloseEdit(){ document.getElementById('udModalOverlay').classList.remove('show'); }

function udClearErrors(){ document.querySelectorAll('.ud-err').forEach(e => e.textContent = ''); }

async function udSaveEdit(){
  const id = document.getElementById('edit-id').value;
  const btn = document.getElementById('udSaveBtn');
  const roleSel = document.getElementById('edit-role');
    const isSE = roleSel.value === 'SE';

  btn.disabled = true;
  udClearErrors();

  // const payload = {
  //   name:     document.getElementById('edit-name').value,
  //   email:    document.getElementById('edit-email').value,
  //   roleId:   roleSel.value,
  //   role:     roleSel.options[roleSel.selectedIndex]?.text || '',
  //   domains:    isSE ? [] : [...udSelectedDomains],
  //   categories: isSE ? [...udSelectedCats] : [],
  //   fdGrants: [],
  // };


  const code = roleSel.value;

  const payload = {
    name:       document.getElementById('edit-name').value,
    email:      document.getElementById('edit-email').value,
    roleId:     code,
    role:       roleSel.options[roleSel.selectedIndex]?.text || '',
    domains:    code === 'ML' ? [...udSelectedDomains] : [],
    categories: code === 'SE' ? [...udSelectedCats]    : [],
    fdGrants:   [],
  };

  const pw = document.getElementById('edit-password').value;
  if (pw.trim() !== '') payload.password = pw;

  try {
    const res = await fetch(`${udUpdateBase}/${id}`, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': udCsrf, 'Accept':'application/json', 'Content-Type':'application/json' },
      body: JSON.stringify(payload),
    });

    if (res.status === 422){
      const { errors } = await res.json();
      Object.entries(errors).forEach(([field, msgs]) => {
        const el = document.getElementById('err-' + field);
        if (el) el.textContent = msgs[0];
      });
      btn.disabled = false;
      return;
    }
    if (!res.ok) throw new Error(res.status);

    udToast('success','Saved','User updated.');
    udCloseEdit();
    setTimeout(() => location.reload(), 900);
  } catch(e){
    udToast('error','Failed','Could not save (' + e.message + ').');
    btn.disabled = false;
  }
}

document.getElementById('udModalOverlay').addEventListener('click', function(e){
  if (e.target === this) udCloseEdit();
});

document.getElementById('edit-role')
        .addEventListener('change', udToggleRoleFields);

</script>
@endpush