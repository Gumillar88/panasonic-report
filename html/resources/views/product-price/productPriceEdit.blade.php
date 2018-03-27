@include('layouts.header', ['title' => 'Edit Product Price- Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Edit Product Price</h1>

    @if(Session::has('product-price-updated'))
        <div class="note success">Data has been updated.</div>
    @endif

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/product/price/edit">
            {!! csrf_field() !!}
            <input type="hidden" name="ID" value="{{ $product_price->ID }}" />

            @if(count($errors) > 0)
    			<div class="note error">{{ $errors->all()[0] }}</div>
    		@endif
			<div class="form-group">
                <label for="dealer_type_ID">Type</label>
                {!! Form::select('dealer_type_ID', $dealer_types, $product_price->dealer_type_ID, ['class' => 'form-control', 'placeholder' => 'Select Dealer Type']) !!}
            </div>

            <div class="form-group">
                <label for="dealer_channel_ID">Channel</label>
                {!! Form::select('dealer_channel_ID', $dealer_channels, $product_price->dealer_channel_ID, ['class' => 'form-control', 'placeholder' => 'Select Dealer Channel']) !!}
            </div>

            <div class="form-group">
                <label for="product">Product</label>
                {!! Form::select('product_ID', $products,  $product_price->product_ID, ['class' => 'form-control chosen-select', 'placeholder' => 'Select Product']) !!}
            </div>

            <div class="form-group">
                <label for="price">Price</label>
                <input type="text" class="form-control" name="price" id="price" placeholder="Price Product" value="{{ $product_price->price }}">
            </div>

            <p class="text-center">
                <input type="submit" class="btn btn-green" value="Save" />
                <a href="{{ env('APP_HOME_URL') }}/product/price" class="btn">Back</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');