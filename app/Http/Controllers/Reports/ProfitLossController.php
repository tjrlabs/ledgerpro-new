<?php

namespace App\Http\Controllers\Reports;

use App\Classes\ErrorData;
use App\Classes\SuccessData;
use App\DTO\Reports\ProfitLossDTO;
use App\Http\Controllers\Controller;
use App\Repositories\ProfitLossRepository;
use Illuminate\Http\Request;

class ProfitLossController extends Controller
{
    public function __construct(
        protected ProfitLossRepository $profitLossRepository,
    ) {}

    public function index()
    {
        $profitLossReports = $this->profitLossRepository->getAllProfitLossReports(request()->all());

        return view('pages.reports.profit_loss.index', compact('profitLossReports'));
    }

    public function create()
    {
        $currentMonth = date('m');
        $currentYear = date('Y');

        $monthOptions = [
            '01' => 'January',
            '02' => 'February',
            '03' => 'March',
            '04' => 'April',
            '05' => 'May',
            '06' => 'June',
            '07' => 'July',
            '08' => 'August',
            '09' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December',
        ];

        $yearOptions = [];
        for ($i = $currentYear; $i <= $currentYear + 5; $i++) {
            $yearOptions[$i] = $i;
        }

        return view('pages.reports.profit_loss.create', compact(
            'monthOptions',
            'yearOptions',
            'currentMonth',
            'currentYear',
        ));
    }

    public function store(Request $request)
    {
        $dto = ProfitLossDTO::from([
            'month' => $request->profit_loss_month,
            'year' => $request->profit_loss_year,
        ]);

        if ($dto instanceof ErrorData) {
            return redirect()->back()
                ->withErrors($dto->getErrorMessages())
                ->withInput();
        }

        $validatedDto = $dto->validate();
        if ($validatedDto instanceof ErrorData) {
            return redirect()->back()
                ->withErrors($validatedDto->getErrorMessages())
                ->withInput();
        }

        $result = $this->profitLossRepository->storeProfitLossReport($validatedDto->toArray());

        if ($result instanceof SuccessData) {
            return redirect()->route('reports.profit-loss.show', $result->data['id'])
                ->with('success', 'P/L report created successfully for ' . $validatedDto->getFormattedMonthYear());
        }

        return redirect()->back()
            ->withErrors($result->getErrorMessages())
            ->withInput();
    }

    public function show(int $id)
    {
        $profitLossReport = $this->profitLossRepository->findProfitLossReport($id);

        if (!$profitLossReport) {
            return redirect()->route('reports.profit-loss')
                ->withErrors(['P/L report not found.']);
        }

        return view('pages.reports.profit_loss.show', compact('profitLossReport'));
    }

    public function destroy(int $id)
    {
        $result = $this->profitLossRepository->deleteProfitLossReport($id);

        if ($result instanceof SuccessData) {
            return redirect()->route('reports.profit-loss')
                ->with('success', 'P/L report deleted successfully.');
        }

        return redirect()->route('reports.profit-loss')
            ->with('error', 'Failed to delete P/L report.');
    }
}
