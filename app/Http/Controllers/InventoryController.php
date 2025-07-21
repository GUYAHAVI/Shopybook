<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryItem::where('business_id', Auth::user()->business->id)
                              ->with(['transactions' => function($q) {
                                  $q->latest()->limit(5);
                              }]);

        // Apply filters
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('brand', 'like', '%' . $request->search . '%');
            });
        }

        $items = $query->orderBy('name')->paginate(20);

        // Get summary statistics
        $summary = [
            'total_items' => InventoryItem::where('business_id', Auth::user()->business->id)->count(),
            'low_stock_items' => InventoryItem::where('business_id', Auth::user()->business->id)->lowStock()->count(),
            'out_of_stock_items' => InventoryItem::where('business_id', Auth::user()->business->id)->outOfStock()->count(),
            'total_value' => InventoryItem::where('business_id', Auth::user()->business->id)
                                         ->selectRaw('SUM(current_quantity * unit_cost) as total')
                                         ->value('total') ?? 0,
            'expiring_soon' => InventoryItem::where('business_id', Auth::user()->business->id)->expiringSoon()->count(),
        ];

        return view('inventory.index', compact('items', 'summary'));
    }

    public function create()
    {
        return view('inventory.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'unit_type' => 'required|string',
            'unit_cost' => 'required|numeric|min:0',
            'current_quantity' => 'required|integer|min:0',
            'minimum_quantity' => 'required|integer|min:0',
            'maximum_quantity' => 'nullable|integer|min:0',
            'supplier' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:today',
            'storage_location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $data['business_id'] = Auth::user()->business->id;

        // Set initial status based on quantity
        if ($data['current_quantity'] == 0) {
            $data['status'] = 'out_of_stock';
        } elseif ($data['current_quantity'] <= $data['minimum_quantity']) {
            $data['status'] = 'low_stock';
        } else {
            $data['status'] = 'active';
        }

        $item = InventoryItem::create($data);

        // Create initial transaction record if quantity > 0
        if ($data['current_quantity'] > 0) {
            InventoryTransaction::create([
                'business_id' => $data['business_id'],
                'inventory_item_id' => $item->id,
                'transaction_type' => 'purchase',
                'quantity' => $data['current_quantity'],
                'unit_cost' => $data['unit_cost'],
                'total_cost' => $data['current_quantity'] * $data['unit_cost'],
                'transaction_date' => $data['purchase_date'] ?? now(),
                'notes' => 'Initial stock entry',
            ]);
        }

        return redirect()->route('inventory.index')->with('success', 'Inventory item added successfully.');
    }

    public function show(InventoryItem $inventory)
    {
        $this->authorize('view', $inventory);
        
        $transactions = $inventory->transactions()
                                 ->with(['staff'])
                                 ->orderByDesc('transaction_date')
                                 ->orderByDesc('created_at')
                                 ->paginate(20);

        return view('inventory.show', compact('inventory', 'transactions'));
    }

    public function edit(InventoryItem $inventory)
    {
        $this->authorize('update', $inventory);
        return view('inventory.edit', compact('inventory'));
    }

    public function update(Request $request, InventoryItem $inventory)
    {
        $this->authorize('update', $inventory);
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'unit_type' => 'required|string',
            'unit_cost' => 'required|numeric|min:0',
            'minimum_quantity' => 'required|integer|min:0',
            'maximum_quantity' => 'nullable|integer|min:0',
            'supplier' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:today',
            'storage_location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $inventory->update($data);
        $inventory->updateStatus(); // Update status based on current quantity

        return redirect()->route('inventory.show', $inventory)->with('success', 'Inventory item updated successfully.');
    }

    public function destroy(InventoryItem $inventory)
    {
        $this->authorize('delete', $inventory);
        
        $inventory->transactions()->delete();
        $inventory->delete();

        return redirect()->route('inventory.index')->with('success', 'Inventory item deleted successfully.');
    }

    public function adjustQuantity(Request $request, InventoryItem $inventory)
    {
        $this->authorize('update', $inventory);
        
        $data = $request->validate([
            'adjustment_type' => 'required|in:add,subtract',
            'quantity' => 'required|integer|min:1',
            'transaction_type' => 'required|string',
            'notes' => 'nullable|string',
            'unit_cost' => 'nullable|numeric|min:0',
        ]);

        $quantity = $data['adjustment_type'] == 'subtract' ? -$data['quantity'] : $data['quantity'];
        $unitCost = $data['unit_cost'] ?? $inventory->unit_cost;

        // Update quantity and create transaction
        $inventory->updateQuantity($quantity, $data['transaction_type'], $data['notes'], Auth::user()->staff->id ?? null);

        $action = $data['adjustment_type'] == 'add' ? 'added to' : 'removed from';
        return redirect()->route('inventory.show', $inventory)
                        ->with('success', "Successfully {$action} {$inventory->name} inventory.");
    }

    public function lowStock()
    {
        $items = InventoryItem::where('business_id', Auth::user()->business->id)
                             ->lowStock()
                             ->orderBy('current_quantity')
                             ->paginate(20);

        return view('inventory.low-stock', compact('items'));
    }

    public function reports()
    {
        $businessId = Auth::user()->business->id;
        
        // Monthly usage report
        $monthlyUsage = InventoryTransaction::where('business_id', $businessId)
                                           ->where('transaction_type', 'usage')
                                           ->whereMonth('transaction_date', now()->month)
                                           ->whereYear('transaction_date', now()->year)
                                           ->sum('total_cost');

        // Top used items this month
        $topUsedItems = InventoryTransaction::where('business_id', $businessId)
                                           ->where('transaction_type', 'usage')
                                           ->whereMonth('transaction_date', now()->month)
                                           ->whereYear('transaction_date', now()->year)
                                           ->with('inventoryItem')
                                           ->select('inventory_item_id', DB::raw('SUM(ABS(quantity)) as total_used'), DB::raw('SUM(total_cost) as total_cost'))
                                           ->groupBy('inventory_item_id')
                                           ->orderByDesc('total_used')
                                           ->limit(10)
                                           ->get();

        // Monthly costs by category
        $categoryCosts = InventoryTransaction::where('business_id', $businessId)
                                            ->whereIn('transaction_type', ['purchase', 'usage'])
                                            ->whereMonth('transaction_date', now()->month)
                                            ->whereYear('transaction_date', now()->year)
                                            ->with('inventoryItem')
                                            ->get()
                                            ->groupBy('inventoryItem.category')
                                            ->map(function($transactions) {
                                                return $transactions->sum('total_cost');
                                            });

        return view('inventory.reports', compact('monthlyUsage', 'topUsedItems', 'categoryCosts'));
    }
}
