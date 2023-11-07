<table>
    <thead>
    <tr>
    <th nowrap>Fecha</th>
    <th>Factura No.</th>
    <th>Cliente </th>
    <th>Total</th>
    <th>Estado</th>
    </tr>
    </thead>
    <tbody>  
        @foreach($bills as $bill)
            <tr>
                <td>{{ $bill->date }}</td>
                <td>{{ $bill->prefix_and_number_bill }}</td>
                <td>{{ $bill->owner->identification . ' - ' . $bill->owner->names . ' ' . $bill->owner->surnames }}</td>
                <td>{{ $bill->total }}</td>
                <td>{{ $bill->cancelled ? 'Anulada' : 'Generada' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>