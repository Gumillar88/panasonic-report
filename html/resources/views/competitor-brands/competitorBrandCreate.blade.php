@include('layouts.header', ['title' => 'Create Competitor Brand - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Create Competitor Brand</h1>

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/competitor-brand/create">
            {!! csrf_field() !!}

            @if(count($errors) > 0)
    			<div class="note error">{{ $errors->all()[0] }}</div>
    		@endif

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Brand name" value="{{ old('name') }}">
            </div>

            <p class="text-center">
                <input type="submit" class="btn btn-green" value="Create" />
                <a href="{{ env('APP_HOME_URL') }}/competitor-brand" class="btn">Back</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');
