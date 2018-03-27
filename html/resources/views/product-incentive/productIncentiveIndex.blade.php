@include('layouts.header', ['title' => 'Product Incentives - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Product Incentives</h1>

    @if(Session::has('product-incentive-created'))
        <div class="note success">Product incentive has been created</div>
    @endif

    @if(Session::has('product-incentive-deleted'))
        <div class="note error">Product incentive has been removed</div>
    @endif

    <div>
        <a href="{{ env('APP_HOME_URL') }}/product/incentive/create" class="btn btn-green">Create</a>
    </div>

    <table id="productIncentiveTable" class="display initTable">
        <thead>
            <tr>
                <th data-width="5%">No.</th>
                <th data-width="25%">Product</th>
                <th data-width="25%">Dealer Channel</th>
                <th data-width="20%">Value</th>
                <th data-width="25%">Options</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productIncentives as $key => $incentive)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $productModels[$incentive->product_model_ID] }}</td>
                    <td>{{ $dealerChannels[$incentive->dealer_channel_ID] }}</td>
                    <td>{{ number_format($incentive->value) }}</td>
                    <td>
                        <a href="{{ env('APP_HOME_URL') }}/product/incentive/edit?ID={{ $incentive->ID }}" class="btn btn-green">Edit</a>
                        <a href="{{ env('APP_HOME_URL') }}/product/incentive/remove?ID={{ $incentive->ID }}" class="btn btn-red">Remove</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

@include('layouts.footer');