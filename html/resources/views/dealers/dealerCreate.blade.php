@include('layouts.header', ['title' => 'Create Dealer - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Create Dealer</h1>

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/dealer/create">
            {!! csrf_field() !!}

            @if(count($errors) > 0)
    			<div class="note error">{{ $errors->all()[0] }}</div>
    		@endif
            
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Dealer name" value="{{ old('name') }}">
            </div>

            <div class="form-group">
                <label for="company">Company</label>
                <input type="text" class="form-control" name="company" id="company" placeholder="Dealer company" value="{{ old('company') }}">
            </div>


            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" class="form-control" name="address" id="address" placeholder="Dealer address" value="{{ old('address') }}">
            </div>
			
			<div class="form-group">
                <label for="code">Code</label>
                <input type="text" class="form-control" name="code" id="code" placeholder="Dealer code" value="{{ old('code') }}">
            </div>

            <div class="form-group">
                <label for="branch_ID">Branch</label>
                {!! Form::select('branch_ID', $branches, old('branch_ID'), ['id' => 'branch_ID', 'class' => 'form-control', 'placeholder' => 'Select branch...']) !!}
            </div>

            <div class="form-group">
                <label for="dealer_account_ID">Account</label>
                {!! Form::select('dealer_account_ID', $dealerAccounts, old('dealer_account_ID'), ['id' => 'dealer_account_ID', 'class' => 'form-control', 'placeholder' => 'Select dealer account...']) !!}
            </div>

            <div class="form-group">
                <label for="dealer_type_ID">Type</label>
                {!! Form::select('dealer_type_ID', $dealerTypes, old('dealer_type_ID'), ['id' => 'dealer_type_ID', 'class' => 'form-control', 'placeholder' => 'Select dealer type...']) !!}
            </div>
            
            <div class="form-group">
                <label for="dealer_channel_ID">Channel</label>
                {!! Form::select('dealer_channel_ID', $dealerChannels, old('dealer_channel_ID'), ['id' => 'dealer_channel_ID', 'class' => 'form-control', 'placeholder' => 'Select dealer channel...']) !!}
            </div>
            
            
            <p class="text-center">
                <input type="submit" class="btn btn-green" value="Create" />
                <a href="{{ env('APP_HOME_URL') }}/dealer" class="btn">Back</a>
            </p>

        </form>
        
    </div>

</div>

@include('layouts.footer');