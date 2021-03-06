@extends('driver.default')
@section('header')
<div class="container text-muted">
    <div class="d-flex justify-content-between my-4">
        <div>
            <h1>Driver Orders</h1>
        </div>
    </div>
</div>
@endsection

@section('content')

<div class="container text-muted">
    <div class="row my-4">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">{{ __('Customer Name') }}</th>
                    <th scope="col">{{ __('Customer Comments') }}</th>
                    <th scope="col">{{ __('Mileage Trip') }}</th>
                    <th scope="col">{{ __('Earnings') }}</th>
                    <th scope="col">{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody>
                @php $row = $orders->firstItem();
                @endphp @foreach ($orders as $order)
                <tr>
                    <th scope="{{ $row }}">{{ $row }}</th>
                    <td>{{ $order->delivery_name }}</td>
                    <td>{{ $order->delivery_comments }}</td>
                    <td>{{ $order->mileage_trip }}</td>
                    <td>${{ number_format((float)$order->delivery_price / 2, 2, '.', '') }}</td>
                    <td>{{ ucfirst(strtolower($order->status)) }}</td>
                </tr>
                @php $row++;
                @endphp @endforeach
            </tbody>
        </table>
        {{ $orders->links() }}
    </div>
    <div>
        <h3>Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{$orders->total()}} total orders</h3>
    </div>
</div>
</P>
@endsection