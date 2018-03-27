@include('layouts.header', ['title' => 'Edit Region - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Edit Region</h1>
    
    @if(Session::has('region-updated'))
        <div class="note success">Data has been updated.</div>
    @endif

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/region/edit">
            {!! csrf_field() !!}
            <input type="hidden" name="ID" value="{{ $region->ID }}" />

            @if(count($errors) > 0)
    			<div class="note error">{{ $errors->all()[0] }}</div>
    		@endif

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Region name" value="{{ $region->name }}">
            </div>

            <div class="form-group">
                <label for="promotor_id">Promotor</label>
                {!! Form::select('promotor_ID', $dataArco, $region->promotor_ID, ['class' => 'form-control', 'placeholder' => 'Select Promotor']) !!}
            </div>

            <p class="text-center">
                <input type="submit" class="btn btn-green" value="Save" />
                <a href="{{ env('APP_HOME_URL') }}/region" class="btn">Back</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');
