@include('layouts.header', ['title' => 'Product - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Product Model</h1>

    @if(Session::has('product-created'))
        <div class="note success">Product has been created</div>
    @endif

    @if(Session::has('product-deleted'))
        <div class="note error">Product has been removed</div>
    @endif

    <div>
        <h5>Type: {{$category_name}}</h5></br>
        <a href="{{ env('APP_HOME_URL') }}/product/category" class="btn">Back</a>
        <a href="{{ env('APP_HOME_URL') }}/product/model/create" class="btn btn-green">Create</a>
    </div>

    <table id="productTable" class="display initTable">
        <thead>
            <tr>
                <th data-width="5%">No</th>
                <th data-width="25%">Name</th>
                <th data-width="15%">Price</th>
                <th data-width="15%">Type</th>
                <th data-width="25%">Options</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $key => $product)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ number_format($product->price) }}</td>
                    <td>{{ $categories[$product->product_category_ID] }}</td>
                    <td>
                        <a href="{{ env('APP_HOME_URL') }}/product/model/edit?ID={{ $product->ID }}" class="btn btn-green">Edit</a>
                        <a href="{{ env('APP_HOME_URL') }}/product/model/remove?ID={{ $product->ID }}" class="btn btn-red">Remove</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

@include('layouts.footer');
