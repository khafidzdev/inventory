@extends('templates.dashboard')
@section('content')
      <main role="main" class="main-content">
        <div class="container-fluid">
          <div class="row justify-content-center">
            <div class="col-12">
              <h2 class="mb-2 page-title font-weight-bold" style="color: #1b68ff;">Data Produk</h2>

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
                          <h5 class="card-title my-0 font-weight-bold">Daftar Produk</h5>
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

                        /* Thumbnail Gambar Produk */
                        .product-img-thumb {
                          width: 44px;
                          height: 44px;
                          object-fit: cover;
                          border-radius: 6px;
                          border: 1px solid #dee2e6;
                        }

                        .product-img-placeholder {
                          width: 44px;
                          height: 44px;
                          display: inline-flex;
                          align-items: center;
                          justify-content: center;
                          background-color: #f8f9fa;
                          border: 1px solid #dee2e6;
                          border-radius: 6px;
                          color: #adb5bd;
                        }

                        /* Badge Status & Kategori disamakan */
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
                              <th style="width: 40px;" class="text-center">No</th>
                              <th style="width: 60px;" class="text-center">Gambar</th>
                              <th>SKU</th>
                              <th>Barcode</th>
                              <th>Nama Produk</th>
                              <th class="text-center">Kategori</th>
                              <th>Satuan</th>
                              <th class="text-right">Harga Beli</th>
                              <th class="text-right">Harga Jual</th>
                              <th class="text-center">Stok</th>
                              <th class="text-center">Min. Stok</th>
                              <th class="text-center">Status</th>
                              <th style="width: 170px;" class="text-center">Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            @forelse($products as $index => $p)
                              <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center">
                                  @if($p->image)
                                    <img src="{{ asset('storage/' . $p->image) }}" alt="{{ $p->name }}" class="product-img-thumb">
                                  @else
                                    <span class="product-img-placeholder">
                                      <i class="fe fe-image fe-16"></i>
                                    </span>
                                  @endif
                                </td>
                                <td>{{ $p->sku }}</td>
                                <td>{{ $p->barcode ?: '-' }}</td>
                                <td>{{ $p->name }}</td>
                                <td class="text-center">
                                  <span class="badge badge-info badge-status text-white">
                                    {{ $p->category->name ?? '-' }}
                                  </span>
                                </td>
                                <td>{{ $p->unit->name ?? '-' }} ({{ $p->unit->code ?? '-' }})</td>
                                <td class="text-right">Rp {{ number_format($p->purchase_price, 0, ',', '.') }}</td>
                                <td class="text-right">Rp {{ number_format($p->selling_price, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $p->stock }}</td>
                                <td class="text-center">{{ $p->minimum_stock }}</td>
                                <td class="text-center">
                                  @if($p->is_active)
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
                                          data-id="{{ $p->id }}"
                                          data-category-id="{{ $p->category_id }}"
                                          data-unit-id="{{ $p->unit_id }}"
                                          data-sku="{{ $p->sku }}"
                                          data-barcode="{{ $p->barcode ?? '' }}"
                                          data-name="{{ $p->name }}"
                                          data-purchase-price="{{ $p->purchase_price }}"
                                          data-selling-price="{{ $p->selling_price }}"
                                          data-stock="{{ $p->stock }}"
                                          data-minimum-stock="{{ $p->minimum_stock }}"
                                          data-image="{{ $p->image ? asset('storage/' . $p->image) : '' }}"
                                          data-active="{{ $p->is_active ? '1' : '0' }}">
                                    <i class="fe fe-edit fe-12 mr-1"></i>Edit
                                  </button>
                                  <button type="button" 
                                          class="btn btn-sm btn-danger btn-delete" 
                                          data-toggle="modal" 
                                          data-target="#deleteModal"
                                          data-id="{{ $p->id }}"
                                          data-name="{{ $p->name }}">
                                    <i class="fe fe-trash-2 fe-12 mr-1"></i>Delete
                                  </button>
                                </td>
                              </tr>
                            @empty
                              <tr>
                                <td colspan="13" class="text-center text-muted py-4">Belum ada data produk.</td>
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

        <!-- Modal Add Product -->
        <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title font-weight-normal" id="addModalLabel">Tambah Data Produk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                  <div class="row">
                    <!-- Form Sisi Kiri -->
                    <div class="col-lg-8 pr-lg-4">
                      <div class="form-row">
                        <div class="form-group col-md-6">
                          <label for="addSku">SKU (Kode Unik Produk) <span class="text-danger">*</span></label>
                          <input type="text" class="form-control" id="addSku" name="sku" value="{{ old('sku') }}" placeholder="Contoh: PRD-001" required maxlength="64">
                        </div>
                        <div class="form-group col-md-6">
                          <label for="addBarcode">Barcode</label>
                          <input type="text" class="form-control" id="addBarcode" name="barcode" value="{{ old('barcode') }}" placeholder="Contoh: 899123456001 (opsional)" maxlength="64">
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="addName">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="addName" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap produk" required maxlength="255">
                      </div>
                      <div class="form-row">
                        <div class="form-group col-md-6">
                          <label for="addCategory">Kategori <span class="text-danger">*</span></label>
                          <select class="form-control" id="addCategory" name="category_id" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $c)
                              <option value="{{ $c->id }}" {{ old('category_id') === $c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->code }})
                              </option>
                            @endforeach
                          </select>
                        </div>
                        <div class="form-group col-md-6">
                          <label for="addUnit">Satuan <span class="text-danger">*</span></label>
                          <select class="form-control" id="addUnit" name="unit_id" required>
                            <option value="">-- Pilih Satuan --</option>
                            @foreach($units as $u)
                              <option value="{{ $u->id }}" {{ old('unit_id') === $u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ $u->code }})
                              </option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                      <div class="form-row">
                        <div class="form-group col-md-6">
                          <label for="addPurchasePrice">Harga Beli (Rp) <span class="text-danger">*</span></label>
                          <input type="number" step="0.01" min="0" class="form-control" id="addPurchasePrice" name="purchase_price" value="{{ old('purchase_price', 0) }}" required>
                        </div>
                        <div class="form-group col-md-6">
                          <label for="addSellingPrice">Harga Jual (Rp) <span class="text-danger">*</span></label>
                          <input type="number" step="0.01" min="0" class="form-control" id="addSellingPrice" name="selling_price" value="{{ old('selling_price', 0) }}" required>
                        </div>
                      </div>
                      <div class="form-row">
                        <div class="form-group col-md-6">
                          <label for="addStock">Stok Saat Ini <span class="text-danger">*</span></label>
                          <input type="number" step="1" min="0" class="form-control" id="addStock" name="stock" value="{{ old('stock', 0) }}" required>
                        </div>
                        <div class="form-group col-md-6">
                          <label for="addMinStock">Stok Minimum <span class="text-danger">*</span></label>
                          <input type="number" step="1" min="0" class="form-control" id="addMinStock" name="minimum_stock" value="{{ old('minimum_stock', 0) }}" required>
                        </div>
                      </div>
                      <div class="form-group mb-0">
                        <label for="addIsActive">Status Produk <span class="text-danger">*</span></label>
                        <select class="form-control" id="addIsActive" name="is_active" required>
                          <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                          <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                      </div>
                    </div>

                    <!-- Gambar Kanan Sendiri -->
                    <div class="col-lg-4 border-left pl-lg-4 d-flex flex-column align-items-center justify-content-start pt-2">
                      <label class="w-100 font-weight-normal text-muted mb-2">Gambar Produk</label>
                      <div class="w-100 text-center p-3 mb-3 border rounded bg-light" style="min-height: 220px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                        <img id="addImagePreview" src="" alt="Pratinjau Gambar" style="max-width: 160px; max-height: 160px; object-fit: contain; display: none;" class="rounded shadow-sm">
                        <div id="addImagePlaceholder" class="text-muted">
                          <i class="fe fe-image fe-32 d-block mb-2 text-secondary"></i>
                          <small>Pratinjau Gambar Baru</small>
                        </div>
                      </div>
                      <div class="custom-file w-100 mb-2">
                        <input type="file" class="custom-file-input" id="addImage" name="image" accept="image/*">
                        <label class="custom-file-label text-truncate" for="addImage">Pilih file gambar...</label>
                      </div>
                      <small class="form-text text-muted text-center">Format: JPG, PNG, WEBP, SVG (Maks. 2MB).</small>
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

        <!-- Modal Edit Product -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title font-weight-normal" id="editModalLabel">Edit Data Produk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form id="editProductForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                  <div class="row">
                    <!-- Form Sisi Kiri -->
                    <div class="col-lg-8 pr-lg-4">
                      <div class="form-row">
                        <div class="form-group col-md-6">
                          <label for="editSku">SKU (Kode Unik Produk) <span class="text-danger">*</span></label>
                          <input type="text" class="form-control" id="editSku" name="sku" required maxlength="64">
                        </div>
                        <div class="form-group col-md-6">
                          <label for="editBarcode">Barcode</label>
                          <input type="text" class="form-control" id="editBarcode" name="barcode" maxlength="64">
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="editName">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editName" name="name" required maxlength="255">
                      </div>
                      <div class="form-row">
                        <div class="form-group col-md-6">
                          <label for="editCategory">Kategori <span class="text-danger">*</span></label>
                          <select class="form-control" id="editCategory" name="category_id" required>
                            @foreach($categories as $c)
                              <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }})</option>
                            @endforeach
                          </select>
                        </div>
                        <div class="form-group col-md-6">
                          <label for="editUnit">Satuan <span class="text-danger">*</span></label>
                          <select class="form-control" id="editUnit" name="unit_id" required>
                            @foreach($units as $u)
                              <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->code }})</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                      <div class="form-row">
                        <div class="form-group col-md-6">
                          <label for="editPurchasePrice">Harga Beli (Rp) <span class="text-danger">*</span></label>
                          <input type="number" step="0.01" min="0" class="form-control" id="editPurchasePrice" name="purchase_price" required>
                        </div>
                        <div class="form-group col-md-6">
                          <label for="editSellingPrice">Harga Jual (Rp) <span class="text-danger">*</span></label>
                          <input type="number" step="0.01" min="0" class="form-control" id="editSellingPrice" name="selling_price" required>
                        </div>
                      </div>
                      <div class="form-row">
                        <div class="form-group col-md-6">
                          <label for="editStock">Stok Saat Ini <span class="text-danger">*</span></label>
                          <input type="number" step="1" min="0" class="form-control" id="editStock" name="stock" required>
                        </div>
                        <div class="form-group col-md-6">
                          <label for="editMinStock">Stok Minimum <span class="text-danger">*</span></label>
                          <input type="number" step="1" min="0" class="form-control" id="editMinStock" name="minimum_stock" required>
                        </div>
                      </div>
                      <div class="form-group mb-0">
                        <label for="editIsActive">Status Produk <span class="text-danger">*</span></label>
                        <select class="form-control" id="editIsActive" name="is_active" required>
                          <option value="1">Aktif</option>
                          <option value="0">Nonaktif</option>
                        </select>
                      </div>
                    </div>

                    <!-- Gambar Kanan Sendiri -->
                    <div class="col-lg-4 border-left pl-lg-4 d-flex flex-column align-items-center justify-content-start pt-2">
                      <label class="w-100 font-weight-normal text-muted mb-2">Gambar Produk</label>
                      <div class="w-100 text-center p-3 mb-3 border rounded bg-light" style="min-height: 220px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                        <img id="editImagePreview" src="" alt="Pratinjau Gambar" style="max-width: 160px; max-height: 160px; object-fit: contain; display: none;" class="rounded shadow-sm">
                        <div id="editImagePlaceholder" class="text-muted">
                          <i class="fe fe-image fe-32 d-block mb-2 text-secondary"></i>
                          <small>Tidak ada gambar</small>
                        </div>
                      </div>
                      <div class="custom-file w-100 mb-2">
                        <input type="file" class="custom-file-input" id="editImage" name="image" accept="image/*">
                        <label class="custom-file-label text-truncate" for="editImage">Ganti file gambar...</label>
                      </div>
                      <small class="form-text text-muted text-center">Biarkan kosong jika tidak ingin mengubah gambar.</small>
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

        <!-- Modal Konfirmasi Delete Product -->
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title font-weight-normal" id="deleteModalLabel">Hapus Produk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form id="deleteProductForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="modal-body text-center py-4">
                  <i class="fe fe-alert-triangle fe-32 text-danger mb-3 d-inline-block"></i>
                  <p class="mb-1">Apakah Anda yakin ingin menghapus produk:</p>
                  <div class="text-dark mb-2" id="deleteProductName"></div>
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
          // Preview gambar saat memilih file di modal Add
          $('#addImage').on('change', function (e) {
            var file = e.target.files[0];
            var fileName = file ? file.name : 'Pilih file gambar...';
            $(this).next('.custom-file-label').html(fileName);

            if (file) {
              var reader = new FileReader();
              reader.onload = function (event) {
                $('#addImagePreview').attr('src', event.target.result).show();
                $('#addImagePlaceholder').hide();
              };
              reader.readAsDataURL(file);
            } else {
              $('#addImagePreview').attr('src', '').hide();
              $('#addImagePlaceholder').show();
            }
          });

          // Preview gambar saat memilih file di modal Edit
          $('#editImage').on('change', function (e) {
            var file = e.target.files[0];
            var fileName = file ? file.name : 'Ganti file gambar...';
            $(this).next('.custom-file-label').html(fileName);

            if (file) {
              var reader = new FileReader();
              reader.onload = function (event) {
                $('#editImagePreview').attr('src', event.target.result).show();
                $('#editImagePlaceholder').hide();
              };
              reader.readAsDataURL(file);
            }
          });

          // Event delegasi untuk tombol Edit agar data produk terisi otomatis ke modal Edit
          $(document).on('click', '.btn-edit', function () {
            var id = $(this).data('id');
            var categoryId = $(this).data('category-id');
            var unitId = $(this).data('unit-id');
            var sku = $(this).data('sku');
            var barcode = $(this).data('barcode');
            var name = $(this).data('name');
            var purchasePrice = $(this).data('purchase-price');
            var sellingPrice = $(this).data('selling-price');
            var stock = $(this).data('stock');
            var minStock = $(this).data('minimum-stock');
            var image = $(this).data('image');
            var active = $(this).data('active');

            var updateUrl = "{{ url('/products') }}/" + id;
            $('#editProductForm').attr('action', updateUrl);

            $('#editCategory').val(categoryId);
            $('#editUnit').val(unitId);
            $('#editSku').val(sku);
            $('#editBarcode').val(barcode);
            $('#editName').val(name);
            $('#editPurchasePrice').val(purchasePrice);
            $('#editSellingPrice').val(sellingPrice);
            $('#editStock').val(stock);
            $('#editMinStock').val(minStock);
            $('#editIsActive').val(active == '1' || active == 1 ? '1' : '0');

            // Reset input file & label
            $('#editImage').val('');
            $('#editImage').next('.custom-file-label').html('Ganti file gambar...');

            // Tampilkan preview gambar saat ini jika ada
            if (image && image.trim() !== '') {
              $('#editImagePreview').attr('src', image).show();
              $('#editImagePlaceholder').hide();
            } else {
              $('#editImagePreview').attr('src', '').hide();
              $('#editImagePlaceholder').show();
            }
          });

          // Event delegasi untuk tombol Delete agar URL dan nama produk terisi ke modal Delete
          $(document).on('click', '.btn-delete', function () {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var deleteUrl = "{{ url('/products') }}/" + id;

            $('#deleteProductForm').attr('action', deleteUrl);
            $('#deleteProductName').text(name);
          });
        });
      </script>
@endsection
