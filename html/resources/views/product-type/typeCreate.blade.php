@include('layouts.header', ['title' => 'Create Product Type - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Create Product Type</h1>

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/product/type/create">
            {!! csrf_field() !!}

            @if(count($errors) > 0)
    			<div class="note error">{{ $errors->all()[0] }}</div>
    		@endif
			
			<div class="form-group">
                <label for="name">Type Name</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Product type name" value="{{ old('name') }}">
            </div>
			
			<div class="form-group">
                <label for="product_category">Product Categories</label>
                {!! Form::select('product_category', $categories, $category_ID, ['class' => 'form-control', 'placeholder' => 'Select category']) !!}
            </div>

            <p class="text-center">
                <input type="submit" class="btn btn-green" value="Create" />
                <a href="{{ env('APP_HOME_URL') }}/product/type/?category_ID={{$category_ID}}" class="btn">Back</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');
