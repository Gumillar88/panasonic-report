@include('layouts.header', ['title' => 'Dealer Channels - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Dealer Channels</h1>

    @if(Session::has('dealer-channel-created'))
        <div class="note success">Dealer channel has been created</div>
    @endif

    @if(Session::has('dealer-channel-deleted'))
        <div class="note error">Dealer channel has been removed</div>
    @endif

    <div>
        <a href="{{ env('APP_HOME_URL') }}/dealer-channel/create" class="btn btn-green">Create</a>
    </div>

    <table id="userTable" class="display initTable">
        <thead>
            <tr>
                <th data-width="5%">No.</th>
                <th data-width="70%">Name</th>
                <th data-width="25%">Options</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dealer_channels as $key => $dealer_channel)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $dealer_channel->name }}</td>
                    <td>
                        <a href="{{ env('APP_HOME_URL') }}/dealer-channel/edit?ID={{ $dealer_channel->ID }}" class="btn btn-green">Edit</a>
                        <a href="{{ env('APP_HOME_URL') }}/dealer-channel/remove?ID={{ $dealer_channel->ID }}" class="btn btn-red">Remove</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

@include('layouts.footer');
