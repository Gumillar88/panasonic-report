@include('layouts.header', ['title' => 'Create Agent - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Create Promotor</h1>

    <div id="promotor">

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/promotor/create">
            {!! csrf_field() !!}

            <div id="parent-data" class="hide" data-content="{{ json_encode($parents['data']) }}"></div>
            
            @if(count($errors) > 0)
    			<div class="note error">{{ $errors->all()[0] }}</div>
    		@endif

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Promotor name" value="{{ old('name') }}">
            </div>
            
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" class="form-control" name="phone" id="phone" placeholder="Promotor phone" value="{{ old('phone') }}">
            </div>

            <div class="form-group">
                <label for="type">Gender</label>
                {!! Form::select('gender', ['male' => 'Male','female' => 'Female'], old('gender'), ['class' => 'form-control', 'placeholder' => 'Select gender']) !!}
            </div>

            <div class="form-group">
                <label for="type">Type</label>
                {!! Form::select('type', $user_type, old('type'), ['class' => 'form-control', 'id' => 'promotor-type-select', 'placeholder' => 'Select user type']) !!}
            </div>
            
            <div class="form-group">
                <label for="dealer_ID">Dealer</label>
                {!! Form::select('dealer_ID', $dealers, old('dealer_ID'), ['class' => 'form-control chosen-select', 'placeholder' => 'Select promotor dealer']) !!}
            </div>

            <div id="parent-field" class="form-group hide">
                <label for="parent_ID">Parent</label>
                {!! Form::select('parent_ID', $parents['list'], old('parent_ID'), ['class' => 'form-control', 'id' => 'parent-select', 'placeholder' => 'Select user parent']) !!}
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="Promotor password" value="{{ old('password') }}" autocomplete="off">
            </div>

            <p class="text-center">
                <input type="submit" class="btn btn-green" value="Create" />
                <a href="{{ env('APP_HOME_URL') }}/promotor" class="btn">Back</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');
