@include('layouts.header', ['title' => 'Create Product - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Create Product Model</h1>

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/product/model/create">
            {!! csrf_field() !!}


            @if(count($errors) > 0)
    			<div class="note error">{{ $errors->all()[0] }}</div>
    		@endif
            
			<div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Product Name" value="{{ old('name') }}">
            </div>
			
			<div class="form-group">
                <label for="dealer_ID">Product Categories</label>
                {!! Form::select('product_category_ID', $product_categories, null, ['class' => 'form-control', 'id' => 'product-category-select']) !!}
            </div>
            
            <div class="form-group">
                <label for="price">Price</label>
                <input type="text" class="form-control" name="price" id="price" placeholder="Product Price" value="{{ old('price') }}">
            </div>
            
            <p class="text-center">
                <input type="submit" class="btn btn-green" value="Create" />
                <a href="{{ env('APP_HOME_URL') }}/product/model/?category_ID={{$category_ID}}" class="btn">Back</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');
