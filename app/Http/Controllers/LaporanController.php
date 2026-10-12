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

    /**
     * Susun laporan rekapitulasi lalu kirim sebagai PDF.
     *
     * Format keluaran hanya PDF. @param bulan "YYYY-MM" untuk satu bulan,
     * atau "all" untuk seluruh periode.
     */
    public function export(Request $request)
    {
        $bulanParam = $request->query('bulan', now()->format('Y-m'));

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

        return $this->exportPdf($periodeText, $setorans, $kategoriSummary, $totalBerat, $totalPoin);
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
}
