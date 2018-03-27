@include('layouts.header', ['title' => 'Competitor Brands - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Competitor Brands</h1>

    @if(Session::has('brand-created'))
        <div class="note success">Brand has been created</div>
    @endif

    @if(Session::has('brand-deleted'))
        <div class="note error">Brand has been removed</div>
    @endif

    <div>
        <a href="{{ env('APP_HOME_URL') }}/competitor-brand/create" class="btn btn-green">Create</a>
    </div>

    <table id="competitorBrandTable" class="display initTable">
        <thead>
            <tr>
                <th data-width="5%">No</th>
                <th data-width="70%">Name</th>
                <th data-width="25%">Options</th>
            </tr>
        </thead>
        <tbody>
            @foreach($brands as $key => $brand)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $brand->name }}</td>
                    <td>
                        <a href="{{ env('APP_HOME_URL') }}/competitor-brand/edit?ID={{ $brand->ID }}" class="btn btn-green">Edit</a>
                        <a href="{{ env('APP_HOME_URL') }}/competitor-brand/remove?ID={{ $brand->ID }}" class="btn btn-red">Remove</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

@include('layouts.footer');
