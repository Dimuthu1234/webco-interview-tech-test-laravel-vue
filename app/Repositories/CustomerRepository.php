<?php namespace App\Repositories;


use App\Telephone;
use Bosnadev\Repositories\Eloquent\Repository;

class CustomerRepository extends Repository
{
    public function model()
    {
        return 'App\Customer';
    }

    public function getAllCustomerRecords()
    {
        return $this->model->orderBy('id', 'desc')->with(['telephones' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])->paginate(15);
    }

    public function storeNewCustomerRecord($request)
    {
        $customerArray = $request->all()[0];
        $teleArray = $request->all()[1];

        $customerRecord = new $this->model;
        $newCustomer = $customerRecord->create($customerArray);
        $telephoneNumbersArray = [];

        for ($x = 0; $x <= sizeof($teleArray) - 1; $x++) {
            $telephoneNumbersArray[] = $teleArray[$x]['name'];
        }

        $telephoneRecords = new Telephone;
        for ($y = 0; $y <= sizeof($telephoneNumbersArray) - 1; $y++) {
            $telephoneRecords->create([
                'customer_id' => $newCustomer->id,
                'telephone' => $telephoneNumbersArray[$y]
            ]);
        }
        return $customerRecord;
    }

    public function updateExistingCustomerRecord($request, $id)
    {
        $customerArray = $request->all()[0];
        $teleArray = $request->all()[1];
        $customerRecord = $this->model->where('id', $id)->first();
        $customerRecord->update($customerArray);
        $telephones = $this->model->where('id', $id)->first()->telephones;
        foreach($telephones as $tele){
            $tele->delete();
        }
        $telephoneNumbersArray = [];

        for ($x = 0; $x <= sizeof($teleArray) - 1; $x++) {
            $telephoneNumbersArray[] = $teleArray[$x]['name'];
        }

        $telephoneRecords = new Telephone;
        for ($y = 0; $y <= sizeof($telephoneNumbersArray) - 1; $y++) {
            $telephoneRecords->create([
                'customer_id' => $customerRecord->id,
                'telephone' => $telephoneNumbersArray[$y]
            ]);
        }
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
