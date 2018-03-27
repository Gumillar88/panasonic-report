@include('layouts.header', ['title' => 'Regions - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Region</h1>

    @if(Session::has('region-created'))
        <div class="note success">Region has been created</div>
    @endif

    @if(Session::has('region-deleted'))
        <div class="note error">Region has been removed</div>
    @endif

    <div>
        <a href="{{ env('APP_HOME_URL') }}/region/create" class="btn btn-green">Create</a>
    </div>

    <table id="userTable" class="display initTable">
        <thead>
            <tr>
                <th data-width="5%">No</th>
                <th data-width="30%">Name</th>
                <th data-width="30%">Arco</th>
                <th data-width="25%">Options</th>
            </tr>
        </thead>
        <tbody>
            @foreach($regions as $key => $region)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $region->name }}</td>
                    <td>{{ $dataArco[$region->promotor_ID] }}</td>
                    <td>
                        <a href="{{ env('APP_HOME_URL') }}/region/edit?ID={{ $region->ID }}" class="btn btn-green">Edit</a>
                        <a href="{{ env('APP_HOME_URL') }}/region/remove?ID={{ $region->ID }}" class="btn btn-red">Remove</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

@include('layouts.footer');
