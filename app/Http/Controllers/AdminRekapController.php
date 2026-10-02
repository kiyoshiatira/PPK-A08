<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Reservation;
use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Barryvdh\DomPDF\Facade\Pdf; 
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RekapExport;
use App\Exports\ReportExport;

class AdminRekapController extends Controller
{
    public function index(Request $request)
    {
        $selectedPeriode = $request->input('periode', Carbon::now()->format('Y-m'));
        $selectedFacility = $request->input('facility_id', 'all');
        // Menangkap nilai filter Tipe Laporan (Default: Keduanya)
        $selectedTipe = $request->input('tipe_laporan', 'all'); 

        $year = substr($selectedPeriode, 0, 4);
        $month = substr($selectedPeriode, 5, 2);

        $facilities = Facility::all();

        $periodeBulan = [];
        for ($i = 0; $i < 12; $i++) {
            $date = Carbon::now()->subMonths($i);
            $periodeBulan[$date->format('Y-m')] = $date->translatedFormat('F Y');
        }

        $facilityQuery = Facility::query();
        if ($selectedFacility !== 'all') {
            $facilityQuery->where('id', $selectedFacility);
        }

        $occupanciesQuery = $facilityQuery->withCount(['reservations' => function($query) use ($year, $month) {
            $query->whereIn('status', ['Approved', 'Disetujui'])
                  ->whereYear('reservation_date', $year)
                  ->whereMonth('reservation_date', $month);
        }]);

        $allOccupancies = $occupanciesQuery->get();
        $maxReservations = $allOccupancies->max('reservations_count') ?: 1;
        $totalPeminjaman = $allOccupancies->sum('reservations_count');
        $fasilitasTerpadat = $allOccupancies->sortByDesc('reservations_count')->first()->name ?? 'Belum ada data';

        $occupancies = $occupanciesQuery->orderBy('reservations_count', 'desc')
            ->paginate(5, ['*'], 'occ_page')->appends(request()->query());

        $reservationDetails = collect();
        if ($selectedFacility !== 'all') {
            $reservationDetails = Reservation::with('user')
                ->where('facility_id', $selectedFacility)
                ->whereIn('status', ['Approved', 'Disetujui']) 
                ->whereYear('reservation_date', $year)
                ->whereMonth('reservation_date', $month)
                ->orderBy('reservation_date', 'asc')
                ->orderBy('start_time', 'asc')
                ->paginate(5, ['*'], 'res_page')->appends(request()->query());
        }

        $reportQuery = Report::with('facility')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month);

        if ($selectedFacility !== 'all') {
            $reportQuery->where('facility_id', $selectedFacility);
        }

        $allReports = $reportQuery->orderBy('created_at', 'desc')->get();
        $totalKerusakan = $allReports->count();
        
        $kerusakanDataCollection = $allReports->groupBy('facility_id')->map(function($group) {
            return (object)[
                'nama' => $group->first()->facility->name ?? 'Fasilitas Terhapus',
                'jumlah' => $group->count()
            ];
        })->sortByDesc('jumlah')->values();

        $pageKer = Paginator::resolveCurrentPage('ker_page');
        $perPage = 5;
        $kerusakanData = new LengthAwarePaginator(
            $kerusakanDataCollection->forPage($pageKer, $perPage),
            $kerusakanDataCollection->count(),
            $perPage,
            $pageKer,
            ['path' => Paginator::resolveCurrentPath(), 'query' => request()->query(), 'pageName' => 'ker_page']
        );
        
        $reportDetails = collect();
        if ($selectedFacility !== 'all') {
            $reportDetails = Report::with('facility')
                ->where('facility_id', $selectedFacility)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->orderBy('created_at', 'desc')
                ->paginate(5, ['*'], 'rep_page')->appends(request()->query());
        }

        return view('admin.rekap.index', compact(
            'facilities', 
            'occupancies', 
            'maxReservations',
            'totalPeminjaman', 
            'fasilitasTerpadat', 
            'kerusakanData', 
            'totalKerusakan',
            'periodeBulan',
            'selectedPeriode',
            'selectedFacility',
            'selectedTipe',
            'reservationDetails',
            'reportDetails'
        ));
    }

    private function getExportData(Request $request)
    {
        $year = substr($request->input('periode', Carbon::now()->format('Y-m')), 0, 4);
        $month = substr($request->input('periode', Carbon::now()->format('Y-m')), 5, 2);
        $facility = $request->input('facility_id', 'all');
        $tipe = $request->input('tipe_export', 'okupansi'); 

        if ($tipe === 'kerusakan') {
            $query = Report::with('facility')->whereYear('created_at', $year)->whereMonth('created_at', $month);
            if ($facility !== 'all') $query->where('facility_id', $facility);
            return ['tipe' => 'kerusakan', 'data' => $query->orderBy('created_at', 'desc')->get()];
        } else {
            $query = Reservation::with(['user', 'facility'])->whereIn('status', ['Approved', 'Disetujui'])
                ->whereYear('reservation_date', $year)->whereMonth('reservation_date', $month);
            if ($facility !== 'all') $query->where('facility_id', $facility);
            return ['tipe' => 'okupansi', 'data' => $query->orderBy('reservation_date', 'asc')->get()];
        }
    }

    public function exportExcel(Request $request)
    {
        $export = $this->getExportData($request);
        if ($export['tipe'] === 'kerusakan') {
            return Excel::download(new ReportExport($export['data']), 'rekap_kerusakan.xlsx');
        }
        return Excel::download(new RekapExport($export['data']), 'rekap_okupansi.xlsx');
    }

    public function exportCsv(Request $request)
    {
        $export = $this->getExportData($request);
        if ($export['tipe'] === 'kerusakan') {
            return Excel::download(new ReportExport($export['data']), 'rekap_kerusakan.csv', \Maatwebsite\Excel\Excel::CSV);
        }
        return Excel::download(new RekapExport($export['data']), 'rekap_okupansi.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function exportPdf(Request $request)
    {
        $export = $this->getExportData($request);
        
        $pdf = Pdf::loadView('admin.rekap.pdf', [
            'tipe' => $export['tipe'],
            'data' => $export['data']
        ]);
        
        $filename = 'rekap_' . $export['tipe'] . '.pdf';
        return $pdf->download($filename);
    }
}