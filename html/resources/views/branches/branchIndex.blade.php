@include('layouts.header', ['title' => 'Branch - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Branch</h1>

    @if(Session::has('branch-created'))
        <div class="note success">Branch has been created</div>
    @endif

    @if(Session::has('branch-deleted'))
        <div class="note error">Branch has been removed</div>
    @endif

    <div>
        <a href="{{ env('APP_HOME_URL') }}/branch/create" class="btn btn-green">Create</a>
    </div>

    <table id="userTable" class="display initTable">
        <thead>
            <tr>
                <th data-width="5%">No</th>
                <th data-width="30%">Name</th>
                <th data-width="20%">Region</th>
                <th data-width="20%">Team leader</th>
                <th data-width="25%">Options</th>
            </tr>
        </thead>
        <tbody>
            @foreach($branches as $key => $branch)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $branch->name }}</td>
                    <td>{{ $regions[$branch->region_ID] }}</td>
                    <td>{{ $dataTl[$branch->promotor_ID] }}</td>
                    <td>
                        <a href="{{ env('APP_HOME_URL') }}/branch/edit?ID={{ $branch->ID }}" class="btn btn-green">Edit</a>
                        <a href="{{ env('APP_HOME_URL') }}/branch/remove?ID={{ $branch->ID }}" class="btn btn-red">Remove</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

@include('layouts.footer');
