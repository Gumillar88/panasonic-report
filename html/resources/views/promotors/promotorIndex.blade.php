@include('layouts.header', ['title' => 'Promotors - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Promotors</h1>

    @if(Session::has('promotor-created'))
        <div class="note success">Promotor has been created.</div>
    @endif

    @if(Session::has('promotor-removed'))
        <div class="note error">Promotor has been removed.</div>
    @endif

    <div>
        <a href="{{ env('APP_HOME_URL') }}/promotor/create" class="btn btn-green">Create</a>
    </div>

    <table id="userTable" class="display initTable">
        <thead>
            <tr>
                <th data-width="3%">No.</th>
                <th data-width="20%">Name</th>
                <th data-width="5%">Branch</th>
                <th data-width="25%">Dealer</th>
                <th data-width="5%">Status</th>
                <th data-width="25%">Options</th>
            </tr>
        </thead>
        <tbody>
            @foreach($promotors as $key => $promotor)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $promotor->name }}</td>
                    <td>{{ $dealers[$promotor->dealer_ID]['branch'] }}</td>
                    <td>{{ $dealers[$promotor->dealer_ID]['name'] }}</td>
                    <td>{{ $user_type[$promotor->type] }}</td>
                    <td>
                        <a href="{{ env('APP_HOME_URL') }}/promotor/edit?ID={{ $promotor->ID }}" class="btn btn-green">Edit</a>
                        <a href="{{ env('APP_HOME_URL') }}/promotor/remove?ID={{ $promotor->ID }}" class="btn btn-red">Remove</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

@include('layouts.footer');
