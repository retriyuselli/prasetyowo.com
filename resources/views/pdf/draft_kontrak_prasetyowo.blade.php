<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Draft Kontrak Prasetyowo</title>
    <style>
        @page {
            margin: 140px 45px 30px 65px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #000;
            margin: 0;
        }

        header {
            position: fixed;
            top: -170px;
            left: 0;
            right: 0;
            bottom: 50px;
            height: 100px;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
        }

        .header-table td {
            vertical-align: middle;
        }

        .logo-img {
            max-height: 60px;
            width: 80%;
        }

        .footer {
            position: fixed;
            bottom: -10px;
            right: 0px;
            text-align: right;
            font-size: 11px;
            color: #000000;
        }

        .pagenum:before {
            content: counter(page);
        }

        .title {
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 16px;
            text-transform: uppercase;
        }
        
        .subtitle {
            text-align: center;
            text-decoration: underline;
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 16px;
        }

        .section-title {
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 3px;
            text-transform: uppercase;
            font-size: 11px;
        }

        .section-title.pasal {
            text-align: center;
        }

        .section-title.subpasal {
            text-align: center;
            text-decoration: underline;
        }

        .text-justify {
            text-align: justify;
        }

        table.content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            margin-top: 12px;
        }

        table.content-table td {
            vertical-align: top;
            padding: 2px 0;
        }

        .label {
            width: 130px;
            font-weight: normal;
        }

        .separator {
            width: 10px;
            text-align: center;
        }

        ol,
        ul {
            margin: 0;
            padding-left: 20px;
        }

        ol[type="a"] {
            padding-left: 40px;
        }

        li {
            margin-bottom: 5px;
            text-align: justify;
        }

        .signature-section {
            margin-top: 20px;
            width: 100%;
            page-break-inside: avoid;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 10px;
        }

        .sign-space {
            height: 70px;
        }

        .company-name {
            font-size: 11px;
            font-weight: bold;
        }

        .company-info {
            font-size: 11px;
        }
    </style>
</head>

<body>
    @php
        $companyName = '{Isi dengan nama perusahaan}';
        $companyAddress = '{Isi dengan alamat perusahaan}';
        $companyPhone = '{Isi dengan nomor telepon perusahaan}';
        $companyEmail = '{Isi dengan email perusahaan}';
        $companyOwnerName = '{Isi dengan nama pemilik perusahaan}';
        $companyOwnerPosition = '{Isi dengan jabatan pemilik perusahaan}';

        $companyBankName = '{Isi dengan nama bank perusahaan}';
        $companyBankAccount = '{Isi dengan nomor rekening bank perusahaan}';
        $companyBankHolder = '{Isi dengan nama pemegang rekening bank perusahaan}';

        $companyBankName2 = null;
        $companyBankAccount2 = null;
        $companyBankHolder2 = null;

        $company = $company ?? null;
        if (! $company && \Illuminate\Support\Facades\Schema::hasTable('companies')) {
            $company = \App\Models\Company::with(['paymentMethod', 'paymentMethodSecondary'])->first();
        } elseif ($company) {
            $company->loadMissing(['paymentMethod', 'paymentMethodSecondary']);
        }

        if ($company?->company_name) {
            $companyName = $company->company_name;
        }
        if ($company?->address) {
            $companyAddress = $company->address;
        }
        if ($company?->phone) {
            $companyPhone = $company->phone;
        }
        if ($company?->email) {
            $companyEmail = $company->email;
        }
        if ($company?->owner_name) {
            $companyOwnerName = $company->owner_name;
        }
        if ($company?->jabatan_owner) {
            $companyOwnerPosition = $company->jabatan_owner;
        }

        if ($company?->paymentMethod) {
            $companyBankName = $company->paymentMethod->bank_name ?: $companyBankName;
            $companyBankAccount = $company->paymentMethod->no_rekening ?: $companyBankAccount;
            $companyBankHolder = $company->paymentMethod->name ?: $companyBankHolder;
        }

        if ($company?->paymentMethodSecondary) {
            $companyBankName2 = $company->paymentMethodSecondary->bank_name ?: null;
            $companyBankAccount2 = $company->paymentMethodSecondary->no_rekening ?: null;
            $companyBankHolder2 = $company->paymentMethodSecondary->name ?: null;
        }

        $record = $record ?? null;
        $prospect = $prospect ?? ($record?->prospect ?? null);

        $createdAt = $record?->created_at
            ? $record->created_at->copy()->setTimezone('Asia/Jakarta')->locale('id')
            : \Carbon\Carbon::now('Asia/Jakarta')->locale('id');

        $hari = $createdAt->translatedFormat('l');
        $tanggal = $createdAt->translatedFormat('d F Y');
        $tempat = $company?->city ?: ($prospect?->venue ?: '__________');

        $clientName = $record?->customer_name;
        if (! $clientName) {
            $cpp = trim((string) ($prospect?->name_cpp ?? ''));
            $cpw = trim((string) ($prospect?->name_cpw ?? ''));
            $clientName = trim($cpp.($cpp && $cpw ? ' & ' : '').$cpw) ?: '__________';
        }

        $clientAddress = $prospect?->address ?: '__________';

        $eventDate = $prospect?->date_resepsi ?: $prospect?->date_akad;
        $eventDateText = $eventDate ? $eventDate->copy()->locale('id')->translatedFormat('d F Y') : '__________';
    @endphp

    <header>
        <table class="header-table" style="margin-top: 50px;">
            <tr>
                <td style="width: 65%;">
                    <div class="company-name">{{ $companyName }}</div>
                    <div class="company-info">
                        Alamat : {{ $companyAddress }}<br>
                        No. Tlp : {{ $companyPhone }}<br>
                        Email : {{ $companyEmail }}
                    </div>
                </td>
                <td style="text-align: right;">
                    @php
                        $logoPath = null;
                        $logoSrc = '';

                        if (
                            isset($company) &&
                            $company?->logo_url &&
                            \Illuminate\Support\Facades\Storage::disk('public')->exists($company->logo_url)
                        ) {
                            $logoPath = \Illuminate\Support\Facades\Storage::disk('public')->path($company->logo_url);
                        } else {
                            $logoPath = public_path('images/logomki.png');
                        }

                        if ($logoPath && file_exists($logoPath)) {
                            $logoMime = mime_content_type($logoPath);
                            if ($logoMime) {
                                $logoSrc = 'data:' . $logoMime . ';base64,' . base64_encode(file_get_contents($logoPath));
                            }
                        }
                    @endphp
                    @if ($logoSrc)
                        <img src="{{ $logoSrc }}" alt="Logo Perusahaan" class="logo-img">
                    @else
                        <b>{{ $companyName }}</b>
                    @endif
                </td>
            </tr>
        </table>
    </header>

    <div class="footer">
        <table style="width: 100%; border-collapse: collapse; border: none;">
            <tr>
                <td style="text-align: right; vertical-align: bottom; padding-right: 0px; font-size: 9px;">
                    <span>
                        Dokumen ini dicetak secara otomatis pada
                        {{ $createdAt->translatedFormat('d F Y') }} pukul {{ $createdAt->translatedFormat('H:i') }} |
                        Hal <span class="pagenum"></span> |
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <div class="title" style="margin-top: 0px; margin-bottom: 1px;">MEMORANDUM OF UNDERSTANDING</div>
    <div class="subtitle" style="margin-top: 0px; margin-bottom: 4px;">PENJUALAN JASA PAKET WEDDING PLANNER & ORGANIZER</div>

    <table class="content-table">
        <tr>
            <td class="label">Hari</td>
            <td class="separator">:</td>
            <td>{{ $hari }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal</td>
            <td class="separator">:</td>
            <td>{{ $tanggal }}</td>
        </tr>
        <tr>
            <td class="label">Tempat</td>
            <td class="separator">:</td>
            <td>{{ $tempat }}</td>
        </tr>
    </table>

    <p class="text-justify">Yang bertanda tangan di bawah ini adalah:</p>
    <ol>
        <li class="text-justify">
            <b>{{ $companyOwnerName }}</b> : {{ $companyOwnerPosition }} dari <b>{{ $companyName }}</b> yang berkedudukan
            di {{ $companyAddress }} dan selanjutnya disebut sebagai <b>PIHAK PERTAMA</b>.
        </li>
        <li class="text-justify">
            <b>{{ $clientName }}</b> : Konsumen yang berkedudukan di {{ $clientAddress }} dan selanjutnya disebut sebagai
            <b>PIHAK KEDUA</b>.
        </li>
    </ol>

    <p class="text-justify">
        PIHAK PERTAMA dan PIHAK KEDUA yang selanjutnya disebut sebagai PARA PIHAK melalui hal ini telah mengikatkan
        secara hukum untuk bekerja sama dalam hal perjanjian penjualan Paket Jasa Wedding Organizer untuk acara
        Pernikahan Pihak Kedua yang akan dilaksanakan pada {{ $eventDateText }}.
    </p>

    <div class="section-title pasal">Pasal 1</div>
    <div class="section-title subpasal"style="font-weight: bold; margin-top: 0; margin-bottom: 6px;">Obyek Perjanjian</div>
    <ol>
        <li class="text-justify">
            Obyek Perjanjian Kerjasama ini adalah penjualan jasa wedding organizer dari PIHAK PERTAMA kepada PIHAK KEDUA
            yang terdiri dari:
            <div style="margin-top: 6px;">
                <b>Wedding Organizer</b>
                <span style="float: right; white-space: nowrap;">
                    Rp. {{ number_format((int) ($record?->grand_total ?? 0), 0, ',', '.') }}
                </span>
            </div>
            <div style="margin-top: 4px;">
                <b>{{ \Illuminate\Support\Str::upper($record?->product?->name ?? 'PAKET WEDDING ORGANIZER') }}</b>
            </div>
            <div style="margin-top: 6px;">Konsultasi dan Penanganan Persiapan Acara</div>
            <ol type="a" style="margin-top: 6px;">
                <li>Mengkoordinir semua vendor yang telah dipesan baik oleh klien maupun paket dari {{ $companyName }}</li>
                <li>Cetak Buku Panduan sebanyak 20 pcs</li>
                <li>Acara dengan konsep standing/ buffet style</li>
                <li>Durasi pekerjaan hari H max 10 jam / Halfday (include persiapan)</li>
                <li>Jumlah Tamu maksimal 500 pax</li>
                <li>Jumlah Tim bekerja sebanyak 8 orang</li>
                <li>Jumlah nama list tamu VIP sebanyak 10 list nama</li>
                <li>Area Wilayah Pekerjaan di Yogyakarta, Sleman, atau Bantul</li>
            </ol>
        </li>
    </ol>

    <div class="section-title pasal">Pasal 2</div>
    <div class="section-title subpasal" style="font-weight: bold; margin-top: 0; margin-bottom: 6px;">Sistem Pembayaran</div>
    <ol>
        <li class="text-justify">
            PIHAK KEDUA wajib melakukan pembayaran kepada PIHAK PERTAMA dengan ketentuan sebagai berikut:
            <ol type="a" style="margin-top: 6px;">
                <li>Down Payment (DP) sebesar 10% (sepuluh persen) dari total nilai perjanjian</li>
                <li>Termin 1 sebesar 30% (tiga puluh persen) pada H-2 (dua) bulan sebelum acara</li>
                <li>Termin 2 sebesar 30% (tiga puluh persen) pada H-1 (satu) bulan sebelum acara</li>
                <li>Pelunasan sebesar 30% (tiga puluh persen) dibayarkan paling lambat H-7 (tujuh hari) sebelum acara</li>
            </ol>
        </li>
        <li class="text-justify">
            Seluruh pembayaran dilakukan melalui rekening resmi PIHAK PERTAMA:
            <div style="margin-top: 10px;">
                <table style="width: 100%; table-layout: fixed;">
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding-right: 10px;">
                            <div><b>{{ $companyBankName }}</b></div>
                            <div>{{ $companyBankAccount }}</div>
                            <div>Atas Nama: <b>{{ $companyBankHolder }}</b></div>
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 10px;">
                            @if ($companyBankName2 || $companyBankAccount2 || $companyBankHolder2)
                                <div><b>{{ $companyBankName2 ?? '-' }}</b></div>
                                <div>{{ $companyBankAccount2 ?? '-' }}</div>
                                <div>Atas Nama: <b>{{ $companyBankHolder2 ?? '-' }}</b></div>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </li>
        <li class="text-justify">Pembayaran dianggap sah setelah dana diterima di rekening PIHAK PERTAMA.</li>
    </ol>

    <div class="section-title pasal">Pasal 3</div>
    <div class="section-title subpasal" style="font-weight: bold; margin-top: 0; margin-bottom: 6px;">Pelaksanaan</div>
    <ol>
        <li class="text-justify">
            Pelaksanaan kerjasama ini dilakukan dengan tenggat waktu sejak tanggal kerjasama ini ditandatangani oleh PARA
            PIHAK hingga acara berlangsung.
        </li>
        <li class="text-justify">
            Pelaksanaan pekerjaan dapat berubah ataupun diperpanjang dengan kesepakatan tertulis oleh PARA PIHAK, maksimal
            3 bulan sebelum pelaksanaan acara.
        </li>
        <li class="text-justify">
            Pelaksanaan pekerjaan dapat berubah waktu pelaksanaan berdasarkan force majeure yang akan diuraikan di Pasal
            6.
        </li>
        <li class="text-justify">
            Perjanjian oleh PARA PIHAK akan berakhir pada {{ $eventDateText }}.
        </li>
    </ol>

    <div class="section-title pasal">Pasal 4</div>
    <div class="section-title subpasal" style="font-weight: bold; margin-top: 0; margin-bottom: 6px;">Hak Dan Kewajiban</div>

    <ol>
        <li class="text-justify">Kewajiban PIHAK PERTAMA: </li>
            <ol type="a" style="margin-top: 6px;">
                <li>Melakukan koordinasi pra acara dengan vendor-vendor yang telah dipilih oleh PIHAK KEDUA</li>
                <li>Melakukan re-checking pekerjaan vendor sesuai dengan perjanjian kerjasama yang telah dijanjikan kepada PIHAK KEDUA</li>
                <li>PIHAK PERTAMA terbuka untuk segala konsultasi dan masukan dari PIHAK KEDUA</li>
                <li>PIHAK PERTAMA akan selalu hadir dan memimpin jalannya setiap Rapat Koordinasi di wilayah Daerah Istimewa Yogyakarta.</li>
                <li>PIHAK PERTAMA akan membuat buku panduan acara untuk panitia dan vendor yang terlibat sebanyak 20 pcs.</li>
                <li>Melaporkan setiap progres yang dikerjakan oleh vendor-vendor kepada PIHAK KEDUA</li>
                <li>Dalam hal PIHAK PERTAMA tidak dapat memenuhi kewajibannya sesuai dengan perjanjian ini, PIHAK KEDUA berhak untuk meminta pengembalian dana sebesar jumlah yang dibayarkan dan dikurangi dengan biaya administrasi sebesar down payment awal.</li>
                <li>Jika PIHAK KEDUA membatalkan perjanjian lebih dari 6 bulan sebelum acara, maka PIHAK KEDUA berhak mendapatkan pengembalian dana sebesar 80% dari jumlah yang telah dibayarkan. Jika pembatalan terjadi dalam waktu 6 bulan sebelum pelaksanaan acara, maka pengembalian dana akan ditentukan berdasarkan kesepakatan bersama.</li>
                <li>PIHAK PERTAMA wajib menyelesaikan semua pekerjaan sesuai dengan jadwal yang telah disepakati dan memastikan bahwa kualitas layanan yang diberikan sesuai dengan spesifikasi yang tertera dalam perjanjian.</li>
            </ol>        
    </ol>

    <ol>
        <li class="text-justify">Hak PIHAK KEDUA: </li>
            <ol type="a" style="margin-top: 6px;">
                <li>Menerima hasil laporan pekerjaan vendor-vendor dari PIHAK PERTAMA</li>
                <li>Mengingatkan semua hal yang berkaitan dengan pelaksanaan acara kepada PIHAK PERTAMA</li>
                <li>Melaksanakan gladi bersih untuk pra acara sesuai dengan waktu yang disepakati dengan PIHAK PERTAMA</li>
                <li>Memberikan masukan dan berkonsultasi terkait pelaksanaan acara pernikahan.</li>
                <li>PIHAK KEDUA berhak untuk mengganti tanggal acara dengan memberikan pemberitahuan tertulis minimal 3 bulan sebelum tanggal pelaksanaan acara.</li>
            </ol>        
    </ol>

    <div class="section-title pasal">Pasal 5</div>
    <div class="section-title subpasal" style="font-weight: bold; margin-top: 0; margin-bottom: 6px;">Pengawasan, Monitoring, dan Sanksi</div>
    <ol>
        <li class="text-justify">PIHAK KEDUA memiliki hak untuk melakukan pengawasan dan monitoring terhadap setiap tahap persiapan dan pelaksanaan acara.</li>
        <li class="text-justify">PIHAK PERTAMA wajib menyediakan laporan kemajuan secara berkala dan terbuka untuk inspeksi mendadak yang dilakukan oleh PIHAK KEDUA.</li>
        <li class="text-justify">Jika PIHAK PERTAMA gagal memenuhi salah satu kewajiban yang tercantum dalam Pasal 4 dalam waktu yang telah disepakati, PIHAK KEDUA berhak meminta pengembalian dana secara penuh atau kompensasi tambahan berupa potongan harga atau layanan tambahan tanpa biaya.</li>
    </ol>

    <div class="section-title pasal">Pasal 6</div>
    <div class="section-title subpasal" style="font-weight: bold; margin-top: 0; margin-bottom: 6px;">Keadaan Memaksa (Force Majeure)</div>
    <ol>
        <li class="text-justify">Keadaan memaksa dapat memperpanjang masa tenggat waktu pelaksanaan kerjasama.</li>
        <li class="text-justify">Keadaan memaksa di antaranya keluarga inti meninggal dunia, bencana alam, dan peperangan yang diumumkan oleh Negara.</li>
        <li class="text-justify">Keadaan memaksa juga termasuk pandemi penyakit yang telah diumumkan bencana oleh Pemerintah Pusat, dan pelaksanaan diundur sesuai kesepakatan.</li>
    </ol>

    <div class="section-title pasal">Pasal 7</div>
    <div class="section-title subpasal" style="font-weight: bold; margin-top: 0; margin-bottom: 6px;">Perselisihan</div>
    <ol>
        <li class="text-justify">Perselisihan terjadi apabila salah satu pihak tidak melaksanakan kewajiban sesuai dengan isi kerjasama.</li>
        <li class="text-justify">
            Penyelesaian perselisihan dilaksanakan dengan:
            <ol type="a" style="margin-top: 6px;">
                <li>Musyawarah mufakat yang dituangkan dalam pelaksanaan perselisihan tertulis.</li>
                <li>Arbitrase.</li>
                <li>Pengadilan.</li>
            </ol>
        </li>
        <li class="text-justify">Pengadilan yang ditunjuk adalah Kantor Pengadilan Negeri Kabupaten Sleman.</li>
    </ol>

    <div class="section-title pasal">Pasal 8</div>
    <div class="section-title subpasal" style="font-weight: bold; margin-top: 0; margin-bottom: 6px;">Penutup</div>
    <ol>
        <li class="text-justify">Perubahan ataupun pembatalan perjanjian adalah kesepakatan PARA PIHAK yang dituangkan dalam nota tertulis.</li>
        <li class="text-justify">Perubahan baik addendum maupun amandemen perjanjian dilaksanakan oleh PARA PIHAK.</li>
    </ol>

    <p class="text-justify">
        Demikian perjanjian kerjasama ini dibuat dengan rangkap dua dan berkekuatan hukum tetap yang sama.
    </p>

    <div style="text-align: right; margin-top: 20px;">
        {{ $company?->city ?: '__________' }}, {{ $tanggal }}
    </div>

    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td>
                    Pihak Pertama
                    <div class="sign-space"></div>
                    <div style="text-decoration: underline;"><b>{{ $companyOwnerName }}</b></div>
                </td>
                <td>
                    Pihak Kedua
                    <div class="sign-space"></div>
                    <div style="text-decoration: underline;"><b>{{ $record?->name_ttd ?? $clientName }}</b></div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
