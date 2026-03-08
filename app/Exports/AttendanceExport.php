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
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AttendanceExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize
{
    protected string $tanggal;
    protected $presensi;
    protected $karyawanAlpa;
    protected int $totalHadir;

    public function __construct(string $tanggal)
    {
        $this->tanggal = $tanggal;

        // Ambil data presensi
        $this->presensi = Presensi::with(['karyawan.user', 'karyawan.jabatan', 'shift'])
            ->where('tanggal', $tanggal)
            ->get();

        // Karyawan alpa
        $hadirIds = $this->presensi->pluck('karyawan_id')->toArray();
        $this->karyawanAlpa = Karyawan::with(['user', 'jabatan', 'jadwalShift.shift'])
            ->where('status', 'aktif')
            ->whereNotIn('id', $hadirIds)
            ->get();

        $this->totalHadir = $this->presensi->count();
    }

    public function collection()
    {
        // Gabung: karyawan hadir + alpa dalam satu collection
        $rows = collect();

        // Karyawan hadir/terlambat
        foreach ($this->presensi as $p) {
            $rows->push([
                'type'    => 'presensi',
                'data'    => $p,
                'jadwal'  => null,
            ]);
        }

        // Karyawan alpa
        foreach ($this->karyawanAlpa as $k) {
            $jadwal = $k->jadwalShift->first(
                fn($j) =>
                $j->tanggal_mulai <= $this->tanggal &&
                    $j->tanggal_selesai >= $this->tanggal
            );
            $rows->push([
                'type'   => 'alpa',
                'data'   => $k,
                'jadwal' => $jadwal,
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Karyawan',
            'NIP',
            'Jabatan',
            'Shift',
            'Waktu Masuk',
            'Waktu Keluar',
            'Jarak dari Kantor (m)',
            'Status',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        if ($row['type'] === 'presensi') {
            $p = $row['data'];
            return [
                $no,
                $p->karyawan->user->name       ?? '-',
                $p->karyawan->nip              ?? '-',
                $p->karyawan->jabatan->nama_jabatan ?? '-',
                $p->shift->nama_shift          ?? '-',
                $p->jam_masuk  ? Carbon::parse($p->jam_masuk)->format('H:i')  : '-',
                $p->jam_pulang ? Carbon::parse($p->jam_pulang)->format('H:i') : '-',
                $p->jarak_dari_kantor ? round($p->jarak_dari_kantor) : '-',
                ucfirst($p->status ?? '-'),
            ];
        }

        // Alpa
        $k      = $row['data'];
        $jadwal = $row['jadwal'];
        return [
            $no,
            $k->user->name                    ?? '-',
            $k->nip                           ?? '-',
            $k->jabatan->nama_jabatan         ?? '-',
            $jadwal?->shift->nama_shift       ?? '-',
            '-',
            '-',
            '-',
            'Alpa',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $this->presensi->count() + $this->karyawanAlpa->count() + 1;

        // Warnai baris header
        $sheet->getStyle('A1:I1')->applyFromArray([
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1e40af'],
            ],
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Warnai baris data berdasarkan status
        $row = 2;
        foreach ($this->presensi as $p) {
            $color = match ($p->status) {
                'hadir'     => 'f0fdf4',
                'terlambat' => 'fefce8',
                default     => 'fff1f2',
            };
            $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color]],
            ]);
            $row++;
        }

        // Baris alpa → merah muda
        foreach ($this->karyawanAlpa as $k) {
            $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'fee2e2']],
            ]);
            $row++;
        }

        // Border seluruh tabel
        $sheet->getStyle("A1:I{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'e2e8f0'],
                ],
            ],
        ]);

        // Center kolom No dan Status
        $sheet->getStyle("A2:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("I2:I{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }

    public function title(): string
    {
        return 'Attendance ' . $this->tanggal;
    }
}
