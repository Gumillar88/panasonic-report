@include('layouts.header', ['title' => 'Product Prices - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Product Prices</h1>

    @if(Session::has('product-price-created'))
        <div class="note success">Product price has been created</div>
    @endif

    @if(Session::has('product-price-deleted'))
        <div class="note error">Product price has been removed</div>
    @endif

    <div>
        <a href="{{ env('APP_HOME_URL') }}/product/price/create" class="btn btn-green">Create</a>
        <a href="{{ env('APP_HOME_URL') }}/product/price/upload" class="btn btn-green">Import From Excel</a>
    </div>

    <table id="userTable" class="display initTable">
        <thead>
            <tr>
                <th data-width="5%">No.</th>
                <th data-width="3%">Dealer Type</th>
                <th data-width="3%">Dealer Channel</th>
                <th data-width="15%">Product</th>
                <th data-width="5%">Price</th>
                <th data-width="15%">Options</th>
            </tr>
        </thead>
        <tbody>
            @foreach($product_prices as $key => $price)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $dealer_types[$price->dealer_type_ID] }}</td>
                    <td>{{ $dealer_channels[$price->dealer_channel_ID] }}</td>
                    <td>{{ $products[$price->product_ID] }}</td>
                    <td>{{ $price->price }}</td>
                    <td>
                        <a href="{{ env('APP_HOME_URL') }}/product/price/edit?ID={{ $price->ID }}" class="btn btn-green">Edit</a>
                        <a href="{{ env('APP_HOME_URL') }}/product/price/remove?ID={{ $price->ID }}" class="btn btn-red">Remove</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

@include('layouts.footer');
