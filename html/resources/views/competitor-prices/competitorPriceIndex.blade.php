@include('layouts.header', ['title' => 'Competitor Brands - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Competitor Brands</h1>

    @if(Session::has('brand-created'))
        <div class="note success">Brand has been created</div>
    @endif

    @if(Session::has('brand-deleted'))
        <div class="note error">Brand has been removed</div>
    @endif

    <div>
        <a href="{{ env('APP_HOME_URL') }}/competitor-brand/create" class="btn btn-green">Create</a>
    </div>

    <table id="competitorBrandTable" class="display initTable">
        <thead>
            <tr>
                <th data-width="5%">No</th>
                <th data-width="10%">Branch</th>
                <th data-width="15%">Dealer</th>
                <th data-width="10%">Brand</th>
                <th data-width="10%">Product Category</th>
                <th data-width="15%">Model Name</th>
                <th data-width="10%">Date</th>
                <th data-width="25%">Options</th>
            </tr>
        </thead>
        <tbody>
            @foreach($prices as $key => $price)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $price->branch }}</td>
                    <td>{{ $price->dealer }}</td>
                    <td>{{ $price->brand }}</td>
                    <td>{{ $price->category }}</td>
                    <td>{{ $price->model_name }}</td>
                    <td>{{ $price->date }}</td>
                    <td>
                        <a href="{{ env('APP_HOME_URL') }}/competitor-price/remove?ID={{ $price->ID }}" class="btn btn-red">Remove</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

@include('layouts.footer');
