@include('layouts.header', ['title' => 'Create Product Incentive - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Create Product Incentive</h1>

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/product/incentive/create">
            {!! csrf_field() !!}

            @if(count($errors) > 0)
    			<div class="note error">{{ $errors->all()[0] }}</div>
    		@endif
            
            <div class="form-group">
                <label for="dealer_channel_ID">Channel</label>
                {!! Form::select('dealer_channel_ID', $dealerChannels, old('dealer_channel_ID'), ['class' => 'form-control', 'placeholder' => 'Select Dealer Channel']) !!}
            </div>

            <div class="form-group">
                <label for="product_model_ID">Product</label>
                {!! Form::select('product_model_ID', $productModels, old('product_model_ID'), ['class' => 'form-control chosen-select', 'placeholder' => 'Select Product']) !!}
            </div>

            <div class="form-group">
                <label for="value">Incentive Value</label>
                <input type="text" class="form-control" name="value" id="value" placeholder="Incentive value of the product" value="{{ old('value') }}">
            </div>
			
            <p class="text-center">
                <input type="submit" class="btn btn-green" value="Create" />
                <a href="{{ env('APP_HOME_URL') }}/product/incentive" class="btn">Back</a>
            </p>

        </form>
        
    </div>

</div>

@include('layouts.footer');