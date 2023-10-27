

@foreach($billingBill->billingBillDetails as $billingBillDetail)

    <li>{{ $billingBillDetail->code . ' ' . $billingBillDetail->description }}</li>

@endforeach