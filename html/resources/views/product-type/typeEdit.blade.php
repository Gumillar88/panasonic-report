@include('layouts.header', ['title' => 'Edit Product Type - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Edit Product Type</h1>

    @if(Session::has('type-updated'))
        <div class="note success">Data has been updated.</div>
    @endif
    
    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/product/type/edit">
            {!! csrf_field() !!}
            <input type="hidden" name="ID" value="{{ $type->ID }}" />

            @if(count($errors) > 0)
    			<div class="note error">{{ $errors->all()[0] }}</div>
    		@endif

            <div class="form-group">
                <label for="fullname">Type Name</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Product Type name" value="{{ $type->name }}">
            </div>
			
			<div class="form-group">
                <label for="division_ID">Product Category</label>
                {!! Form::select('product_category', $categories, $type->category_ID, ['class' => 'form-control', 'placeholder' => 'Select category']) !!}
            </div>
			
            <p class="text-center">
                <input type="submit" class="btn btn-green" value="Save" />
                <a href="{{ env('APP_HOME_URL') }}/product/type/?category_ID={{$category_ID}}" class="btn">Back</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');
