@include('layouts.header', ['title' => 'Dealer Types - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Dealer Types</h1>

    @if(Session::has('dealer-type-created'))
        <div class="note success">Dealer type has been created</div>
    @endif

    @if(Session::has('dealer-type-deleted'))
        <div class="note error">Dealer type has been removed</div>
    @endif

    <div>
        <a href="{{ env('APP_HOME_URL') }}/dealer-type/create" class="btn btn-green">Create</a>
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
            @foreach($dealer_types as $key => $dealer_type)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $dealer_type->name }}</td>
                    <td>
                        <a href="{{ env('APP_HOME_URL') }}/dealer-type/edit?ID={{ $dealer_type->ID }}" class="btn btn-green">Edit</a>
                        <a href="{{ env('APP_HOME_URL') }}/dealer-type/remove?ID={{ $dealer_type->ID }}" class="btn btn-red">Remove</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

@include('layouts.footer');
