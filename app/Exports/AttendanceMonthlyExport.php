<?php

namespace App\Exports;

use App\Models\Karyawan;
use App\Models\Presensi;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AttendanceMonthlyExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize,
    WithColumnWidths
{
    protected Carbon $start;
    protected Carbon $end;
    protected string $bulan;       // format Y-m, e.g. "2026-03"
    protected int    $totalHariKerja;
    protected $karyawanList;

    public function __construct(string $bulan)
    {
        $this->bulan = $bulan;
        $this->start = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
        $this->end   = Carbon::createFromFormat('Y-m', $bulan)->endOfMonth();

        // Hitung hari kerja (Senin–Jumat) dalam bulan ini
        $this->totalHariKerja = 0;
        $cur = $this->start->copy();
        while ($cur->lte($this->end)) {
            if (!$cur->isWeekend()) $this->totalHariKerja++;
            $cur->addDay();
        }

        // Semua karyawan aktif beserta presensinya di bulan ini
        $this->karyawanList = Karyawan::with(['user', 'jabatan'])
            ->where('status', 'aktif')
            ->get()
            ->map(function ($k) {
                $presensi = Presensi::where('karyawan_id', $k->id)
                    ->whereBetween('tanggal', [
                        $this->start->toDateString(),
                        $this->end->toDateString(),
                    ])
                    ->get();

                $hadir     = $presensi->where('status', 'hadir')->count();
                $terlambat = $presensi->where('status', 'terlambat')->count();
                $totalMasuk = $hadir + $terlambat;
                $alpa      = $this->totalHariKerja - $totalMasuk;
                $alpa      = max($alpa, 0);
                $persen    = $this->totalHariKerja > 0
                    ? round(($totalMasuk / $this->totalHariKerja) * 100, 1)
                    : 0;

                return [
                    'karyawan'      => $k,
                    'hadir'         => $hadir,
                    'terlambat'     => $terlambat,
                    'alpa'          => $alpa,
                    'total_masuk'   => $totalMasuk,
                    'persentase'    => $persen,
                ];
            })
            ->sortBy(fn($r) => $r['karyawan']->user->name ?? '');
    }

    public function collection()
    {
        return $this->karyawanList->values();
    }

    public function headings(): array
    {
        $label = Carbon::createFromFormat('Y-m', $this->bulan)
            ->locale('id')
            ->isoFormat('MMMM YYYY');

        return [
            'No',
            'Nama Karyawan',
            'NIP',
            'Jabatan',
            "Hadir\n(hari)",
            "Terlambat\n(hari)",
            "Alpa\n(hari)",
            "Total Hari Kerja\n({$this->totalHariKerja} hari)",
            "Kehadiran\n(%)",
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $row['karyawan']->user->name          ?? '-',
            $row['karyawan']->nip                 ?? '-',
            $row['karyawan']->jabatan->nama_jabatan ?? '-',
            $row['hadir'],
            $row['terlambat'],
            $row['alpa'],
            $this->totalHariKerja,
            $row['persentase'] . '%',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $this->karyawanList->count() + 1;

        // ── Header row ────────────────────────────────────────────────────────
        $sheet->getStyle('A1:I1')->applyFromArray([
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1e3a8a'],
            ],
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(38);

        // ── Warnai baris data berdasarkan % kehadiran ─────────────────────────
        $row = 2;
        foreach ($this->karyawanList as $r) {
            $pct = $r['persentase'];
            $color = match (true) {
                $pct >= 90  => 'f0fdf4',   // hijau (kehadiran bagus)
                $pct >= 70  => 'fefce8',   // kuning (cukup)
                default     => 'fff1f2',   // merah (kurang)
            };
            $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color]],
            ]);

            // Warna teks kolom persentase
            $pctColor = match (true) {
                $pct >= 90  => '16a34a',
                $pct >= 70  => 'b45309',
                default     => 'dc2626',
            };
            $sheet->getStyle("I{$row}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => $pctColor]],
            ]);

            $row++;
        }

        // ── Border seluruh tabel ──────────────────────────────────────────────
        $sheet->getStyle("A1:I{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'e2e8f0'],
                ],
            ],
        ]);

        // ── Alignment ─────────────────────────────────────────────────────────
        // No → center
        $sheet->getStyle("A2:A{$lastRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        // Angka → center
        foreach (['E', 'F', 'G', 'H', 'I'] as $col) {
            $sheet->getStyle("{$col}2:{$col}{$lastRow}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // ── Baris total di paling bawah ───────────────────────────────────────
        $totalRow = $lastRow + 1;
        $sheet->setCellValue("A{$totalRow}", '');
        $sheet->setCellValue("B{$totalRow}", 'TOTAL');
        $sheet->setCellValue("E{$totalRow}", $this->karyawanList->sum('hadir'));
        $sheet->setCellValue("F{$totalRow}", $this->karyawanList->sum('terlambat'));
        $sheet->setCellValue("G{$totalRow}", $this->karyawanList->sum('alpa'));
        $sheet->setCellValue("H{$totalRow}", '');
        $sheet->setCellValue("I{$totalRow}", '');

        $sheet->getStyle("A{$totalRow}:I{$totalRow}")->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'e0e7ff']],
            'font' => ['bold' => true],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'c7d2fe'],
                ],
            ],
        ]);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 28,
            'C' => 16,
            'D' => 20,
            'E' => 10,
            'F' => 12,
            'G' => 10,
            'H' => 16,
            'I' => 12,
        ];
    }

    public function title(): string
    {
        return 'Rekap ' . $this->bulan;
    }
}
