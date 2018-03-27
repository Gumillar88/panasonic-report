@include('layouts.header', ['title' => 'Edit Dealer Account- Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Edit Dealer Account</h1>

    @if(Session::has('dealer-account-updated'))
        <div class="note success">Data has been updated.</div>
    @endif

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/dealer-account/edit">
            {!! csrf_field() !!}
            <input type="hidden" name="ID" value="{{ $dealer_account->ID }}" />

            @if(count($errors) > 0)
    			<div class="note error">{{ $errors->all()[0] }}</div>
    		@endif
            
			<div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Dealer account name" value="{{ $dealer_account->name }}">
            </div>

            <div class="form-group">
                <label for="branch_ID">Branch</label>
                {!! Form::select('branch_ID', $dataBranch, $dealer_account->branch_ID, ['class' => 'form-control', 'placeholder' => 'Select Branch']) !!}
            </div>

            <div class="form-group">
                <label for="promotor_ID">Promotor</label>
                {!! Form::select('promotor_ID', $dataTl, $dealer_account->promotor_ID, ['id' => 'promotor_ID', 'class' => 'form-control', 'placeholder' => 'Select Team Leader...']) !!}
            </div>
			
            <p class="text-center">
                <input type="submit" class="btn btn-green" value="Save" />
                <a href="{{ env('APP_HOME_URL') }}/dealer-account" class="btn">Back</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');