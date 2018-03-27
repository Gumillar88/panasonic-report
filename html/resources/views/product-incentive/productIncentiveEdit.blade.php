@include('layouts.header', ['title' => 'Edit Product Incentive - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Edit Product Incentive</h1>

    @if(Session::has('product-incentive-updated'))
        <div class="note success">Data has been updated.</div>
    @endif
    
    @if(Session::has('product-incentive-exist'))
        <div class="note error">
            Data that you try to save already exist. Please edit this data instead of creating the new one.
        </div>
    @endif

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/product/incentive/edit">
            {!! csrf_field() !!}
            <input type="hidden" name="ID" value="{{ $incentive->ID }}" />

            @if(count($errors) > 0)
    			<div class="note error">{{ $errors->all()[0] }}</div>
    		@endif
            
            <div class="form-group">
                <label for="dealer_channel_ID">Channel</label>
                {!! Form::select('dealer_channel_ID', $dealerChannels, $incentive->dealer_channel_ID, ['class' => 'form-control', 'placeholder' => 'Select Dealer Channel']) !!}
            </div>

            <div class="form-group">
                <label for="product_model_ID">Product</label>
                {!! Form::select('product_model_ID', $productModels,  $incentive->product_model_ID, ['class' => 'form-control chosen-select', 'placeholder' => 'Select Product']) !!}
            </div>

            <div class="form-group">
                <label for="value">Price</label>
                <input type="text" class="form-control" name="value" id="value" placeholder="Incentive value of the Product" value="{{ $incentive->value }}">
            </div>

            <p class="text-center">
                <input type="submit" class="btn btn-green" value="Save" />
                <a href="{{ env('APP_HOME_URL') }}/product/incentive" class="btn">Back</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');