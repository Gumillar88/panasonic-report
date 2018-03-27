@include('layouts.header', ['title' => 'Remove Product Category - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Remove Product Category</h1>

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/product/category/remove">
            {!! csrf_field() !!}
            <input type="hidden" name="ID" value="{{ $ID }}" />

            <p>
                Warning: This action cannot be undone. <br />
                Are you sure want to remove the product category?
            </p>

            <p>
                <input type="submit" class="btn btn-red" value="Remove" />
                <a href="{{ env('APP_HOME_URL') }}/product/category" class="btn">Cancel</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');
