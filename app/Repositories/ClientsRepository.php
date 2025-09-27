<?php

namespace App\Repositories;

use App\Classes\ResponseData;
use App\Classes\SuccessData;
use App\Classes\ErrorData;
use App\DTO\Clients\ManageClientDTO;
use App\Models\Client;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;

class ClientsRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private AccountBalanceRepository $accountBalanceRepository
    ){}

    /**
     * Get all clients ordered by name
     *
     * @return Collection
     */
    public function getAllClients(): Collection
    {
        return Client::orderBy('client_name')->get();
    }

    /**
     * Store a new client in the database
     *
     * @param ManageClientDTO $clientDTO
     * @return ResponseData
     */
    public function storeClient(ManageClientDTO $clientDTO): ResponseData
    {
        try {
            // Create a new client using the DTO data
            $client = new Client();
            $client->company_profile_id = session('company_profile.id');
            $client->client_name = $clientDTO->clientName;
            $client->display_name = $clientDTO->displayName;
            $client->client_email = $clientDTO->clientEmail;
            $client->client_phone = $clientDTO->clientPhone;
            $client->client_type = $clientDTO->clientType;
            $client->client_tax_number = $clientDTO->clientTaxNumber;
            $client->billing_address = $clientDTO->billingAddress;
            $client->shipping_address = $clientDTO->shippingAddress;
            $client->is_active = $clientDTO->isActive ? 1 : 0;

            // Save the client to the database
            $client->save();

            // Handle account balance creation
            $this->handleAccountBalance($client, $clientDTO);

            // Return success response with the created client
            return new SuccessData(['message' => 'Client created successfully', 'client' => $client->toArray()]);
        } catch (Exception $e) {
            // Log the error
            Log::error('Failed to create client: ' . $e->getMessage());

            // Return error response
            return new ErrorData(['message' => 'Failed to create client: ' . $e->getMessage()]);
        }
    }

    /**
     * Update an existing client in the database
     *
     * @param ManageClientDTO $clientDTO
     * @param int $id
     * @return ResponseData
     */
    public function updateClient(ManageClientDTO $clientDTO, int $id): ResponseData
    {
        try {
            // Find the client to update
            $client = Client::findOrFail($id);

            // Update the client using the DTO data
            $client->client_name = $clientDTO->clientName;
            $client->display_name = $clientDTO->displayName;
            $client->client_email = $clientDTO->clientEmail;
            $client->client_phone = $clientDTO->clientPhone;
            $client->client_type = $clientDTO->clientType;
            $client->client_tax_number = $clientDTO->clientTaxNumber;
            $client->billing_address = $clientDTO->billingAddress;
            $client->shipping_address = $clientDTO->shippingAddress;
            $client->is_active = $clientDTO->isActive ? 1 : 0;

            // Save the updated client
            $client->save();

            // Handle account balance creation/update for editing
            $this->handleAccountBalance($client, $clientDTO);

            // Return success response with the updated client
            return new ResponseData(new SuccessData($client->toArray()));
        } catch (Exception $e) {

            // Return error response
            return new ErrorData(['message' => 'Failed to update client: ' . $e->getMessage()]);
        }
    }

    /**
     * Handle account balance creation based on DTO settings
     *
     * @param Client $client
     * @param ManageClientDTO $clientDTO
     * @return void
     */
    private function handleAccountBalance(Client $client, ManageClientDTO $clientDTO): void
    {
        try {
            $companyProfileId = session('company_profile.id');

            if ($clientDTO->addOpeningBalance &&
                $clientDTO->accountBalance !== null &&
                $clientDTO->applicableMonth !== null &&
                $clientDTO->applicableYear !== null) {

                // Create account balance with user-specified values
                $this->accountBalanceRepository->storeOrUpdateAccountBalance(
                    $companyProfileId,
                    $client->id,
                    $clientDTO->applicableMonth,
                    $clientDTO->applicableYear,
                    $clientDTO->accountBalance
                );

            } else {
                // Create default account balance with current month/year and 0 balance
                $currentMonth = (int) date('n');
                $currentYear = (int) date('Y');

                $this->accountBalanceRepository->storeOrUpdateAccountBalance(
                    $companyProfileId,
                    $client->id,
                    $currentMonth,
                    $currentYear,
                    0.00
                );
            }
        } catch (Exception $e) {
            // Log the error but don't fail the client creation/update
            Log::error('Failed to handle account balance: ' . $e->getMessage());
        }
    }

    /**
     * Find a client by ID
     *
     * @param int $id
     * @return Client|null
     */
    public function findClient(int $id): ?Client
    {
        return Client::where('id', $id)
            ->where('company_profile_id', session('company_profile.id'))
            ->first();
    }

    /**
     * Delete a client from the database
     *
     * @param int $id
     * @return ResponseData
     */
    public function deleteClient(int $id): ResponseData
    {
        try {
            $client = Client::where('id', $id)
                ->where('company_profile_id', session('company_profile.id'))
                ->first();

            if (!$client) {
                return new ErrorData(['Client not found']);
            }

            $client->delete();

            return new SuccessData(['message' => 'Client deleted successfully']);
        } catch (Exception $e) {
            Log::error('Failed to delete client: ' . $e->getMessage());
            return new ErrorData(['Failed to delete client: ' . $e->getMessage()]);
        }
    }

    /**
     * Check if client has any related records
     *
     * @param int $clientId
     * @return array
     */
    public function hasRelatedRecords(int $clientId): array
    {
        // This method still needs to use repositories, but for now we'll keep it simple
        // since we're focusing on the controller changes
        $hasPayments = \App\Models\Payment::where('client_id', $clientId)->exists();
        $hasSales = \App\Models\Sale::where('client_id', $clientId)->exists();

        return [
            'hasPayments' => $hasPayments,
            'hasSales' => $hasSales,
            'hasAnyRelated' => $hasPayments || $hasSales
        ];
    }
}
