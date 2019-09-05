<?php namespace App\Repositories;


use App\Customer;
use Bosnadev\Repositories\Eloquent\Repository;

class CustomerRepository extends Repository
{
    public function model()
    {
        return 'App\Customer';
    }

    public function getAllCustomerRecords()
    {
        return $this->model->orderBy('id', 'desc')->paginate(15);
    }

    public function storeNewCustomerRecord($request)
    {
        $customerRecord = new $this->model;
        $customerRecord->create($request->input());
        return $customerRecord;
    }

    public function updateExistingCustomerRecord($request, $id)
    {
        $customerRecord = $this->model->where('id', $id)->first();
        $customerRecord->update($request->input());
        return $customerRecord;
    }

    public function getCustomerRecordById($id)
    {
        return $this->model->where('id', $id)->first();
    }

    public function deleteCustomerRecord($id)
    {
        $deleteCustomer = $this->model->where('id', $id)->first();
        $deleteCustomer->delete();
        return $deleteCustomer;

    }

}
