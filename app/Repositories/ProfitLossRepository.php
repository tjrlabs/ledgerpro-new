<?php

namespace App\Repositories;

use App\Classes\CurrentCompany;
use App\Classes\ErrorData;
use App\Classes\ResponseData;
use App\Classes\SuccessData;
use App\Models\Reports\PaymentsBoard;
use App\Models\Reports\ProfitLossReport;
use App\Models\Transaction;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class ProfitLossRepository
{
    public function __construct() {}

    public function getAllProfitLossReports(array $filters = []): Collection
    {
        $query = ProfitLossReport::with('paymentsBoard')
            ->forCompany(CurrentCompany::id())
            ->orderByDesc('start_date');

        if (!empty($filters['month_year'])) {
            $query->byMonthYear($filters['month_year']);
        }

        return $query->get();
    }

    public function storeProfitLossReport(array $profitLossData): ResponseData
    {
        try {
            $companyProfileId = CurrentCompany::id();
            $boardMonthYear = $profitLossData['board_month_year'];

            $existingReport = ProfitLossReport::forCompany($companyProfileId)
                ->byMonthYear($boardMonthYear)
                ->first();

            if ($existingReport) {
                return new ErrorData(['P/L report already exists for this period.']);
            }

            $paymentsBoard = PaymentsBoard::forCompany($companyProfileId)
                ->byMonthYear($boardMonthYear)
                ->first();

            if (!$paymentsBoard) {
                return new ErrorData(['Payments board not found for this period.']);
            }

            if (!$paymentsBoard->finalized_at) {
                return new ErrorData(['Finalize the payments board for this period before creating P/L.']);
            }

            $calculatedData = $this->calculateProfitLossData($paymentsBoard);

            $profitLossReport = ProfitLossReport::create([
                'company_profile_id' => $companyProfileId,
                'payments_board_id' => $paymentsBoard->id,
                'board_month_year' => $paymentsBoard->board_month_year,
                'start_date' => $paymentsBoard->start_date,
                'end_date' => $paymentsBoard->end_date,
                'total_cash_sales' => $calculatedData['total_cash_sales'],
                'total_pre_gst_sales' => $calculatedData['total_pre_gst_sales'],
                'total_gst' => $calculatedData['total_gst'],
                'total_income' => $calculatedData['total_income'],
                'total_expenses' => $calculatedData['total_expenses'],
                'profit_loss' => $calculatedData['profit_loss'],
            ]);

            return new SuccessData($profitLossReport->load('paymentsBoard')->toArray());
        } catch (Exception $e) {
            Log::error('Failed to create P/L report: ' . $e->getMessage());

            return new ErrorData(['Failed to create P/L report: ' . $e->getMessage()]);
        }
    }

    public function findProfitLossReport(int $id): ?ProfitLossReport
    {
        return ProfitLossReport::with('paymentsBoard')
            ->forCompany(CurrentCompany::id())
            ->find($id);
    }

    public function deleteProfitLossReport(int $id): ResponseData
    {
        try {
            $profitLossReport = ProfitLossReport::forCompany(CurrentCompany::id())->find($id);

            if (!$profitLossReport) {
                return new ErrorData(['P/L report not found.']);
            }

            $profitLossReport->delete();

            return new SuccessData(['message' => 'P/L report deleted successfully.']);
        } catch (Exception $e) {
            Log::error('Failed to delete P/L report: ' . $e->getMessage());

            return new ErrorData(['Failed to delete P/L report: ' . $e->getMessage()]);
        }
    }

    private function calculateProfitLossData(PaymentsBoard $paymentsBoard): array
    {
        $totalExpenses = Transaction::query()
            ->where('company_profile_id', CurrentCompany::id())
            ->where('transaction_type', 'expense')
            ->whereBetween('transaction_date', [$paymentsBoard->start_date, $paymentsBoard->end_date])
            ->sum('total_amount');

        $totalCashSales = (float) $paymentsBoard->total_cash_sales;
        $totalPreGstSales = (float) $paymentsBoard->total_pre_gst_amount;
        $totalGst = (float) $paymentsBoard->total_gst_amount;
        $totalIncome = $totalCashSales + $totalPreGstSales + $totalGst;
        $profitLoss = $totalIncome - (float) $totalExpenses - $totalGst;

        return [
            'total_cash_sales' => $totalCashSales,
            'total_pre_gst_sales' => $totalPreGstSales,
            'total_gst' => $totalGst,
            'total_income' => $totalIncome,
            'total_expenses' => (float) $totalExpenses,
            'profit_loss' => $profitLoss,
        ];
    }
}
