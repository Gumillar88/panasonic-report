@include('layouts.header', ['title' => 'Edit Competitor Brand - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Edit Competitor Brand</h1>
    
    @if(Session::has('brand-updated'))
        <div class="note success">Data has been updated.</div>
    @endif

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/competitor-brand/edit">
            {!! csrf_field() !!}
            <input type="hidden" name="ID" value="{{ $brand->ID }}" />

            @if(count($errors) > 0)
    			<div class="note error">{{ $errors->all()[0] }}</div>
    		@endif

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Competitor brand name" value="{{ $brand->name }}">
            </div>

            <p class="text-center">
                <input type="submit" class="btn btn-green" value="Save" />
                <a href="{{ env('APP_HOME_URL') }}/competitor-brand" class="btn">Back</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');
