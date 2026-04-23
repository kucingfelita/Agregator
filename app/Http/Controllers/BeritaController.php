<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BeritaController extends Controller
{
    private string $feedUrl = 'https://smkn1bawang.sch.id/feed/';

    public function index(Request $request){
        $keyword = $request->keyword;
        $query = \App\Models\Berita::query();

        if ($keyword) {
            $query->where('judul', 'like', "%$keyword%")
                ->orWhere('deskripsi', 'like', "%$keyword%")
                ->orWhere('kategori', 'like', "%$keyword%");
        }
        $beritas = $query->orderByDesc('tanggal_publish')->paginate(10)->withQueryString();
        return view('berita.index', compact('beritas', 'keyword'));
    }

    public function sync(){
        try{
            $response = Http::timeout(20)
            ->accept('application/rss+xml, application/xml, text/xml')
            ->get($this->feedUrl);

            if ($response->failed()){
                return redirect()->back()->with('error', 'Gagal mengambil data feed dari website SMKN 1 Bawang.');
            }
            $xmlString = $response->body();
            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA);

            if(!$xml){
                return redirect()->route('berita.index')->with('error', 'Format xml feed tidak valid atau tidak dapat dibaca.');
             }
             $jumlahTersimpan = 0;
             if(isset($xml->channel->item)){
                foreach($xml->channel->item as $item){
                $title = isset ($item->title) ? trim((string)$item->title) : 'Null';
                $link = isset ($item->link) ? trim((string)$item->link) : 'Null';

                $description = isset ($item->description) ? trim(strip_tags((string)$item->description)) : 'Null';
                $author = isset ($item->children('dc', true)->creator) ? trim((string)$item->children('dc', true)->creator) : 'Null';

                $kategori = null;
                if(isset($item->category)){
                    $kategoriData = [];
                    foreach($item->category as $cat){
                        $kategoriData[] = trim((string)$cat);
             }
             $kategori = implode(', ', array_filter($kategoriData));
             }
             $tanggalPublish = null;
                if(isset($item->pubDate) && !empty((string)$item->pubDate)){
                    if ($timestamp = strtotime((string)$item->pubDate)) {
                        $tanggalPublish = date('Y-m-d H:i:s', $timestamp);
                    }
                }
                if (!$title || !$link) {
                    continue;
                }
                Berita::updateOrCreate(
                    ['link' => $link],
                    [
                        'judul' => $title,
                        'deskripsi' => $description,
                        'author' => $author,
                        'kategori' => $kategori,
                        'tanggal_publish' => $tanggalPublish
                    ]
                );

                $jumlahTersimpan++;
            }
        }
        return redirect()->route('berita.index')->with('success', 'Sinkronisasi berhasil. Total data diproses: ' . $jumlahTersimpan . ' berita berhasil disimpan atau diperbarui.');
        } catch (\Throwable $e){
            return redirect()->route('berita.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        } 
    }
    public function truncate(){
        Berita::truncate();
        return redirect()->route('berita.index')->with('success', 'Semua data berita berhasil dihapus.');
    }
}
