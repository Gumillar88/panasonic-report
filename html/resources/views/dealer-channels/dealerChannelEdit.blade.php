@include('layouts.header', ['title' => 'Edit Dealer Channel- Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Edit Dealer Channel</h1>

    @if(Session::has('dealer-channel-updated'))
        <div class="note success">Data has been updated.</div>
    @endif

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/dealer-channel/edit">
            {!! csrf_field() !!}
            <input type="hidden" name="ID" value="{{ $dealer_channel->ID }}" />

            @if(count($errors) > 0)
    			<div class="note error">{{ $errors->all()[0] }}</div>
    		@endif
            
			<div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Dealer type name" value="{{ $dealer_channel->name }}">
            </div>
			
            <p class="text-center">
                <input type="submit" class="btn btn-green" value="Save" />
                <a href="{{ env('APP_HOME_URL') }}/dealer-channel" class="btn">Back</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');