@include('layouts.header', ['title' => 'Edit Promotor - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Edit Promotor</h1>
    
    @if(Session::has('promotor-updated'))
        <div class="note success">Data has been updated.</div>
    @endif

    @if(Session::has('promotor-block'))
        <div class="note success">Promotor has been blocked.</div>
    @endif

    @if(Session::has('promotor-unblock'))
        <div class="note success">Promotor has been unblocked.</div>
    @endif

    @if(Session::has('promotor-logout'))
        <div class="note success">Promotor has been logged out.</div>
    @endif
    
    @if(Session::has('promotor-reset-password'))
        <div class="note success">Promotor has been reset password.</div>
    @endif

    @if(Session::has('promotor-non-active'))
        <div class="note success">Promotor has been non active.</div>
    @endif

    <div id="promotor">
        
        <div class="form-group">
            @if ($isBlocked)
                <a href="{{ env('APP_HOME_URL') }}/promotor/block?ID={{ $promotor->ID }}" class="btn btn-green">Unblock</a>
            @else
                <a href="{{ env('APP_HOME_URL') }}/promotor/block?ID={{ $promotor->ID }}" class="btn btn-red">Block</a>
            @endif
            
            <a href="{{ env('APP_HOME_URL') }}/promotor/logout?ID={{ $promotor->ID }}" class="btn btn-red">Logout</a>
            <a href="{{ env('APP_HOME_URL') }}/promotor/reset?ID={{ $promotor->ID }}" class="btn btn-green">Reset Password</a>
            <a href="{{ env('APP_HOME_URL') }}/promotor/non-active?ID={{ $promotor->ID }}" class="btn btn-red">Non Active</a>
        </div>
        
        
        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/promotor/edit">
            {!! csrf_field() !!}
            
            <input type="hidden" name="ID" value="{{ $promotor->ID }}" />
            <div id="parent-data" class="hide" data-content="{{ json_encode($parents['data']) }}"></div>

            @if(count($errors) > 0)
    			<div class="note error">{{ $errors->all()[0] }}</div>
    		@endif

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Promotor name" value="{{ $promotor->name }}">
            </div>
            
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" class="form-control" name="phone" id="phone" placeholder="Promotor phone" value="{{ $promotor->phone }}">
            </div>

            <div class="form-group">
                <label for="type">Gender</label>
                {!! Form::select('gender', ['male' => 'Male','female' => 'Female'],  $promotor->gender, ['class' => 'form-control', 'placeholder' => 'Select gender']) !!}
            </div>

            <div class="form-group">
                <label for="type">Type</label>
                {!! Form::select('type', $user_type, $promotor->type, ['class' => 'form-control', 'id' => 'promotor-type-select', 'placeholder' => 'Select user type']) !!}
            </div>
            
            <div class="form-group">
                <label for="dealer_ID">Dealer</label>
                {!! Form::select('dealer_ID', $dealers, $promotor->dealer_ID, ['class' => 'form-control chosen-select', 'placeholder' => 'Select promotor dealer']) !!}
            </div>

            <div id="parent-field" class="form-group">
                <label for="parent_ID">Parent</label>
                {!! Form::select('parent_ID', $parents['list'], $promotor->parent_ID, ['id' => 'parent-select', 'class' => 'form-control', 'placeholder' => 'Select user parent']) !!}
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="Promotor password" value="{{ old('password') }}" autocomplete="off">
                <p class="help-block">Fill password field if you want to change agent password.</p>
            </div>

            <p class="text-center">
                <input type="submit" class="btn btn-green" value="Save" />
                <a href="{{ env('APP_HOME_URL') }}/promotor" class="btn">Back</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');
