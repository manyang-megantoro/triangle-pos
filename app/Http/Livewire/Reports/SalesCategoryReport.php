<?php

namespace App\Http\Livewire\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Sale\Entities\Sale;
use Modules\Sale\Entities\SaleDetails;

class SalesCategoryReport extends Component
{

    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $customers;
    public $start_date;
    public $end_date;
    public $customer_id;
    public $category_id;
    public $sale_status;
    public $payment_status;

    protected $rules = [
        'start_date' => 'required|date|before:end_date',
        'end_date'   => 'required|date|after:start_date',
    ];

    public function mount($customers, $categories) {
        $this->customers = $customers;
        $this->categories = $categories;
        $this->start_date = today()->subDays(30)->format('Y-m-d');
        $this->end_date = today()->format('Y-m-d');
        $this->customer_id = '';
        $this->category_id = '';
        $this->sale_status = '';
        $this->payment_status = '';
    }

    public function render() {
        // dd(SaleDetails::with('sale', 'product.category')
        //     ->whereHas('sale', function ($query) {
        //         return $query
        //             ->whereDate('date', '>=', $this->start_date)
        //             ->whereDate('date', '<=', $this->end_date)
        //             ->when($this->customer_id, function ($query) {
        //                 return $query->where('customer_id', $this->customer_id);
        //             })
        //             ->when($this->sale_status, function ($query) {
        //                 return $query->where('status', $this->sale_status);
        //             })
        //             ->when($this->payment_status, function ($query) {
        //                 return $query->where('payment_status', $this->payment_status);
        //             })
        //             ->orderBy('date', 'desc');
        //     })
        //     ->get()->toArray());
        $salesDetail = SaleDetails::with('sale', 'product.category')
            ->whereHas('sale', function ($query) {
                return $query
                    ->whereDate('date', '>=', $this->start_date)
                    ->whereDate('date', '<=', $this->end_date)
                    ->when($this->customer_id, function ($query) {
                        return $query->where('customer_id', $this->customer_id);
                    })
                    ->when($this->sale_status, function ($query) {
                        return $query->where('status', $this->sale_status);
                    })
                    ->when($this->payment_status, function ($query) {
                        return $query->where('payment_status', $this->payment_status);
                    })
                    ->orderBy('date', 'desc');
            })
            ->whereHas('product', function ($query) {
                return $query
                    ->when($this->category_id, function ($query) {
                        return $query->where('category_id', $this->category_id);
                    });
            });
        $totalPrice = $salesDetail->sum('sub_total')/ 100;
        $salesDetailShow = $salesDetail->paginate(10);

        return view('livewire.reports.sales-category-report', [
            'salesDetail' => $salesDetailShow,
            'totalPrice' => $totalPrice
        ]);
    }

    public function generateReport() {
        $this->validate();
        $this->render();
    }
}
