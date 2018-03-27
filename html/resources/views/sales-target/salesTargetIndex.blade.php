@include('layouts.header', ['title' => 'Sales Targets - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Sales Targets (by {{ ucfirst($type) }})</h1>

    <div>
        <a href="{{ env('APP_HOME_URL') }}/sales-target?type=dealer" class="{{ $typeButton['dealer'] }}">Target by Dealer</a>
        <a href="{{ env('APP_HOME_URL') }}/sales-target?type=promotor" class="{{ $typeButton['promotor'] }}">Target by Promotor</a>
    </div>

    <div>
        {!! Form::select('targetDate', $listMonth['months'], $date, ['class' => 'targetDate form-control', 'data-type' => $type]) !!}
    </div>

    <table id="userTable" class="display initTable">
        <thead>
            <tr>
                <th data-width="5%">No.</th>
                @if($type === 'promotor')
                    <th data-width="30%">Promotor</th>
                    <th data-width="20%">Dealer</th>
                    <th data-width="15%">Branch</th>
                @else
                    <th data-width="30%">Dealer</th>
                    <th data-width="20%">Branch</th>
                    <th data-width="15%">Total Promotor</th>
                @endif
                <th data-width="15%">Total</th>
                <th data-width="15%">Options</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataTarget as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    
                    @if($type === 'promotor')
                    
                        <td>{{ $item['promotor'] }}</td>
                        <td>{{ $item['dealer'] }}</td>
                        <td>{{ $item['branch'] }}</td>
                    
                    @else
                    
                        <td>{{ $item['dealer'] }}</td>
                        <td>{{ $item['branch'] }}</td>
                        <td>{{ $item['totalPromotor'] }}</td>
                    
                    @endif
                    
                    <td>{{ number_format($item['total']) }}</td>

                    @if($item['hasTarget'])
                        <td>
                            <a href="{{ env('APP_HOME_URL') }}/sales-target/edit?ID={{ $item['ID'] }}&date={{ $date }}&type={{ $type }}" class="btn btn-green">Edit</a>
                        </td>
                    @else
                        <td>
                            <a href="{{ env('APP_HOME_URL') }}/sales-target/create?promotor_ID={{ $item['promotor_ID'] }}&date={{ $date }}" class="btn btn-green">Set</a>
                        </td>
                    @endif
                    
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

@include('layouts.footer');
