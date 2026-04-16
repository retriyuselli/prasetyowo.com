<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Pribadi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/datacrew/datacrew.css') }}" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans', system-ui, -apple-system, Segoe UI, Roboto, Oxygen, Ubuntu, Cantarell, 'Fira Sans', 'Droid Sans', 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #f7fafc 0%, #eef2ff 100%);
        }

        .form-card {
            border-radius: 1rem;
            border: 1px solid #e5e7eb;
        }

        .form-card .card-header {
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
        }

        .card-header .title-wrap {
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .card-header .subtitle {
            opacity: .9;
            font-size: .9rem;
        }

        .step-indicator .nav-link {
            border: 1px solid #e5e7eb;
            border-radius: 9999px;
            padding: .4rem .9rem;
            color: #374151;
        }

        .step-indicator .nav-link.active {
            background-color: #eef2ff;
            color: #3730a3;
            border-color: #c7d2fe;
            font-weight: 600;
        }

        .input-group-text {
            background-color: #f3f4f6;
            border-color: #e5e7eb;
        }

        .form-control,
        .form-select,
        textarea {
            border-color: #e5e7eb;
            padding: .6rem .8rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border: none;
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.2);
        }

        .btn-primary:hover {
            filter: brightness(1.05);
        }

        .foto-preview-wrap {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
        }

        .foto-preview-wrap img {
            border-radius: .75rem;
            border: 1px solid #e5e7eb;
        }
    </style>
    <meta http-equiv="Cache-Control" content="no-store" />
</head>

<body>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="container py-4 py-md-5">
        <div class="card form-card shadow-lg">
            <div class="card-header px-4 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="title-wrap">
                        <i class="bi bi-pencil-square fs-3"></i>
                        <div>
                            <div class="h4 mb-0">Edit Data Pribadi</div>
                            <div class="subtitle">Perbarui informasi Anda</div>
                        </div>
                    </div>
                    <a href="{{ route('data-pribadi.create') }}" class="btn btn-light btn-sm rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body p-4 p-md-5">
                <form action="{{ $updateUrl }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <div class="h5 mb-3">Profil</div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" id="nama_lengkap" name="nama_lengkap"
                                        value="{{ old('nama_lengkap', $dataPribadi->nama_lengkap) }}" required>
                                    @error('nama_lengkap')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                                        value="{{ old('email', $dataPribadi->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                        <hr class="my-4">
                        <div class="h5 mb-3">Kontak</div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nomor_telepon" class="form-label">Nomor Telepon</label>
                                    <div class="input-group">
                                        <span class="input-group-text">+62</span>
                                        <input type="tel" class="form-control @error('nomor_telepon') is-invalid @enderror" id="nomor_telepon" name="nomor_telepon"
                                            value="{{ old('nomor_telepon', $dataPribadi->nomor_telepon) }}" required>
                                    </div>
                                    @error('nomor_telepon')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                                    <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" id="tanggal_lahir" name="tanggal_lahir"
                                        value="{{ old('tanggal_lahir', optional($dataPribadi->tanggal_lahir)->format('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                                    @error('tanggal_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="no_rekening" class="form-label">No. Rekening</label>
                                    <input type="text" inputmode="numeric" autocomplete="off"
                                        class="form-control @error('no_rekening') is-invalid @enderror" id="no_rekening" name="no_rekening"
                                        value="{{ old('no_rekening', $dataPribadi->no_rekening) }}" placeholder="Masukkan nomor rekening" required>
                                    @error('no_rekening')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="bank_name" class="form-label">Nama Bank</label>
                                    <input type="text" class="form-control @error('bank_name') is-invalid @enderror" id="bank_name" name="bank_name"
                                        value="{{ old('bank_name', $dataPribadi->bank_name) }}" placeholder="Contoh: BCA" required>
                                    @error('bank_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                        <hr class="my-4">
                        <div class="h5 mb-3">Detail</div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                    <select class="form-select @error('jenis_kelamin') is-invalid @enderror" id="jenis_kelamin" name="jenis_kelamin" required>
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="Laki-laki" {{ old('jenis_kelamin', $dataPribadi->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="Perempuan" {{ old('jenis_kelamin', $dataPribadi->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    @error('jenis_kelamin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="pekerjaan" class="form-label">Pekerjaan</label>
                                    <input type="text" class="form-control @error('pekerjaan') is-invalid @enderror" id="pekerjaan" name="pekerjaan"
                                        value="{{ old('pekerjaan', $dataPribadi->pekerjaan) }}" required>
                                    @error('pekerjaan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="alamat" class="form-label">Alamat</label>
                                    <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3" required>{{ old('alamat', $dataPribadi->alamat) }}</textarea>
                                    @error('alamat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="foto" class="form-label">Foto (Opsional)</label>
                                    <input type="file" class="form-control @error('foto') is-invalid @enderror" id="foto" name="foto" {{ $dataPribadi->foto ? '' : 'required' }}>
                                    @error('foto')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if ($dataPribadi->foto_url)
                                        <div class="mt-2 foto-preview-wrap">
                                            <img src="{{ $dataPribadi->foto_url }}" alt="Foto" width="120">
                                            <div class="text-muted small">Foto saat ini</div>
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="gaji" class="form-label">Gaji</label>
                                    <input type="text" inputmode="numeric" autocomplete="off"
                                        class="form-control @error('gaji') is-invalid @enderror" id="gaji" name="gaji"
                                        value="{{ old('gaji', $dataPribadi->gaji) }}" placeholder="Contoh: 300.000" required>
                                    @error('gaji')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                        <hr class="my-4">
                        <div class="h5 mb-3">Motivasi</div>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="motivasi_kerja" class="form-label">Motivasi Kerja</label>
                                    <textarea class="form-control @error('motivasi_kerja') is-invalid @enderror" id="motivasi_kerja" name="motivasi_kerja" rows="4" required>{{ old('motivasi_kerja', $dataPribadi->motivasi_kerja) }}</textarea>
                                    @error('motivasi_kerja')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="pelatihan" class="form-label">Pelatihan</label>
                                    <textarea class="form-control @error('pelatihan') is-invalid @enderror" id="pelatihan" name="pelatihan" rows="3" required>{{ old('pelatihan', $dataPribadi->pelatihan) }}</textarea>
                                    @error('pelatihan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function formatRibuan(input) {
            const raw = String(input || '').replace(/[^\d]/g, '');
            if (!raw) return '';
            return raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function digitsOnly(input) {
            return String(input || '').replace(/[^\d]/g, '');
        }

        const gajiInput = document.getElementById('gaji');
        if (gajiInput) {
            gajiInput.value = formatRibuan(gajiInput.value);
            gajiInput.addEventListener('input', function () {
                const start = gajiInput.selectionStart;
                const before = gajiInput.value;
                gajiInput.value = formatRibuan(gajiInput.value);
                const diff = gajiInput.value.length - before.length;
                gajiInput.setSelectionRange(start + diff, start + diff);
            });
        }

        const noRekeningInput = document.getElementById('no_rekening');
        if (noRekeningInput) {
            noRekeningInput.value = digitsOnly(noRekeningInput.value);
            noRekeningInput.addEventListener('input', function () {
                const start = noRekeningInput.selectionStart;
                const before = noRekeningInput.value;
                noRekeningInput.value = digitsOnly(noRekeningInput.value);
                const diff = noRekeningInput.value.length - before.length;
                noRekeningInput.setSelectionRange(start + diff, start + diff);
            });
        }
    </script>
</body>

</html>
