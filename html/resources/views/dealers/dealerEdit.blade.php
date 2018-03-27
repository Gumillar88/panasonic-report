@include('layouts.header', ['title' => 'Edit Dealer - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Edit Dealer</h1>

    @if(Session::has('dealer-updated'))
        <div class="note success">Data has been updated.</div>
    @endif

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/dealer/edit">
            {!! csrf_field() !!}
            <input type="hidden" name="ID" value="{{ $dealer->ID }}" />

            @if(count($errors) > 0)
    			<div class="note error">{{ $errors->all()[0] }}</div>
    		@endif
            
			<div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Dealer name" value="{{ $dealer->name }}">
            </div>

            <div class="form-group">
                <label for="company">Company</label>
                <input type="text" class="form-control" name="company" id="company" placeholder="Dealer company" value="{{ $dealer->company }}">
            </div>


            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" class="form-control" name="address" id="address" placeholder="Dealer address" value="{{ $dealer->address }}">
            </div>
			
			<div class="form-group">
                <label for="code">Code</label>
                <input type="text" class="form-control" name="code" id="code" placeholder="Dealer code" value="{{ $dealer->code }}">
            </div>
            
            <div class="form-group">
                <label for="branch_ID">Branch</label>
                {!! Form::select('branch_ID', $branches, $dealer->branch_ID, ['id' => 'branch_ID', 'class' => 'form-control', 'placeholder' => 'Select branch...']) !!}
            </div>

            <div class="form-group">
                <label for="dealer_account_ID">Dealer Account</label>
                {!! Form::select('dealer_account_ID', $dealerAccounts,  $dealer->dealer_account_ID, ['id' => 'dealer_account_ID', 'class' => 'form-control', 'placeholder' => 'Select dealer account...']) !!}
            </div>
			
            <div class="form-group">
                <label for="dealer_type_ID">Type</label>
                {!! Form::select('dealer_type_ID', $dealerTypes, $dealer->dealer_type_ID, ['id' => 'dealer_type_ID', 'class' => 'form-control', 'placeholder' => 'Select dealer type...']) !!}
            </div>
            
            <div class="form-group">
                <label for="dealer_channel_ID">Channel</label>
                {!! Form::select('dealer_channel_ID', $dealerChannels, $dealer->dealer_channel_ID, ['id' => 'dealer_channel_ID', 'class' => 'form-control', 'placeholder' => 'Select dealer channel...']) !!}
            </div>

			
            <p class="text-center">
                <input type="submit" class="btn btn-green" value="Save" />
                <a href="{{ env('APP_HOME_URL') }}/dealer" class="btn">Back</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');