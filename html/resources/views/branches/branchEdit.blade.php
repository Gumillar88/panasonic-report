@include('layouts.header', ['title' => 'Edit Branch - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Edit Branch</h1>
    
    @if(Session::has('branch-updated'))
        <div class="note success">Data has been updated.</div>
    @endif

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/branch/edit">
            {!! csrf_field() !!}
            <input type="hidden" name="ID" value="{{ $branch->ID }}" />

            @if(count($errors) > 0)
    			<div class="note error">{{ $errors->all()[0] }}</div>
    		@endif

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Branch name" value="{{ $branch->name }}">
            </div>

            <div class="form-group">
                <label for="region_ID">Region</label>
                {!! Form::select('region_ID', $regions, $branch->region_ID, ['id' => 'region_ID', 'class' => 'form-control', 'placeholder' => 'Select Region...']) !!}
            </div>

            <div class="form-group">
                <label for="promotor_ID">Promotor</label>
                {!! Form::select('promotor_ID', $dataTl, $branch->promotor_ID, ['id' => 'promotor_ID', 'class' => 'form-control', 'placeholder' => 'Select Team Leader...']) !!}
            </div>


            <p class="text-center">
                <input type="submit" class="btn btn-green" value="Save" />
                <a href="{{ env('APP_HOME_URL') }}/branch" class="btn">Back</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');
