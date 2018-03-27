@include('layouts.header', ['title' => 'Create Dealer Type- Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Create Dealer Price</h1>

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/product/price/create">
            {!! csrf_field() !!}

            @if(count($errors) > 0)
    			<div class="note error">{{ $errors->all()[0] }}</div>
    		@endif
            
            <div class="form-group">
                <label for="dealer_type_ID">Type</label>
                {!! Form::select('dealer_type_ID', $dealer_types, old('dealer_type_ID'), ['class' => 'form-control', 'placeholder' => 'Select Dealer Type']) !!}
            </div>

            <div class="form-group">
                <label for="dealer_channel_ID">Channel</label>
                {!! Form::select('dealer_channel_ID', $dealer_channels, old('dealer_channel_ID'), ['class' => 'form-control', 'placeholder' => 'Select Dealer Channel']) !!}
            </div>

            <div class="form-group">
                <label for="product">Product</label>
                {!! Form::select('product_ID', $products, old('product_ID'), ['class' => 'form-control chosen-select', 'placeholder' => 'Select Product']) !!}
            </div>

            <div class="form-group">
                <label for="price">Price</label>
                <input type="text" class="form-control" name="price" id="price" placeholder="Price Product" value="{{ old('price') }}">
            </div>
			
            <p class="text-center">
                <input type="submit" class="btn btn-green" value="Create" />
                <a href="{{ env('APP_HOME_URL') }}/product/price" class="btn">Back</a>
            </p>

        </form>
        
    </div>

</div>

@include('layouts.footer');