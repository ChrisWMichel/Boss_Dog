<?php

namespace App\Http\Controllers\Api;

use App\Enums\AddressType;
use App\Models\Api\Customer;
use App\Models\CustomerAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CustomerListResource;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $perPage = request('per_page', 10);
        $search = request('search', '');
        $sortField = request('sortField', 'updated_at');
        $sortDirection = request('sortDirection', 'asc');

        $query = Customer::query()
            ->with(['shippingAddress', 'billingAddress'])
            ->orderBy($sortField, $sortDirection);

            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }
            $customers = $query->paginate($perPage);

        return CustomerListResource::collection($customers);
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $customer->load(['shippingAddress', 'billingAddress']);
        
        return new CustomerListResource($customer);
    }
    

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Customer      $customer
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerRequest $request, Customer $customer)
    {

        $data = $request->validated();
    
    // Extract customer data (excluding shipping and billing)
    $customerData = collect($data)->except(['shipping', 'billing'])->toArray();
    $customerData['updated_by'] = $request->user()->id;
    
    DB::beginTransaction();
    try {
        // Handle billing address if provided
        if (isset($data['billing']) && !empty($data['billing']['address1'])) {
            $billingData = $data['billing'];
            if ($customer->billingAddress) {
                $customer->billingAddress->update($billingData);
            } else {
                $billingData['customer_id'] = $customer->user_id;
                $billingData['type'] = AddressType::Billing->value;
                CustomerAddress::create($billingData);
            }
        }
        
        // Handle shipping address if provided
        if (isset($data['shipping'])) {
            // Only create/update shipping address if address1 is not empty
            if (!empty($data['shipping']['address1'])) {
                $shippingData = $data['shipping'];
                if ($customer->shippingAddress) {
                    $customer->shippingAddress->update($shippingData);
                } else {
                    $shippingData['customer_id'] = $customer->user_id;
                    $shippingData['type'] = AddressType::Shipping->value;
                    CustomerAddress::create($shippingData);
                }
            } 
            // If shipping is empty but billing is provided, copy billing to shipping
            elseif (isset($data['billing']) && !empty($data['billing']['address1'])) {
                $shippingData = $data['billing']; // Copy billing data to shipping
                if ($customer->shippingAddress) {
                    $customer->shippingAddress->update($shippingData);
                } else {
                    $shippingData['customer_id'] = $customer->user_id;
                    $shippingData['type'] = AddressType::Shipping->value;
                    CustomerAddress::create($shippingData);
                }
            }
            // Otherwise, don't create an empty shipping address
        }
        
        $customer->update($customerData);
        DB::commit();
        
        return new CustomerListResource($customer);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::critical(__METHOD__ . ' method does not work. '. $e->getMessage());
        throw $e;
    }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Customer $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return response()->noContent();
    }

}
