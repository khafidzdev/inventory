@extends('templates.dashboard')
@section('content')
      <main role="main" class="main-content">
        <div class="container-fluid">
          <div class="row justify-content-center">
            <div class="col-12">
              <h2 class="mb-2 page-title font-weight-normal">Data User</h2>
              <p class="card-text text-muted">Daftar pengguna dan manajemen akun staff dan kasir sistem inventory.</p>

              <!-- Flash Messages -->
              @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                  <i class="fe fe-check-circle mr-2"></i>{{ session('success') }}
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
              @endif

              @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <i class="fe fe-alert-triangle mr-2"></i>{{ session('error') }}
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
              @endif

              @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <div class="mb-1"><i class="fe fe-alert-circle mr-2"></i>Terjadi kesalahan pengisian formulir:</div>
                  <ul class="mb-0 pl-3">
                    @foreach($errors->all() as $err)
                      <li>{{ $err }}</li>
                    @endforeach
                  </ul>
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
              @endif

              <div class="row my-4">
                <div class="col-md-12">
                  <div class="card shadow">
                    <div class="card-body">

                      <!-- Header Bagian Atas Tabel & Tombol Add -->
                      <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                          <h4 class="card-title my-0 font-weight-normal">Tabel User</h4>
                          <p class="card-text text-muted mb-0 small">Kelola data seluruh akun pengguna dan hak akses sistem.</p>
                        </div>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addModal">
                          <i class="fe fe-plus fe-12 mr-1"></i>Add
                        </button>
                      </div>

                      <style>
                        /* Garis tepi luar tabel */
                        #dataTable-1 {
                          border: 1px solid #dee2e6 !important;
                          border-collapse: collapse !important;
                        }

                        /* Header tabel berwarna abu-abu (gray) */
                        #dataTable-1 thead th {
                          background-color: #e9ecef !important;
                          border: 1px solid #dee2e6 !important;
                          color: #212529 !important;
                          font-weight: 400 !important;
                          vertical-align: middle;
                        }

                        /* Garis pemisah vertikal antar kolom penuh dari atas ke bawah */
                        #dataTable-1 th,
                        #dataTable-1 td {
                          border: 1px solid #dee2e6 !important;
                          vertical-align: middle;
                        }

                        /* Teks isi tabel sama rata hitam biasa tidak bold */
                        #dataTable-1 tbody td {
                          color: #212529 !important;
                          font-weight: 400 !important;
                        }

                        /* Badge Role diperbesar */
                        .badge-role {
                          font-size: 0.85rem !important;
                          padding: 0.45rem 0.85rem !important;
                          font-weight: 500 !important;
                          letter-spacing: 0.2px;
                          border-radius: 4px;
                        }

                        /* Badge Status */
                        .badge-status {
                          font-size: 0.82rem !important;
                          padding: 0.4rem 0.8rem !important;
                          font-weight: 500 !important;
                          border-radius: 4px;
                        }

                        /* Tombol aksi solid tanpa outline */
                        .btn-edit,
                        .btn-delete {
                          border: none !important;
                        }
                      </style>

                      <!-- Table Datatables -->
                      <table class="table datatables table-bordered" id="dataTable-1">
                        <thead class="thead-light">
                          <tr>
                            <th style="width: 50px;" class="text-center">No</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th class="text-center">Role</th>
                            <th>No. Telepon</th>
                            <th class="text-center">Status</th>
                            <th style="width: 170px;" class="text-center">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          @forelse($users as $index => $u)
                            <tr>
                              <td class="text-center">{{ $index + 1 }}</td>
                              <td>{{ $u->name }}</td>
                              <td>{{ $u->username }}</td>
                              <td>{{ $u->email }}</td>
                              <td class="text-center">
                                @if($u->role === 'warehouse_staff')
                                  <span class="badge badge-info badge-role text-white">Staff Gudang</span>
                                @elseif($u->role === 'cashier')
                                  <span class="badge badge-warning badge-role text-dark">Kasir</span>
                                @else
                                  <span class="badge badge-secondary badge-role">{{ $u->role }}</span>
                                @endif
                              </td>
                              <td>{{ $u->phone ?? '-' }}</td>
                              <td class="text-center">
                                @if($u->is_active)
                                  <span class="badge badge-success badge-status text-white">Aktif</span>
                                @else
                                  <span class="badge badge-secondary badge-status">Nonaktif</span>
                                @endif
                              </td>
                              <td class="text-center">
                                <button type="button" 
                                        class="btn btn-sm btn-primary btn-edit mr-1" 
                                        data-toggle="modal" 
                                        data-target="#editModal"
                                        data-id="{{ $u->id }}"
                                        data-name="{{ $u->name }}"
                                        data-username="{{ $u->username }}"
                                        data-email="{{ $u->email }}"
                                        data-role="{{ $u->role }}"
                                        data-phone="{{ $u->phone ?? '' }}"
                                        data-active="{{ $u->is_active ? '1' : '0' }}">
                                  <i class="fe fe-edit fe-12 mr-1"></i>Edit
                                </button>
                                <button type="button" 
                                        class="btn btn-sm btn-danger btn-delete" 
                                        data-toggle="modal" 
                                        data-target="#deleteModal"
                                        data-id="{{ $u->id }}"
                                        data-name="{{ $u->name }}">
                                  <i class="fe fe-trash-2 fe-12 mr-1"></i>Delete
                                </button>
                              </td>
                            </tr>
                          @empty
                            <tr>
                              <td colspan="8" class="text-center text-muted py-4">Belum ada data user.</td>
                            </tr>
                          @endforelse
                        </tbody>
                      </table>

                    </div>
                  </div>
                </div> <!-- simple table -->
              </div> <!-- end section -->
            </div> <!-- .col-12 -->
          </div> <!-- .row -->
        </div> <!-- .container-fluid -->

        <!-- Modal Add User -->
        <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title font-weight-normal" id="addModalLabel">Tambah Data User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                  <div class="form-group">
                    <label for="addName">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="addName" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required maxlength="150">
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="addUsername">Username <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="addUsername" name="username" value="{{ old('username') }}" placeholder="Contoh: kasir1" required maxlength="50">
                    </div>
                    <div class="form-group col-md-6">
                      <label for="addEmail">Email <span class="text-danger">*</span></label>
                      <input type="email" class="form-control" id="addEmail" name="email" value="{{ old('email') }}" placeholder="user@inventory.com" required maxlength="100">
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="addPassword">Password <span class="text-danger">*</span></label>
                      <input type="password" class="form-control" id="addPassword" name="password" placeholder="Minimal 6 karakter" required minlength="6">
                    </div>
                    <div class="form-group col-md-6">
                      <label for="addPhone">No. Telepon</label>
                      <input type="text" class="form-control" id="addPhone" name="phone" value="{{ old('phone') }}" placeholder="Contoh: 081234567890 (opsional)" maxlength="20">
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="addRole">Role <span class="text-danger">*</span></label>
                      <select class="form-control" id="addRole" name="role" required>
                        <option value="warehouse_staff" {{ old('role') === 'warehouse_staff' ? 'selected' : '' }}>Staff Gudang</option>
                        <option value="cashier" {{ old('role') === 'cashier' || !old('role') ? 'selected' : '' }}>Kasir</option>
                      </select>
                    </div>
                    <div class="form-group col-md-6">
                      <label for="addIsActive">Status <span class="text-danger">*</span></label>
                      <select class="form-control" id="addIsActive" name="is_active" required>
                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                  <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Modal Edit User -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title font-weight-normal" id="editModalLabel">Edit Data User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form id="editUserForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                  <div class="form-group">
                    <label for="editName">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="editName" name="name" required maxlength="150">
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="editUsername">Username <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="editUsername" name="username" required maxlength="50">
                    </div>
                    <div class="form-group col-md-6">
                      <label for="editEmail">Email <span class="text-danger">*</span></label>
                      <input type="email" class="form-control" id="editEmail" name="email" required maxlength="100">
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="editPassword">Password Baru</label>
                      <input type="password" class="form-control" id="editPassword" name="password" placeholder="Kosongkan jika tetap" minlength="6">
                      <small class="form-text text-muted">Isi hanya jika ingin mengganti password.</small>
                    </div>
                    <div class="form-group col-md-6">
                      <label for="editPhone">No. Telepon</label>
                      <input type="text" class="form-control" id="editPhone" name="phone" placeholder="Contoh: 081234567890 (opsional)" maxlength="20">
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="editRole">Role <span class="text-danger">*</span></label>
                      <select class="form-control" id="editRole" name="role" required>
                        <option value="warehouse_staff">Staff Gudang</option>
                        <option value="cashier">Kasir</option>
                      </select>
                    </div>
                    <div class="form-group col-md-6">
                      <label for="editIsActive">Status <span class="text-danger">*</span></label>
                      <select class="form-control" id="editIsActive" name="is_active" required>
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                  <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Modal Konfirmasi Delete User -->
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title font-weight-normal" id="deleteModalLabel">Hapus User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form id="deleteUserForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="modal-body text-center py-4">
                  <i class="fe fe-alert-triangle fe-32 text-danger mb-3 d-inline-block"></i>
                  <p class="mb-1">Apakah Anda yakin ingin menghapus user:</p>
                  <div class="text-dark mb-2" id="deleteUserName"></div>
                  <p class="text-muted small mb-0">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer justify-content-center">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                  <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
              </form>
            </div>
          </div>
        </div>

      </main> <!-- main -->

      <script>
        document.addEventListener('DOMContentLoaded', function () {
          // Event delegasi untuk tombol Edit agar data baris otomatis terisi ke modal Edit
          $(document).on('click', '.btn-edit', function () {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var username = $(this).data('username');
            var email = $(this).data('email');
            var role = $(this).data('role');
            var phone = $(this).data('phone');
            var active = $(this).data('active');

            var updateUrl = "{{ url('/admin/users') }}/" + id;
            $('#editUserForm').attr('action', updateUrl);

            $('#editName').val(name);
            $('#editUsername').val(username);
            $('#editEmail').val(email);
            $('#editPassword').val('');
            $('#editRole').val(role);
            $('#editPhone').val(phone);
            $('#editIsActive').val(active == '1' || active == 1 ? '1' : '0');
          });

          // Event delegasi untuk tombol Delete agar URL dan nama user terisi ke modal Delete
          $(document).on('click', '.btn-delete', function () {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var deleteUrl = "{{ url('/admin/users') }}/" + id;

            $('#deleteUserForm').attr('action', deleteUrl);
            $('#deleteUserName').text(name);
          });
        });
      </script>
@endsection
