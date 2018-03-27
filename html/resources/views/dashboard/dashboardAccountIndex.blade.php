@include('layouts.header', ['title' => 'Dashboard Accounts - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Dashboard Accounts</h1>

    @if(Session::has('account-created'))
        <div class="note success">Account has been created.</div>
    @endif

    @if(Session::has('account-removed'))
        <div class="note error">Account has been removed.</div>
    @endif

    <div>
        <a href="{{ env('APP_HOME_URL') }}/dashboard-account/create" class="btn btn-green">Create</a>
    </div>

    <table class="display initTable">
        <thead>
            <tr>
                <th data-width="5%">No.</th>
                <th data-width="30%">Name</th>
                <th data-width="20%">Email</th>
                <th data-width="20%">Last Access</th>
                <th data-width="25%">Options</th>
            </tr>
        </thead>
        <tbody>
            @foreach($accounts as $key => $account)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $account->name }}</td>
                    <td>{{ $account->email }}</td>
                    @if ($account->last_access > 0)
                        <td>{{ date('d-m-Y H:i:s', $account->updated) }}</td>
                    @else
                        <td>(none)</td>
                    @endif
                    <td>
                        <a href="{{ env('APP_HOME_URL') }}/dashboard-account/edit?ID={{ $account->ID }}" class="btn btn-green">Edit</a>
                        <a href="{{ env('APP_HOME_URL') }}/dashboard-account/remove?ID={{ $account->ID }}" class="btn btn-red">Remove</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

@include('layouts.footer');
