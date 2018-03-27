@include('layouts.header', ['title' => 'Create Dealer Account- Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Create Dealer Account</h1>

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/dealer-account/create">
            {!! csrf_field() !!}

            @if(count($errors) > 0)
    			<div class="note error">{{ $errors->all()[0] }}</div>
    		@endif
            
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Dealer Account name" value="{{ old('name') }}">
            </div>

            <div class="form-group">
                <label for="branch_ID">Branch</label>
                {!! Form::select('branch_ID', $dataBranch, old('branch_ID'), ['class' => 'form-control', 'placeholder' => 'Select Branch']) !!}
            </div>

            <div class="form-group">
                <label for="promotor_ID">Promotor</label>
                {!! Form::select('promotor_ID', $dataTl, old('promotor_ID'), ['id' => 'promotor_ID', 'class' => 'form-control', 'placeholder' => 'Select Team Leader...']) !!}
            </div>
			
            <p class="text-center">
                <input type="submit" class="btn btn-green" value="Create" />
                <a href="{{ env('APP_HOME_URL') }}/dealer-account" class="btn">Back</a>
            </p>

        </form>
        
    </div>

</div>

@include('layouts.footer');