@include('layouts.header', ['title' => 'Dealer Accounts - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Dealer Accounts</h1>

    @if(Session::has('dealer-account-created'))
        <div class="note success">Dealer account has been created</div>
    @endif

    @if(Session::has('dealer-account-deleted'))
        <div class="note error">Dealer account has been removed</div>
    @endif

    <div>
        <a href="{{ env('APP_HOME_URL') }}/dealer-account/create" class="btn btn-green">Create</a>
    </div>

    <table id="userTable" class="display initTable">
        <thead>
            <tr>
                <th data-width="5%">No.</th>
                <th data-width="30%">Name</th>
                <th data-width="20%">Branch</th>
                <th data-width="20%">Team Leader</th>
                <th data-width="25%">Options</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dealer_accounts as $key => $dealer_account)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $dealer_account->name }}</td>
                    <td>{{ $branch[$dealer_account->branch_ID] }}</td>
                    <td>{{ $dataTl[$dealer_account->promotor_ID] }}</td>
                    <td>
                        <a href="{{ env('APP_HOME_URL') }}/dealer-account/edit?ID={{ $dealer_account->ID }}" class="btn btn-green">Edit</a>
                        <a href="{{ env('APP_HOME_URL') }}/dealer-account/remove?ID={{ $dealer_account->ID }}" class="btn btn-red">Remove</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

@include('layouts.footer');
