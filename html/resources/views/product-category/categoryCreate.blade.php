@include('layouts.header', ['title' => 'Create Product Category - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Create Product Category</h1>

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/product/category/create">
            {!! csrf_field() !!}

            @if(count($errors) > 0)
    			<div class="note error">{{ $errors->all()[0] }}</div>
    		@endif
			
			<div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Division name" value="{{ old('name') }}">
            </div>

            <p class="text-center">
                <input type="submit" class="btn btn-green" value="Create" />
                <a href="{{ env('APP_HOME_URL') }}/product/category" class="btn">Back</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');
