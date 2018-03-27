@include('layouts.header', ['title' => 'Edit Dashboard Account - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Edit Dashboard Article</h1>
    
    @if(Session::has('account-updated'))
        <div class="note success">Data has been updated.</div>
    @endif
    
    @if(Session::has('token-removed'))
        <div class="note success">Token has been removed.</div>
    @endif

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/dashboard-account/edit">
            {!! csrf_field() !!}
            <input type="hidden" name="ID" value="{{ $account->ID }}" />

            @if(count($errors) > 0)
    			<div class="note error">{{ $errors->all()[0] }}</div>
    		@endif

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Account name" value="{{ $account->name }}">
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="text" class="form-control" name="email" id="email" placeholder="Account email" value="{{ $account->email }}">
            </div>

            <p class="text-center">
                <input type="submit" class="btn btn-green" value="Save" />
                <a href="{{ env('APP_HOME_URL') }}/dashboard-account" class="btn">Back</a>
            </p>

        </form>
    </div>
    
    <table class="display initTable">
        <thead>
            <tr>
                <th data-width="5%">No.</th>
                <th data-width="50%">Token</th>
                <th data-width="20%">Created</th>
                <th data-width="25%">Options</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tokens as $key => $token)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $token->token }}</td>
                    <td>{{ date('d-m-Y H:i:s', $token->created) }}</td>
                    <td>
                        <a href="{{ env('APP_HOME_URL') }}/dashboard-account/remove-token?tokenID={{ $token->ID }}" class="btn btn-red">Remove</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

@include('layouts.footer');
