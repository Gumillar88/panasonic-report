@include('layouts.header', ['title' => 'Dealers - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Dealers</h1>

    @if(Session::has('dealer-created'))
        <div class="note success">Dealer has been created</div>
    @endif

    @if(Session::has('dealer-deleted'))
        <div class="note error">Dealer has been removed</div>
    @endif

    <div>
        <a href="{{ env('APP_HOME_URL') }}/dealer/create" class="btn btn-green">Create</a>
    </div>

    <table id="dealerTable" class="display initTable">
        <thead>
            <tr>
                <th data-width="5%">No</th>
                <th data-width="20%">Name</th>
                <th data-width="10%">Region</th>
                <th data-width="10%">Branch</th>
                <th data-width="10%">Channel</th>
                <th data-width="10%">Type</th>
                <th data-width="10%">Account</th>
                <th data-width="25%">Options</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dealers as $key => $dealer)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $dealer->name }}</td>
                    <td>{{ $regions[$dealer->region_ID] }}</td>
                    <td>{{ $branch[$dealer->branch_ID] }}</td>
                    <td>{{ $dealerChannels[$dealer->dealer_channel_ID] }}</td>
                    <td>{{ $dealerTypes[$dealer->dealer_type_ID] }}</td>
                    <td>{{ $dealerAccounts[$dealer->dealer_account_ID] }}</td>
                    <td>
                        <a href="{{ env('APP_HOME_URL') }}/dealer/edit?ID={{ $dealer->ID }}" class="btn btn-green">Edit</a>
                        <a href="{{ env('APP_HOME_URL') }}/dealer/remove?ID={{ $dealer->ID }}" class="btn btn-red">Remove</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

@include('layouts.footer');
