<?php

namespace App\Repositories;

use App\Classes\ResponseData;
use App\Classes\SuccessData;
use App\Classes\ErrorData;
use App\Models\AccountBalance;
use App\Models\Client;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;

class AccountBalanceRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(){}

    /**
     * Get all account balances for a specific company
     *
     * @param int $companyProfileId
     * @return Collection
     */
    public function getAllAccountBalances(int $companyProfileId): Collection
    {
        return AccountBalance::with(['client', 'companyProfile'])
            ->forCompany($companyProfileId)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
    }

    /**
     * Get account balances for a specific client
     *
     * @param int $clientId
     * @param int $companyProfileId
     * @return Collection
     */
    public function getClientAccountBalances(int $clientId, int $companyProfileId): Collection
    {
        return AccountBalance::with(['client', 'companyProfile'])
            ->forClient($clientId)
            ->forCompany($companyProfileId)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
    }

    /**
     * Get account balance for a specific period
     *
     * @param int $clientId
     * @param int $companyProfileId
     * @param int $month
     * @param int $year
     * @return AccountBalance|null
     */
    public function getAccountBalanceForPeriod(int $clientId, int $companyProfileId, int $month, int $year): ?AccountBalance
    {
        return AccountBalance::forClient($clientId)
            ->forCompany($companyProfileId)
            ->forPeriod($month, $year)
            ->first();
    }

    /**
     * Store or update account balance
     *
     * @param int $companyProfileId
     * @param int $clientId
     * @param int $month
     * @param int $year
     * @param float $openingBalance
     * @return ResponseData
     */
    public function storeOrUpdateAccountBalance(int $companyProfileId, int $clientId, int $month, int $year, float $openingBalance): ResponseData
    {
        try {
            // Validate that the client exists and belongs to the company
            $client = Client::where('id', $clientId)
                ->where('company_profile_id', $companyProfileId)
                ->first();

            if (!$client) {
                return new ErrorData(['message' => 'Client not found or does not belong to this company.']);
            }

            // Create or update the account balance
            $accountBalance = AccountBalance::createOrUpdate(
                $companyProfileId,
                $clientId,
                $month,
                $year,
                $openingBalance
            );

            return new SuccessData(['message' => 'Account balance saved successfully.']);

        } catch (Exception $e) {
            return new ErrorData(['message' => 'Failed to save account balance. Please try again.']);
        }
    }

    /**
     * Delete account balance
     *
     * @param int $id
     * @param int $companyProfileId
     * @return ResponseData
     */
    public function deleteAccountBalance(int $id, int $companyProfileId): ResponseData
    {
        try {
            $accountBalance = AccountBalance::where('id', $id)
                ->forCompany($companyProfileId)
                ->first();

            if (!$accountBalance) {
                return new ErrorData(['message' => 'Account balance record not found.']);
            }

            $accountBalance->delete();

            return new SuccessData(['message' => 'Account balance deleted successfully.']);

        } catch (Exception $e) {
            return new ErrorData(['message' => 'Failed to delete account balance. Please try again.']);
        }
    }

    /**
     * Get account balances for a specific period across all clients
     *
     * @param int $companyProfileId
     * @param int $month
     * @param int $year
     * @return Collection
     */
    public function getAccountBalancesForPeriod(int $companyProfileId, int $month, int $year): Collection
    {
        return AccountBalance::with(['client', 'companyProfile'])
            ->forCompany($companyProfileId)
            ->forPeriod($month, $year)
            ->orderBy('opening_balance', 'desc')
            ->get();
    }

    /**
     * Check if account balance exists for a client in a specific period
     *
     * @param int $clientId
     * @param int $companyProfileId
     * @param int $month
     * @param int $year
     * @return bool
     */
    public function accountBalanceExists(int $clientId, int $companyProfileId, int $month, int $year): bool
    {
        return AccountBalance::forClient($clientId)
            ->forCompany($companyProfileId)
            ->forPeriod($month, $year)
            ->exists();
    }

    /**
     * Get total opening balance for a company in a specific period
     *
     * @param int $companyProfileId
     * @param int $month
     * @param int $year
     * @return float
     */
    public function getTotalOpeningBalanceForPeriod(int $companyProfileId, int $month, int $year): float
    {
        return AccountBalance::forCompany($companyProfileId)
            ->forPeriod($month, $year)
            ->sum('opening_balance');
    }

    /**
     * Get clients with opening balances for a specific period
     *
     * @param int $companyProfileId
     * @param int $month
     * @param int $year
     * @return Collection
     */
    public function getClientsWithOpeningBalances(int $companyProfileId, int $month, int $year): Collection
    {
        return AccountBalance::with('client')
            ->forCompany($companyProfileId)
            ->forPeriod($month, $year)
            ->where('opening_balance', '>', 0)
            ->get();
    }

    /**
     * Delete all account balances for a specific client
     *
     * @param int $clientId
     * @return bool|ErrorData
     */
    public function deleteAccountBalancesByClient(int $clientId)
    {
        try {
            AccountBalance::where('client_id', $clientId)->delete();
            return true;
        } catch (Exception $e) {

            return new ErrorData(['Failed to delete account balances for client.']);
        }
    }
}
