<?php

namespace App\Http\Controllers;

use App\Models\KategoriSampah;
use App\Models\Setoran;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    public function bulanTersedia(): JsonResponse
    {
        $bulan = Setoran::selectRaw('MONTH(tgl) as bulan, YEAR(tgl) as tahun')
            ->groupBy('bulan', 'tahun')
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->get()
            ->map(fn ($item) => [
                'value' => sprintf('%d-%02d', $item->tahun, $item->bulan),
                'label' => Carbon::createFromDate($item->tahun, $item->bulan, 1)->locale('id')->translatedFormat('F Y'),
            ]);

        return response()->json(['success' => true, 'data' => $bulan]);
    }

    public function export(Request $request)
    {
        $bulanParam = $request->query('bulan', now()->format('Y-m'));
        $format = strtolower($request->query('format', 'excel'));

        $query = Setoran::with(['user', 'kategori']);

        if ($bulanParam !== 'all') {
            try {
                $date = Carbon::createFromFormat('Y-m', $bulanParam);
                $query->whereYear('tgl', $date->year)->whereMonth('tgl', $date->month);
                $periodeText = $date->locale('id')->translatedFormat('F Y');
            } catch (\Exception $e) {
                $periodeText = $bulanParam;
            }
        } else {
            $periodeText = 'Semua Periode';
        }

        $setorans = $query->orderBy('tgl', 'asc')->get();

        $totalBerat = 0;
        $totalPoin = 0;
        $kategoriSummary = [];

        foreach ($setorans as $s) {
            $beratKg = $s->satuan === 'Gram' ? ($s->berat / 1000) : $s->berat;
            $totalBerat += $beratKg;
            $totalPoin += $s->poin;

            $katName = $s->kategori ? $s->kategori->nama : 'Lainnya';
            if (!isset($kategoriSummary[$katName])) {
                $kategoriSummary[$katName] = [
                    'nama' => $katName,
                    'total_berat_kg' => 0,
                    'total_poin' => 0,
                    'jumlah_transaksi' => 0,
                ];
            }
            $kategoriSummary[$katName]['total_berat_kg'] += $beratKg;
            $kategoriSummary[$katName]['total_poin'] += $s->poin;
            $kategoriSummary[$katName]['jumlah_transaksi'] += 1;
        }

        if ($format === 'pdf') {
            return $this->exportPdf($periodeText, $setorans, $kategoriSummary, $totalBerat, $totalPoin);
        }

        return $this->exportCsv($periodeText, $setorans, $kategoriSummary, $totalBerat, $totalPoin);
    }

    private function exportPdf(string $periodeText, $setorans, array $kategoriSummary, float $totalBerat, int $totalPoin): StreamedResponse
    {
        $filename = 'Laporan_Sampah_PLUNG_' . str_replace(' ', '_', $periodeText) . '.pdf';

        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('laporan.pdf', [
            'periode' => $periodeText,
            'setorans' => $setorans,
            'kategoriSummary' => $kategoriSummary,
            'totalBerat' => $totalBerat,
            'totalPoin' => $totalPoin,
        ])->render());
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response()->stream(function () use ($dompdf) {
            echo $dompdf->output();
        }, 200, $headers);
    }

    private function exportCsv(string $periodeText, $setorans, array $kategoriSummary, float $totalBerat, int $totalPoin): StreamedResponse
    {
        $filename = 'Rekap_Setoran_Sampah_PLUNG_' . str_replace(' ', '_', $periodeText) . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($periodeText, $setorans, $kategoriSummary, $totalBerat, $totalPoin) {
            $handle = fopen('php://output', 'w');

            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, ['REKAPITULASI PENGELOLAAN SAMPAH MANDIRI - PLUNG!']);
            fputcsv($handle, ['WILAYAH RT 36 / RW 09']);
            fputcsv($handle, ['Periode:', $periodeText]);
            fputcsv($handle, ['Tanggal Cetak:', Carbon::now()->locale('id')->translatedFormat('d F Y, H:i') . ' WIB']);
            fputcsv($handle, []);

            fputcsv($handle, ['--- RINGKASAN PER KATEGORI SAMPAH ---']);
            fputcsv($handle, ['No', 'Kategori Sampah', 'Jumlah Transaksi', 'Total Berat (Kg)', 'Total Poin']);
            $noKat = 1;
            foreach ($kategoriSummary as $kat) {
                fputcsv($handle, [
                    $noKat++,
                    $kat['nama'],
                    $kat['jumlah_transaksi'],
                    number_format($kat['total_berat_kg'], 2, ',', '.'),
                    $kat['total_poin'],
                ]);
            }
            fputcsv($handle, [
                'TOTAL',
                '',
                $setorans->count(),
                number_format($totalBerat, 2, ',', '.'),
                $totalPoin,
            ]);
            fputcsv($handle, []);

            fputcsv($handle, ['--- RINCIAN TRANSAKSI SETORAN WARGA ---']);
            fputcsv($handle, ['No', 'Tanggal', 'Nama Warga', 'No. Rumah / Blok', 'Kategori Sampah', 'Berat / Jumlah', 'Poin', 'Status', 'Catatan']);

            $no = 1;
            foreach ($setorans as $s) {
                $cleanBerat = ((float) $s->berat == (int) $s->berat ? (int) $s->berat : (float) $s->berat);
                fputcsv($handle, [
                    $no++,
                    $s->tgl ? $s->tgl->locale('id')->translatedFormat('d/m/Y') : '-',
                    $s->user ? $s->user->nama : '-',
                    $s->user ? $s->user->rumah : '-',
                    $s->kategori ? $s->kategori->nama : '-',
                    $cleanBerat . ' ' . $s->satuan,
                    $s->poin,
                    ucfirst($s->status),
                    $s->catatan ?? '-',
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['Dicetak otomatis oleh Sistem Aplikasi PLUNG!']);

            fclose($handle);
        }, 200, $headers);
    }
}
