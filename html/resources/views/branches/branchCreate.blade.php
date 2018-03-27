@include('layouts.header', ['title' => 'Create Branch - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Create Branch</h1>

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/branch/create">
            {!! csrf_field() !!}

            @if(count($errors) > 0)
    			<div class="note error">{{ $errors->all()[0] }}</div>
    		@endif

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Branch name" value="{{ old('name') }}">
            </div>

            <div class="form-group">
                <label for="region_ID">Region</label>
                {!! Form::select('region_ID', $regions, old('region_ID'), ['id' => 'region_ID', 'class' => 'form-control', 'placeholder' => 'Select Region...']) !!}
            </div>

            <div class="form-group">
                <label for="promotor_ID">Promotor</label>
                {!! Form::select('promotor_ID', $dataTl, old('promotor_ID'), ['id' => 'promotor_ID', 'class' => 'form-control', 'placeholder' => 'Select Team Leader...']) !!}
            </div>

            <p class="text-center">
                <input type="submit" class="btn btn-green" value="Create" />
                <a href="{{ env('APP_HOME_URL') }}/branch" class="btn">Back</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');
