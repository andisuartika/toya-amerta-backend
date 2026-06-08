@extends('layouts.vertical', ['title' => 'Kelola Pengguna'])

@section('content')
<div class="container-fluid">

    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Kelola Pengguna</h4>
            <p class="text-muted mb-0 fs-13">Kelola akun admin dan petugas</p>
        </div>
        <button class="btn btn-primary btn-sm px-3" id="btnTambah"
                data-bs-toggle="modal" data-bs-target="#modalUser">
            <i data-feather="plus" style="width:15px;height:15px;" class="me-1"></i>Tambah Pengguna
        </button>
    </div>

    {{-- Filter Bar --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 align-items-end">
                <div class="col-sm-3">
                    <label class="form-label mb-1 fs-13">Role</label>
                    <select name="role" class="form-select form-select-sm">
                        <option value="">Semua Role</option>
                        <option value="admin"    {{ request('role') === 'admin'    ? 'selected' : '' }}>Admin</option>
                        <option value="petugas"  {{ request('role') === 'petugas'  ? 'selected' : '' }}>Petugas</option>
                        <option value="pelanggan"{{ request('role') === 'pelanggan'? 'selected' : '' }}>Pelanggan</option>
                    </select>
                </div>
                <div class="col-sm-3">
                    <label class="form-label mb-1 fs-13">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="col-sm-auto">
                    <button type="submit" class="btn btn-primary btn-sm px-4"><i data-feather="filter" style="width:13px;height:13px;" class="me-1"></i>Filter</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm px-4 ms-1"><i data-feather="x" style="width:13px;height:13px;" class="me-1"></i>Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <input type="text" id="usersSearch" class="form-control form-control-sm w-auto"
                               placeholder="Cari pengguna..." style="min-width:220px">
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="usersTable">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>No. HP</th>
                                    <th class="text-center" style="width:100px">Role</th>
                                    <th class="text-center" style="width:90px">Status</th>
                                    <th class="text-end" style="width:100px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                <tr>
                                    <td class="fw-medium">{{ $user->name }}</td>
                                    <td class="text-muted">{{ $user->email }}</td>
                                    <td class="text-muted">{{ $user->phone ?? '—' }}</td>
                                    <td class="text-center">
                                        @if ($user->role === 'admin')
                                            <span class="badge bg-danger-subtle text-danger">Admin</span>
                                        @elseif ($user->role === 'petugas')
                                            <span class="badge bg-warning-subtle text-warning">Petugas</span>
                                        @else
                                            <span class="badge bg-info-subtle text-info">Pelanggan</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($user->is_active)
                                            <span class="badge bg-success-subtle text-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a class="btn btn-sm bg-primary-subtle me-1" role="button"
                                            data-bs-toggle="modal" data-bs-target="#modalUser"
                                            data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}"
                                            data-email="{{ $user->email }}"
                                            data-phone="{{ $user->phone }}"
                                            data-role="{{ $user->role }}"
                                            data-active="{{ $user->is_active ? '1' : '0' }}"
                                            title="Edit">
                                            <i data-feather="edit-2" style="width:14px;height:14px;" class="text-primary"></i>
                                        </a>
                                        <a class="btn btn-sm bg-danger-subtle btn-delete" role="button"
                                            data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}"
                                            title="Hapus">
                                            <i data-feather="trash-2" style="width:14px;height:14px;" class="text-danger"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Belum ada data pengguna</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Modal Form --}}
<div class="modal fade" id="modalUser" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formUser" method="POST">
                @csrf
                <div id="userMethod"></div>
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold" id="modalUserTitle">Tambah Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="userName" class="form-control" required
                               placeholder="Nama lengkap">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="userEmail" class="form-control" required
                               placeholder="email@contoh.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">No. HP</label>
                        <input type="text" name="phone" id="userPhone" class="form-control"
                               placeholder="cth: 081234567890">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Role <span class="text-danger">*</span></label>
                        <select name="role" id="userRole" class="form-select" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="admin">Admin</option>
                            <option value="petugas">Petugas</option>
                            <option value="pelanggan">Pelanggan</option>
                        </select>
                    </div>
                    <div class="mb-3" id="passwordField">
                        <label class="form-label fw-medium">Password <span class="text-danger" id="passwordRequired">*</span></label>
                        <input type="password" name="password" id="userPassword" class="form-control"
                               placeholder="Minimal 8 karakter">
                        <div class="form-text" id="passwordHint" style="display:none">
                            Kosongkan jika tidak ingin mengubah password.
                        </div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active"
                               id="userActive" value="1" checked>
                        <label class="form-check-label" for="userActive">Akun Aktif</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('admin.partials.modal-delete', [
    'entity'  => 'Pengguna',
    'warning' => 'Pengguna yang memiliki data terkait tidak dapat dihapus.',
])
@endsection

@section('script-bottom')
<script>
(function () {
    document.getElementById('usersSearch')?.addEventListener('input', function () {
        var q = this.value.toLowerCase();
        document.querySelectorAll('#usersTable tbody tr').forEach(function (row) {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });

    document.getElementById('btnTambah').addEventListener('click', function () {
        document.getElementById('modalUserTitle').textContent = 'Tambah Pengguna';
        document.getElementById('formUser').action = '{{ route('admin.users.store') }}';
        document.getElementById('userMethod').innerHTML = '';
        document.getElementById('formUser').reset();
        document.getElementById('userActive').checked = true;
        document.getElementById('userPassword').required = true;
        document.getElementById('passwordRequired').style.display = '';
        document.getElementById('passwordHint').style.display = 'none';
    });

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-bs-target="#modalUser"][data-id]');
        if (!btn) return;
        var d = btn.dataset;
        document.getElementById('modalUserTitle').textContent = 'Edit Pengguna';
        document.getElementById('formUser').action = '/admin/users/' + d.id;
        document.getElementById('userMethod').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('userName').value   = d.name;
        document.getElementById('userEmail').value  = d.email;
        document.getElementById('userPhone').value  = d.phone || '';
        document.getElementById('userRole').value   = d.role;
        document.getElementById('userPassword').value = '';
        document.getElementById('userPassword').required = false;
        document.getElementById('passwordRequired').style.display = 'none';
        document.getElementById('passwordHint').style.display = '';
        document.getElementById('userActive').checked = d.active === '1';
    });

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-delete');
        if (!btn) return;
        document.getElementById('deleteItemName').textContent = btn.dataset.name;
        document.getElementById('formDelete').action = '/admin/users/' + btn.dataset.id;
        new bootstrap.Modal(document.getElementById('modalDelete')).show();
    });
}());
</script>
@endsection
