@include('layouts.header', ['title' => 'Users - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Users</h1>

    @if(Session::has('user-created'))
        <div class="note success">User has been created</div>
    @endif

    @if(Session::has('user-deleted'))
        <div class="note error">User has been removed</div>
    @endif

    <div>
        <a href="{{ env('APP_HOME_URL') }}/user/create" class="btn btn-green">Create</a>
    </div>

    <table id="userTable" class="display initTable">
        <thead>
            <tr>
                <th data-width="5%">User ID</th>
                <th data-width="20%">Fullname</th>
                <th data-width="20%">Username</th>
                <th data-width="10%">Type</th>
                <th data-width="20%">Created</th>
                <th data-width="25%">Options</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->ID }}</td>
                    <td>{{ $user->fullname }}</td>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->type }}</td>
                    <td>{{ date('j F Y', $user->created) }}</td>
                    <td>
                        <a href="{{ env('APP_HOME_URL') }}/user/edit?ID={{ $user->ID }}" class="btn btn-green">Edit</a>
                        <a href="{{ env('APP_HOME_URL') }}/user/remove?ID={{ $user->ID }}" class="btn btn-red">Remove</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

@include('layouts.footer');