@extends('templates.dashboard')
@section('content')
      <main role="main" class="main-content">
        <div class="container-fluid">
          <div class="row justify-content-center">
            <div class="col-12">
              <h2 class="mb-2 page-title font-weight-bold" style="color: #1b68ff;">Data Satuan</h2>

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
                          <h5 class="card-title my-0 font-weight-bold">Daftar Satuan</h5>
                        </div>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addModal">
                          <i class="fe fe-plus fe-12 mr-1"></i>Tambah
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

                        /* Teks isi tabel dan kode sama rata hitam biasa tidak bold */
                        #dataTable-1 tbody td,
                        #dataTable-1 code {
                          color: #212529 !important;
                          font-weight: 400 !important;
                          background: transparent !important;
                          font-family: inherit !important;
                        }

                        /* Badge Status & Satuan */
                        .badge-status {
                          font-size: 0.82rem !important;
                          padding: 0.35rem 0.75rem !important;
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
                      <div class="table-responsive">
                        <table class="table datatables table-bordered" id="dataTable-1">
                          <thead class="thead-light">
                            <tr>
                              <th style="width: 50px;" class="text-center">No</th>
                              <th>Kode / Simbol</th>
                              <th>Nama Satuan</th>
                              <th class="text-center">Jumlah Produk</th>
                              <th style="width: 170px;" class="text-center">Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            @forelse($units as $index => $u)
                              <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $u->code }}</td>
                                <td>{{ $u->name }}</td>
                                <td class="text-center">{{ $u->products_count }} item</td>
                                <td class="text-center">
                                  <button type="button" 
                                          class="btn btn-sm btn-primary btn-edit mr-1" 
                                          data-toggle="modal" 
                                          data-target="#editModal"
                                          data-id="{{ $u->id }}"
                                          data-code="{{ $u->code }}"
                                          data-name="{{ $u->name }}">
                                    <i class="fe fe-edit fe-12 mr-1"></i>Edit
                                  </button>
                                  <button type="button" 
                                          class="btn btn-sm btn-danger btn-delete" 
                                          data-toggle="modal" 
                                          data-target="#deleteModal"
                                          data-id="{{ $u->id }}"
                                          data-name="{{ $u->name }}"
                                          data-products-count="{{ $u->products_count }}">
                                    <i class="fe fe-trash-2 fe-12 mr-1"></i>Delete
                                  </button>
                                </td>
                              </tr>
                            @empty
                              <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada data satuan.</td>
                              </tr>
                            @endforelse
                          </tbody>
                        </table>
                      </div>

                    </div>
                  </div>
                </div> <!-- simple table -->
              </div> <!-- end section -->
            </div> <!-- .col-12 -->
          </div> <!-- .row -->
        </div> <!-- .container-fluid -->

        <!-- Modal Add Unit -->
        <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title font-weight-normal" id="addModalLabel">Tambah Data Satuan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form action="{{ route('units.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                  <div class="form-group">
                    <label for="addCode">Kode / Simbol Satuan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="addCode" name="code" value="{{ old('code') }}" placeholder="Contoh: PCS, BOX, KG, LUSIN" required maxlength="10">
                    <small class="form-text text-muted">Akan otomatis disimpan dalam huruf besar.</small>
                  </div>
                  <div class="form-group">
                    <label for="addName">Nama Satuan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="addName" name="name" value="{{ old('name') }}" placeholder="Contoh: Pieces, Box, Kilogram" required maxlength="50">
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

        <!-- Modal Edit Unit -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title font-weight-normal" id="editModalLabel">Edit Data Satuan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form id="editUnitForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                  <div class="form-group">
                    <label for="editCode">Kode / Simbol Satuan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="editCode" name="code" required maxlength="10">
                  </div>
                  <div class="form-group">
                    <label for="editName">Nama Satuan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="editName" name="name" required maxlength="50">
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

        <!-- Modal Konfirmasi Delete Unit -->
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title font-weight-normal" id="deleteModalLabel">Hapus Satuan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form id="deleteUnitForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="modal-body text-center py-4">
                  <i class="fe fe-alert-triangle fe-32 text-danger mb-3 d-inline-block"></i>
                  <p class="mb-1">Apakah Anda yakin ingin menghapus satuan:</p>
                  <div class="text-dark mb-2" id="deleteUnitName"></div>
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
          // Event delegasi untuk tombol Edit agar data terisi ke modal Edit
          $(document).on('click', '.btn-edit', function () {
            var id = $(this).data('id');
            var code = $(this).data('code');
            var name = $(this).data('name');

            var updateUrl = "{{ url('/units') }}/" + id;
            $('#editUnitForm').attr('action', updateUrl);

            $('#editCode').val(code);
            $('#editName').val(name);
          });

          // Event delegasi untuk tombol Delete agar URL dan nama satuan terisi ke modal Delete
          $(document).on('click', '.btn-delete', function () {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var count = $(this).data('products-count');

            if (count > 0) {
              alert('Satuan ini sedang digunakan oleh ' + count + ' produk dan tidak dapat dihapus.');
              return;
            }

            var deleteUrl = "{{ url('/units') }}/" + id;
            $('#deleteUnitForm').attr('action', deleteUrl);
            $('#deleteUnitName').text(name);
            $('#deleteModal').modal('show');
          });
        });
      </script>
@endsection
