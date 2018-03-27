@include('layouts.header', ['title' => 'Promotor Report - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Promotor Report View</h1>

    @if(Session::has('report-removed'))
        <div class="note success">Data has been updated.</div>
    @endif

    <div>
        {!! Form::select('reportDate', $listMonth['months'], $date, ['class' => 'reportDate form-control', 'data-id' => $promotorID]) !!}
    </div>
    
    <table id="userTable" class="display initTable">
        <thead>
            <tr>
                <th data-width="5%">No.</th>
                <th data-width="30%">Product</th>
                <th data-width="20%">Date</th>
                <th data-width="15%">Quantity</th>
                <th data-width="15%">Options</th>
            </tr>
        </thead>

        <tbody>
            @foreach($dataReport as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    @if($item->custom_name !== "")
                        <td>{{ $item->custom_name }}</td>
                    @else
                        <td>{{ $item->name }}</td>
                    @endif
                    <td>{{ $item->date }}</td>
                    <td>{{ $item->quantity }}</td>                    
                    <td>
                        <a href="{{ env('APP_HOME_URL') }}/report/edit?ID={{ $item->ID }}&date={{ $date }}" class="btn btn-green">Edit</a>
                        <a href="{{ env('APP_HOME_URL') }}/report/remove?ID={{ $item->ID }}&date={{ $date }}" class="btn btn-red">Remove</a>
                    </td>            
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

@include('layouts.footer');
