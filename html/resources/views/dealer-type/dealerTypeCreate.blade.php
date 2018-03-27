@include('layouts.header', ['title' => 'Create Dealer Type- Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Create Dealer Type</h1>

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/dealer-type/create">
            {!! csrf_field() !!}

            @if(count($errors) > 0)
    			<div class="note error">{{ $errors->all()[0] }}</div>
    		@endif
            
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Dealer type name" value="{{ old('name') }}">
            </div>
			
            <p class="text-center">
                <input type="submit" class="btn btn-green" value="Create" />
                <a href="{{ env('APP_HOME_URL') }}/dealer-type" class="btn">Back</a>
            </p>

        </form>
        
    </div>

</div>

@include('layouts.footer');