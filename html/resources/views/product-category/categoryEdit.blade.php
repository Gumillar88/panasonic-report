@include('layouts.header', ['title' => 'Rename Product Category - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Rename Product Category</h1>

    @if(Session::has('category-updated'))
        <div class="note success">Data has been updated.</div>
    @endif

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/product/category/edit">
            {!! csrf_field() !!}
            <input type="hidden" name="ID" value="{{ $category->ID }}" />

            @if(count($errors) > 0)
    			<div class="note error">{{ $errors->all()[0] }}</div>
    		@endif

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Division name" value="{{ $category->name }}">
            </div>
			
            <p class="text-center">
                <input type="submit" class="btn btn-green" value="Save" />
                <a href="{{ env('APP_HOME_URL') }}/product/category" class="btn">Back</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');
