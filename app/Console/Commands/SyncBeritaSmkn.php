<?php

namespace App\Console\Commands;

use App\Models\Berita;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Command untuk sinkronisasi berita dari website SMKN 1 Bawang
 *
 * Command ini mengambil data berita dari RSS feed SMKN 1 Bawang
 * dan menyimpannya ke dalam database.
 */
class SyncBeritaSmkn extends Command
{
    /**
     * Signature command
     *
     * @var string
     */
    protected $signature = 'berita:sync-smkn';

    /**
     * Deskripsi command
     *
     * @var string
     */
    protected $description = 'Sinkronisasi berita dari website SMKN 1 Bawang';

    /**
     * Eksekusi command
     *
     * @return int
     */
    public function handle(): int
    {
        // URL feed RSS dari SMKN 1 Bawang
        $feedUrl = 'https://smkn1bawang.sch.id/feed/';

        try {
            // Mengambil data dari feed dengan timeout 20 detik
            $response = Http::timeout(20)
                ->accept('application/rss+xml, application/xml, text/xml')
                ->get($feedUrl);

            // Cek apakah request berhasil
            if ($response->failed()) {
                $this->error('Gagal mengambil feed.');
                return self::FAILURE;
            }

            // Mengaktifkan error handling internal untuk libxml
            libxml_use_internal_errors(true);

            // Parse XML dari response body
            $xml = simplexml_load_string($response->body(), 'SimpleXMLElement', LIBXML_NOCDATA);

            // Validasi format XML
            if (!$xml || !isset($xml->channel->item)) {
                $this->error('Format feed tidak valid.');
                return self::FAILURE;
            }

            $jumlah = 0;

            // Loop melalui setiap item berita di feed
            foreach ($xml->channel->item as $item) {
                // Ekstrak data dari item
                $title = isset($item->title) ? trim((string) $item->title) : null;
                $link = isset($item->link) ? trim((string) $item->link) : null;
                $description = isset($item->description) ? trim(strip_tags((string) $item->description)) : null;
                $author = isset($item->children('dc', true)->creator)
                    ? trim((string) $item->children('dc', true)->creator)
                    : null;

                // Ekstrak kategori
                $kategori = null;
                if (isset($item->category)) {
                    $kategoriData = [];
                    foreach ($item->category as $cat) {
                        $kategoriData[] = trim((string) $cat);
                    }
                    $kategori = implode(', ', array_filter($kategoriData));
                }

                // Ekstrak tanggal publish
                $tanggalPublish = null;
                if (isset($item->pubDate) && !empty((string) $item->pubDate)) {
                    $timestamp = strtotime((string) $item->pubDate);
                    if ($timestamp !== false) {
                        $tanggalPublish = date('Y-m-d H:i:s', $timestamp);
                    }
                }

                // Skip jika judul atau link kosong
                if (!$title || !$link) {
                    continue;
                }

                // Simpan atau update berita di database
                Berita::updateOrCreate(
                    ['link' => $link],
                    [
                        'judul' => $title,
                        'deskripsi' => $description,
                        'author' => $author,
                        'kategori' => $kategori,
                        'tanggal_publish' => $tanggalPublish,
                    ]
                );

                $jumlah++;
            }

            // Tampilkan pesan sukses
            $this->info("Sinkronisasi selesai. Total data diproses: {$jumlah}");
            return self::SUCCESS;
        } catch (\Throwable $e) {
            // Tangani error
            $this->error('Terjadi kesalahan: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
