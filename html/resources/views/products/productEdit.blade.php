@include('layouts.header', ['title' => 'Edit Product - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Edit Product Model</h1>

    @if(Session::has('product-updated'))
        <div class="note success">Data has been updated.</div>
    @endif

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/product/model/edit">
            {!! csrf_field() !!}
            <input type="hidden" name="ID" value="{{ $product->ID }}" />

            @if(count($errors) > 0)
    			<div class="note error">{{ $errors->all()[0] }}</div>
    		@endif
			
			<div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Product Name" value="{{ $product->name }}">
            </div>
			
            <div class="form-group">
                <label for="product_category_ID">Product Categories</label>
                {!! Form::select('product_category_ID', $product_categories, $product->product_category_ID, ['class' => 'form-control', 'id' => 'product-category-select']) !!}
            </div>
            
            <div class="form-group">
                <label for="price">Price</label>
                <input type="text" class="form-control" name="price" id="price" placeholder="Product Price" value="{{ $product->price }}">
            </div>
			
            <p class="text-center">
                <input type="submit" class="btn btn-green" value="Save" />
                <a href="{{ env('APP_HOME_URL') }}/product/model/?category_ID={{$category_ID}}" class="btn">Back</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');
