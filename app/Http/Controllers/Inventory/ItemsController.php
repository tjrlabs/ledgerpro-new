<?php

namespace App\Http\Controllers\Inventory;

use App\Classes\ErrorData;
use App\Classes\SuccessData;
use App\Http\Controllers\Controller;
use App\Models\Inventory\Items;
use App\Repositories\ItemsRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\DTO\Items\ManageItemDTO;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ItemsController extends Controller
{
    public function __construct(public ItemsRepository $itemsRepository)
    {
    }

    /**
     * Display a listing of items.
     *
     * @param Request $request
     * @return View|JsonResponse
     */
    public function index(Request $request): View|JsonResponse
    {
        $allItems = Items::where('company_profile_id', session('company_profile.id'))
            ->orderBy('created_at', 'desc')
            ->get();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => $allItems
            ]);
        }

        return view('pages.items.index', [
            'items' => $allItems,
        ]);
    }

    /**
     * Show the form for creating a new item.
     *
     * @param Request $request
     * @return View|JsonResponse
     */
    public function create(Request $request): View|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => 'This endpoint is only for web interface. Use POST to /items to create an item.'
            ], 400);
        }

        return view('pages.items.create');
    }

    /**
     * Store a newly created item in storage.
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $items = ManageItemDTO::from($request->all());
        if($items instanceof ErrorData) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $items->getErrorMessages()
                ], 422);
            }

            return redirect()->back()->withErrors($items->getErrorMessages())->withInput();
        }

        $response = $this->itemsRepository->storeItem($items);
        if ($response instanceof ErrorData) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to create item',
                    'errors' => $response->getErrorMessages()
                ], 500);
            }

            return redirect()->back()->withErrors($response->getErrorMessages())->withInput();
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Item created successfully',
                'data' => $response->data
            ], 201);
        }

        return redirect()->route('items.index')->with('success', 'Item created successfully!');
    }


    /**
     * Show the form for editing the specified item.
     *
     * @param Request $request
     * @param int $id
     * @return View|JsonResponse
     */
    public function edit(Request $request, int $id): View|JsonResponse|RedirectResponse
    {
        try {
            $item = Items::findOrFail($id);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This endpoint is only for web interface. Use PUT to /items/{id} to update an item.'
                ], 400);
            }

            return view('pages.items.create', [
                'item' => $item,
                'isEditing' => true
            ]);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Item not found'
                ], 404);
            }

            return redirect()->route('items.index')->with('error', 'Item not found');
        }
    }

    /**
     * Update the specified item in storage.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse|JsonResponse
     */
    public function update(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $items = ManageItemDTO::from($request->all());
        if($items instanceof ErrorData) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $items->getErrorMessages()
                ], 422);
            }

            return redirect()->back()->withErrors($items->getErrorMessages())->withInput();
        }

        $response = $this->itemsRepository->updateItem($items, $id);
        if ($response instanceof ErrorData) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to update item',
                    'errors' => $response->getErrorMessages()
                ], 500);
            }

            return redirect()->back()->withErrors($response->getErrorMessages())->withInput();
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Item updated successfully',
                'data' => $response->getData()
            ]);
        }

        return redirect()->route('items.index')->with('success', 'Item updated successfully!');
    }

    /**
     * Remove the specified item from storage.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse|JsonResponse
     */
    public function destroy(Request $request, int $id): RedirectResponse|JsonResponse
    {
        try {
            $item = Items::findOrFail($id);
            $item->delete();

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Item deleted successfully'
                ]);
            }

            return redirect()->route('items.index')->with('success', 'Item deleted successfully!');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to delete item: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('items.index')->with('error', 'Failed to delete item');
        }
    }
}
