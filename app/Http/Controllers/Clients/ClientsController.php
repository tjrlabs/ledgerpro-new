<?php

namespace App\Http\Controllers\Clients;

use App\Classes\ErrorData;
use App\DTO\Clients\ManageClientDTO;
use App\Http\Controllers\Controller;
use App\Repositories\ClientsPaymentsRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\SalesRepository;
use App\Repositories\AccountBalanceRepository;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Repositories\ClientsRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClientsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        public ClientsRepository $clientsRepository,
        public ClientsPaymentsRepository $paymentsRepository,
        public PaymentRepository $paymentRepository,
        public SalesRepository $salesRepository,
        public AccountBalanceRepository $accountBalanceRepository
    )
    {
    }

    /**
     * Display a listing of the clients.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $clients = Client::where('company_profile_id', session('company_profile.id'))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('clients.index', [
            'clients' => $clients,
        ]);
    }

    /**
     * Show the form for creating a new client.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Store a newly created client in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $clientDTO = ManageClientDTO::from($request->all());
        if ($clientDTO instanceof ErrorData) {
            return redirect()->back()->withErrors($clientDTO->getErrorMessages())->withInput();
        }

        $response = $this->clientsRepository->storeClient($clientDTO);
        if ($response instanceof ErrorData) {
            return redirect()->back()->withErrors($response->getErrorMessages())->withInput();
        }

        return redirect()->route('clients.index')->with('success', 'Client created successfully!');
    }

    /**
     * Show the form for editing the specified client.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit(int $id): View
    {
        $client = Client::findOrFail($id);

        // Fetch existing account balance for this client
        $accountBalance = null;
        $companyProfileId = session('company_profile.id');

        // Get the most recent account balance record for this client
        $accountBalance = \App\Models\AccountBalance::where('client_id', $client->id)
            ->where('company_profile_id', $companyProfileId)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->first();

        return view('clients.create', [
            'client' => $client,
            'isEditing' => true,
            'accountBalance' => $accountBalance
        ]);
    }

    /**
     * Update the specified client in storage.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $clientDTO = ManageClientDTO::from($request->all());
        if ($clientDTO instanceof ErrorData) {
            return redirect()->back()->withErrors($clientDTO->getErrorMessages())->withInput();
        }

        $response = $this->clientsRepository->updateClient($clientDTO, $id);
        if ($response instanceof ErrorData) {
            return redirect()->back()->withErrors($response->getErrorMessages())->withInput();
        }

        return redirect()->route('clients.index')->with('success', 'Client updated successfully!');
    }

    /**
     * Remove the specified client from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            // Use repository to find the client
            $client = $this->clientsRepository->findClient($id);

            if (!$client) {
                return redirect()->route('clients.index')
                    ->with('error', 'Client not found.');
            }

            // Check if client has any related records using repositories
            $clientPayments = $this->paymentRepository->getPaymentsByClient($id);
            $clientSales = $this->salesRepository->getAllSales(['client_id' => $id]);

            if ($clientPayments->isNotEmpty() || $clientSales->isNotEmpty()) {
                return redirect()->route('clients.index')
                    ->with('error', 'Cannot delete client as it has associated payments or sales records.');
            }

            // Delete associated account balances using repository
            $deleteBalancesResult = $this->accountBalanceRepository->deleteAccountBalancesByClient($id);

            // Delete the client using repository
            $result = $this->clientsRepository->deleteClient($id);

            if ($result instanceof \App\Classes\ErrorData) {
                return redirect()->route('clients.index')
                    ->with('error', $result->getErrorMessages()[0] ?? 'Failed to delete client.');
            }

            return redirect()->route('clients.index')
                ->with('success', 'Client deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->route('clients.index')
                ->with('error', 'Failed to delete client. Please try again.');
        }
    }

    /**
     * Fetch available clients for a payments board.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchForBoard(Request $request)
    {
        try {
            $request->validate([
                'board_id' => 'required|integer|exists:payments_board,id'
            ]);

            $boardId = $request->input('board_id');
            $companyProfileId = session('company_profile.id');

            // Use repository to get available clients for the board
            $activeClients = $this->paymentsRepository->getAvailableClientsForBoard($boardId, $companyProfileId);
            return response()->json([
                'success' => true,
                'clients' => $activeClients->map(function ($client) {
                    return [
                        'id' => $client->id,
                        'client_name' => $client->client_name,
                        'email' => $client->email,
                        'phone' => $client->phone,
                        'company_name' => $client->company_name ?? null,
                    ];
                })
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid board ID provided.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch clients: ' . $e->getMessage()
            ], 500);
        }
    }
}
