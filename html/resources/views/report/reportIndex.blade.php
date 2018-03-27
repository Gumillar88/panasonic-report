@include('layouts.header', ['title' => 'Promotor Report - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Promotor Reports</h1>

    <table id="userTable" class="display initTable">
        <thead>
            <tr>
                <th data-width="5%">No.</th>
                <th data-width="30%">Promotor</th>
                <th data-width="20%">Dealer</th>
                <th data-width="15%">Branch</th>
                <th data-width="15%">Options</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataTarget as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $item['promotor'] }}</td>
                    <td>{{ $item['dealer'] }}</td>
                    <td>{{ $item['branch'] }}</td>                    
                    <td>
                        <a href="{{ env('APP_HOME_URL') }}/report/view?ID={{ $item['ID'] }}&date={{ $date }}" class="btn btn-green">View</a>
                    </td>            
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

@include('layouts.footer');
