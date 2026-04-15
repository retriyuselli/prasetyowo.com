<?php

namespace App\Http\Controllers;

use App\Models\DataPribadi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View; // Import Validator

class FrontendDataPribadiController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create()
    {
        return view('data-pribadi.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        if ($request->has('gaji')) {
            $request->merge([
                'gaji' => preg_replace('/[^\d]/', '', (string) $request->input('gaji')),
            ]);
        }

        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:data_pribadis,email',
            'nomor_telepon' => 'required|string|max:20',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|string|in:Laki-laki,Perempuan',
            'alamat' => 'required|string',
            // Validasi untuk foto: harus gambar, tipe mime tertentu, dan ukuran maksimal 1MB (1024 KB)
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'pekerjaan' => 'required|string|max:255',
            'gaji' => 'required|numeric|min:0',
            'motivasi_kerja' => 'required|string',
            'pelatihan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('data-pribadi.create')
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('data-pribadi-fotos', 'public');
            $data['foto'] = $path;
        }

        $dataPribadi = DataPribadi::create($data);

        $editUrl = URL::temporarySignedRoute('data-pribadi.edit', now()->addDays(7), ['dataPribadi' => $dataPribadi->id]);

        return redirect()
            ->route('data-pribadi.success')
            ->with('success', 'Data pribadi berhasil disimpan!')
            ->with('edit_url', $editUrl);
    }

    public function edit(DataPribadi $dataPribadi): View
    {
        $updateUrl = URL::temporarySignedRoute('data-pribadi.update', now()->addDays(7), ['dataPribadi' => $dataPribadi->id]);

        return view('data-pribadi.edit', compact('dataPribadi', 'updateUrl'));
    }

    public function update(Request $request, DataPribadi $dataPribadi): RedirectResponse
    {
        $fotoRule = $dataPribadi->foto ? 'nullable' : 'required';

        if ($request->has('gaji')) {
            $request->merge([
                'gaji' => preg_replace('/[^\d]/', '', (string) $request->input('gaji')),
            ]);
        }

        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:data_pribadis,email,' . $dataPribadi->id,
            'nomor_telepon' => 'required|string|max:20',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|string|in:Laki-laki,Perempuan',
            'alamat' => 'required|string',
            'foto' => $fotoRule . '|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'pekerjaan' => 'required|string|max:255',
            'gaji' => 'required|numeric|min:0',
            'motivasi_kerja' => 'required|string',
            'pelatihan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->to(URL::previous())
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        if ($request->hasFile('foto')) {
            if ($dataPribadi->foto) {
                Storage::disk('public')->delete($dataPribadi->foto);
            }
            $path = $request->file('foto')->store('data-pribadi-fotos', 'public');
            $data['foto'] = $path;
        } else {
            unset($data['foto']);
        }

        $dataPribadi->update($data);

        $editUrl = URL::temporarySignedRoute('data-pribadi.edit', now()->addDays(7), ['dataPribadi' => $dataPribadi->id]);

        return redirect()
            ->route('data-pribadi.success')
            ->with('success', 'Data pribadi berhasil diperbarui!')
            ->with('edit_url', $editUrl);
    }

    public function index(Request $request) // Tambahkan Request $request
    {
        $query = DataPribadi::query();

        // Logika Pencarian
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            // Sesuaikan 'nama_lengkap' dengan nama kolom yang benar di tabel Anda
            $query->where('nama_lengkap', 'LIKE', '%'.$searchTerm.'%');
        }

        $dataPribadis = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('data-pribadi.index', compact('dataPribadis'));
    }

    /**
     * Show success thank-you page after storing data.
     *
     * @return View
     */
    public function success(): View
    {
        return view('data-pribadi.success');
    }
}
