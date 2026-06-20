<?php

namespace App\Http\Controllers;

use App\Models\Background;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BackgroundController extends Controller
{
    public function index()
    {
        $backgrounds = Background::orderBy('harga')->get();
        return view('background.index', compact('backgrounds'));
    }

    public function create()
    {
        return view('background.create');
    }

    private function cekTier($type, $harga)
    {
        return match($type) {
            'color' => $harga < 50,
            'image' => $harga >= 50 && $harga < 100,
            'video' => $harga >= 100,
        };
    }

    private function pesanTier($type)
    {
        return match($type) {
            'color' => 'Background tipe warna harus harga di bawah 50 coin.',
            'image' => 'Background tipe foto harus harga 50-99 coin.',
            'video' => 'Background tipe video harus harga minimal 100 coin.',
        };
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'   => 'required|string|max:50',
            'harga'  => 'required|integer|min:0',
            'type'   => 'required|in:color,image,video',
            'warna1' => 'required_if:type,color',
            'warna2' => 'required_if:type,color',
            'file'   => 'required_if:type,image,video|file',
        ]);

        $type  = $request->type;
        $harga = (int) $request->harga;

        if (!$this->cekTier($type, $harga)) {
            return back()->withErrors(['harga' => $this->pesanTier($type)])->withInput();
        }

        if ($type === 'color') {
            $value = 'background: linear-gradient(135deg, '.$request->warna1.', '.$request->warna2.');';
        } else {
            $request->validate([
                'file' => $type === 'image'
                    ? 'mimes:jpg,jpeg,png,webp|max:5120'
                    : 'mimes:mp4,webm|max:20480',
            ]);

            $file = $request->file('file');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('backgrounds'), $filename);
            $value = 'backgrounds/'.$filename;
        }

        Background::create([
            'slug'  => Str::slug($request->nama).'-'.time(),
            'nama'  => $request->nama,
            'harga' => $harga,
            'type'  => $type,
            'value' => $value,
        ]);

        return redirect()->route('background.index')->with('success', 'Background berhasil ditambahkan!');
    }

    public function edit(Background $background)
    {
        return view('background.edit', compact('background'));
    }

    public function update(Request $request, Background $background)
    {
        $request->validate([
            'nama'   => 'required|string|max:50',
            'harga'  => 'required|integer|min:0',
            'type'   => 'required|in:color,image,video',
            'warna1' => 'required_if:type,color',
            'warna2' => 'required_if:type,color',
        ]);

        $type  = $request->type;
        $harga = (int) $request->harga;

        if (!$this->cekTier($type, $harga)) {
            return back()->withErrors(['harga' => $this->pesanTier($type)])->withInput();
        }

        if ($type !== 'color' && !$request->hasFile('file') && $background->type !== $type) {
            return back()->withErrors(['file' => 'Wajib upload file karena tipe background diganti.'])->withInput();
        }

        $data = [
            'nama'  => $request->nama,
            'harga' => $harga,
            'type'  => $type,
        ];

        if ($type === 'color') {
            $data['value'] = 'background: linear-gradient(135deg, '.$request->warna1.', '.$request->warna2.');';
        } elseif ($request->hasFile('file')) {
            $request->validate([
                'file' => $type === 'image'
                    ? 'mimes:jpg,jpeg,png,webp|max:5120'
                    : 'mimes:mp4,webm|max:20480',
            ]);

            if ($background->type !== 'color' && $background->value && file_exists(public_path($background->value))) {
                unlink(public_path($background->value));
            }

            $file = $request->file('file');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('backgrounds'), $filename);
            $data['value'] = 'backgrounds/'.$filename;
        }

        $background->update($data);

        return redirect()->route('background.index')->with('success', 'Background berhasil diupdate!');
    }

    public function destroy(Background $background)
    {
        if ($background->slug === 'default') {
            return back()->with('error', 'Background Default tidak boleh dihapus!');
        }

        if ($background->type !== 'color' && $background->value && file_exists(public_path($background->value))) {
            unlink(public_path($background->value));
        }

        $background->delete();

        return redirect()->route('background.index')->with('success', 'Background berhasil dihapus!');
    }
}